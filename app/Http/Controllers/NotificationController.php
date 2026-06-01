<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemNotification;
use App\Models\NotificationRead;
use App\Models\User;
use App\Models\Role;

class NotificationController extends Controller
{
    // Role labels map
    private const ROLE_LABELS = [
        'ADMIN'        => 'Quản trị viên',
        'DOCTOR'       => 'Bác sĩ',
        'CASHIER'      => 'Thu ngân',
        'RECEPTIONIST' => 'Lễ tân',
        'NURSE'        => 'Y tá',
    ];

    /**
     * 📋 Danh sách thông báo người dùng được nhận
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $filter = $request->get('filter', 'all'); // all | unread | sent

        $query = SystemNotification::with(['sender.roles', 'targetUser', 'reads'])
            ->orderBy('notified_at', 'desc');

        if ($filter === 'sent') {
            // Thông báo tôi đã gửi (bất kể còn hiệu lực không)
            $query->where('sender_id', $user->id);
        } else {
            // Thông báo tôi nhận được
            $query->visibleTo($user);

            if ($filter === 'unread') {
                $query->whereDoesntHave('reads', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            }
        }

        $notifications = $query->paginate(15)->withQueryString();

        // Đếm chưa đọc để hiển thị trên header
        $unreadCount = SystemNotification::visibleTo($user)
            ->whereDoesntHave('reads', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->count();

        return view('notifications.index', compact('notifications', 'filter', 'unreadCount'));
    }

    /**
     * ➕ Form soạn thông báo
     */
    public function create()
    {
        $user = auth()->user();
        $myRoleCode = $user->roles->first()?->role_code ?? '';
        $isAdmin = $user->hasRole('ADMIN');

        // Danh sách role để gửi theo nhóm
        $roles = Role::where('status', 1)
            ->whereNotIn('role_code', ['PATIENT'])
            ->get();

        // Nếu không phải admin, chỉ cho phép gửi theo role của mình
        $allowedRoles = $isAdmin
            ? $roles
            : $roles->where('role_code', $myRoleCode);

        // Danh sách nhân viên để gửi cá nhân (trừ bản thân, trừ PATIENT)
        $staffUsers = User::with('roles')
            ->where('users.id', '!=', $user->id)
            ->whereHas('roles', function ($q) {
                $q->whereNotIn('role_code', ['PATIENT'])
                  ->where('roles.status', 1);
            })
            ->where('users.status', 1)
            ->orderBy('users.full_name')
            ->get()
            ->map(function ($u) {
                return [
                    'id'        => $u->id,
                    'full_name' => $u->full_name,
                    'role_name' => $u->roles->first()?->role_name ?? '---',
                    'role_code' => $u->roles->first()?->role_code ?? '',
                ];
            });

        return view('notifications.create', compact('isAdmin', 'allowedRoles', 'staffUsers', 'myRoleCode'));
    }

    /**
     * 💾 Lưu thông báo
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('ADMIN');
        $myRoleCode = $user->roles->first()?->role_code ?? '';

        $request->validate([
            'title'       => 'required|string|max:255',
            'content'     => 'required|string',
            'target_type' => 'required|in:all,role,user',
            'target_role' => 'required_if:target_type,role|nullable|string',
            'target_user_id' => 'required_if:target_type,user|nullable|exists:users,id',
            'expires_at'  => 'nullable|date|after:now',
        ], [
            'title.required'       => 'Vui lòng nhập tiêu đề thông báo.',
            'content.required'     => 'Vui lòng nhập nội dung thông báo.',
            'target_type.required' => 'Vui lòng chọn đối tượng nhận.',
            'target_role.required_if' => 'Vui lòng chọn nhóm nhận.',
            'target_user_id.required_if' => 'Vui lòng chọn người nhận.',
            'expires_at.after'     => 'Thời gian hết hạn phải sau thời điểm hiện tại.',
        ]);

        // Kiểm tra quyền gửi
        if ($request->target_type === 'all' && !$isAdmin) {
            return back()->withInput()->with('error', 'Chỉ ADMIN mới được gửi thông báo đến tất cả.');
        }

        if ($request->target_type === 'role' && !$isAdmin) {
            // Non-admin chỉ được gửi cho role của mình
            if ($request->target_role !== $myRoleCode) {
                return back()->withInput()->with('error', 'Bạn chỉ được gửi thông báo theo nhóm của mình.');
            }
        }

        SystemNotification::create([
            'sender_id'      => $user->id,
            'target_type'    => $request->target_type,
            'target_role'    => $request->target_type === 'role' ? $request->target_role : null,
            'target_user_id' => $request->target_type === 'user' ? $request->target_user_id : null,
            'title'          => $request->title,
            'content'        => $request->content,
            'notified_at'    => now(),
            'expires_at'     => $request->expires_at ?: null,
            'status'         => 1,
        ]);

        return redirect()->route('notifications.index')
            ->with('success', 'Đã gửi thông báo thành công!');
    }

    /**
     * 🔍 Chi tiết thông báo + đánh dấu đã đọc
     */
    public function show($id)
    {
        $user = auth()->user();
        $notification = SystemNotification::with(['sender.roles', 'targetUser'])->findOrFail($id);

        // Kiểm tra user có quyền xem không
        $canView = (int)$notification->sender_id === (int)$user->id
            || $this->isVisibleToUser($notification, $user);

        if (!$canView) {
            abort(403, 'Bạn không có quyền xem thông báo này.');
        }

        // Đánh dấu đã đọc nếu chưa
        if ((int)$notification->sender_id !== (int)$user->id) {
            NotificationRead::firstOrCreate([
                'notification_id' => $notification->id,
                'user_id'         => $user->id,
            ], [
                'read_at' => now(),
            ]);
        }

        return view('notifications.show', compact('notification'));
    }

    /**
     * 🗑️ Thu hồi thông báo (set status = 0)
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $notification = SystemNotification::findOrFail($id);

        // Chỉ người gửi hoặc Admin mới được thu hồi
        if ((int)$notification->sender_id !== (int)$user->id && !$user->hasRole('ADMIN')) {
            return back()->with('error', 'Bạn không có quyền thu hồi thông báo này.');
        }

        $notification->update(['status' => 0]);

        return redirect()->route('notifications.index')
            ->with('success', 'Đã thu hồi thông báo.');
    }

    /**
     * 🔢 AJAX: Đếm số thông báo chưa đọc
     */
    public function ajaxUnreadCount()
    {
        $user = auth()->user();

        // PATIENT không có thông báo nội bộ
        if ($user->hasRole('PATIENT')) {
            return response()->json(['count' => 0]);
        }

        $count = SystemNotification::visibleTo($user)
            ->whereDoesntHave('reads', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->count();

        return response()->json(['count' => $count]);
    }

    // ========================
    // Private helpers
    // ========================

    private function isVisibleToUser(SystemNotification $n, User $user): bool
    {
        if ($n->status != 1) return false;
        if ($n->expires_at && $n->expires_at->isPast()) return false;

        $userRoles = $user->roles->pluck('role_code')->toArray();

        return match ($n->target_type) {
            'all'  => true,
            'role' => in_array($n->target_role, $userRoles),
            'user' => (int)$n->target_user_id === (int)$user->id,
            default => false,
        };
    }
}
