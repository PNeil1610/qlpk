<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\KeycloakAdminService;
use Illuminate\Database\QueryException;

class UserController extends Controller
{
    private KeycloakAdminService $keycloak;

    public function __construct(KeycloakAdminService $keycloak)
    {
        $this->keycloak = $keycloak;
    }

    /**
     * Danh sách users từ DB local (liên kết với Keycloak qua keycloak_sub).
     */
    public function index()
    {
        $users = User::orderBy('id', 'desc')->paginate(15);

        // Lấy thêm thông tin roles từ Keycloak nếu có thể
        $keycloakUsers = [];
        try {
            $kcUsers = $this->keycloak->getUsers(0, 100);
            foreach ($kcUsers as $ku) {
                $keycloakUsers[$ku['id']] = $ku;
            }
        } catch (\Exception $e) {
            // Keycloak chưa chạy hoặc lỗi kết nối → bỏ qua
        }

        return view('users.index', compact('users', 'keycloakUsers'));
    }

    /**
     * Chi tiết user: kết hợp dữ liệu DB local + Keycloak.
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        $keycloakUser  = null;
        $keycloakRoles = [];

        if ($user->keycloak_sub) {
            try {
                $keycloakUser  = $this->keycloak->getUser($user->keycloak_sub);
                $keycloakRoles = $this->keycloak->getUserRoles($user->keycloak_sub);
            } catch (\Exception $e) {
                // Keycloak offline
            }
        }

        return view('users.show', compact('user', 'keycloakUser', 'keycloakRoles'));
    }

    /**
     * Khóa / Mở khóa tài khoản thông qua Keycloak (enabled flag).
     * Đồng thời cập nhật cột `status` trong DB local để đồng bộ.
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không thể thay đổi trạng thái tài khoản của chính mình!'
            ], 403);
        }

        $newStatus  = $user->status == 1 ? 0 : 1;
        $enabled    = $newStatus == 1;
        $statusText = $enabled ? 'mở khóa' : 'khóa';

        // Cập nhật Keycloak nếu user đã liên kết
        $keycloakSuccess = true;
        if ($user->keycloak_sub) {
            try {
                $keycloakSuccess = $this->keycloak->setUserEnabled($user->keycloak_sub, $enabled);
            } catch (\Exception $e) {
                $keycloakSuccess = false;
            }
        }

        // Cập nhật DB local dù Keycloak có lỗi hay không
        $user->status = $newStatus;
        $user->save();

        $displayName = $user->full_name ?? $user->username ?? $user->keycloak_sub;
        $message = "Tài khoản {$displayName} đã được {$statusText}.";

        if (!$keycloakSuccess && $user->keycloak_sub) {
            $message .= ' (Lưu ý: Không thể đồng bộ với Keycloak, vui lòng kiểm tra kết nối.)';
        }

        return response()->json([
            'success'    => true,
            'message'    => $message,
            'new_status' => $newStatus
        ]);
    }

    /**
     * Admin đặt lại mật khẩu tạm thời cho user qua Keycloak Admin API.
     */
    public function resetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'new_temp_password' => 'required|string|min:6',
        ], [
            'new_temp_password.required' => 'Mật khẩu tạm không được để trống.',
            'new_temp_password.min'      => 'Mật khẩu tạm phải có ít nhất 6 ký tự.',
        ]);

        if (!$user->keycloak_sub) {
            return redirect()->route('users.show', $user->id)
                ->with('error', 'Tài khoản này chưa được liên kết với Keycloak. Người dùng cần đăng nhập ít nhất một lần để liên kết.');
        }

        try {
            $success = $this->keycloak->resetPassword(
                $user->keycloak_sub,
                $request->new_temp_password,
                true // temporary = true → yêu cầu đổi mật khẩu khi đăng nhập
            );

            if ($success) {
                $displayName = $user->full_name ?? $user->username;
                return redirect()->route('users.show', $user->id)
                    ->with('success', "Đã đặt lại mật khẩu tạm cho <strong>{$displayName}</strong>.")
                    ->with('temp_password_info', "Mật khẩu tạm mới: <strong>{$request->new_temp_password}</strong>. Người dùng sẽ được yêu cầu đổi mật khẩu khi đăng nhập lần sau.");
            }

            return redirect()->route('users.show', $user->id)
                ->with('error', 'Không thể đặt lại mật khẩu. Vui lòng kiểm tra kết nối Keycloak.');

        } catch (\Exception $e) {
            return redirect()->route('users.show', $user->id)
                ->with('error', 'Lỗi kết nối Keycloak: ' . $e->getMessage());
        }
    }

    /**
     * AJAX tìm kiếm users (tìm trong DB local).
     */
    public function ajaxSearch(Request $request)
    {
        $keyword    = $request->keyword;
        $roleFilter = $request->role;

        $query = User::query()
            ->when($keyword, function ($q) use ($keyword) {
                $q->where(function ($qq) use ($keyword) {
                    $qq->where('username', 'like', "%{$keyword}%")
                       ->orWhere('full_name', 'like', "%{$keyword}%")
                       ->orWhere('email', 'like', "%{$keyword}%")
                       ->orWhere('phone_number', 'like', "%{$keyword}%");
                });
            })
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($user) {
                return [
                    'id'           => $user->id,
                    'username'     => $user->username,
                    'full_name'    => $user->full_name,
                    'email'        => $user->email,
                    'phone_number' => $user->phone_number,
                    'status'       => $user->status,
                    'keycloak_sub' => $user->keycloak_sub,
                ];
            });

        return response()->json($query);
    }

    /**
     * Xóa user local (không xóa khỏi Keycloak, chỉ xóa mapping DB).
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Bạn không thể xóa tài khoản của chính mình!');
        }

        $displayName = $user->full_name ?? $user->username ?? "ID #{$id}";

        try {
            $user->delete();
            return redirect()->route('users.index')
                ->with('success', "Đã xóa liên kết tài khoản <strong>{$displayName}</strong> khỏi hệ thống. (Tài khoản Keycloak vẫn còn, cần xóa thủ công trên Keycloak Console nếu cần.)");
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return redirect()->route('users.index')
                    ->with('error', "Không thể xóa tài khoản <strong>{$displayName}</strong> vì đang được liên kết với dữ liệu khác trong hệ thống.");
            }
            return redirect()->route('users.index')
                ->with('error', 'Đã xảy ra lỗi khi xóa tài khoản. Vui lòng thử lại.');
        }
    }
}
