<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('users.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::with('roles')->findOrFail($id);
        return view('users.show', compact('user'));
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không thể thay đổi trạng thái tài khoản của chính mình!'
            ], 403);
        }

        $user->status = $user->status == 1 ? 0 : 1;
        $user->save();

        $statusText = $user->status == 1 ? 'mở khóa' : 'khóa';

        return response()->json([
            'success' => true,
            'message' => "Tài khoản {$user->username} đã được {$statusText}.",
            'new_status' => $user->status
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Không cho xóa chính mình
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Bạn không thể xóa tài khoản của chính mình!');
        }

        // Không cho xóa admin duy nhất
        $isAdmin = $user->roles()->where('role_code', 'ADMIN')->exists();
        if ($isAdmin) {
            $adminCount = User::whereHas('roles', function ($q) {
                $q->where('role_code', 'ADMIN');
            })->count();

            if ($adminCount <= 1) {
                return redirect()->route('users.index')
                    ->with('error', 'Không thể xóa tài khoản ADMIN duy nhất trong hệ thống!');
            }
        }

        $username = $user->username;
        $user->roles()->detach();
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "Đã xóa tài khoản <strong>{$username}</strong> khỏi hệ thống.");
    }

    public function resetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'new_temp_password' => 'required|string|min:6',
        ], [
            'new_temp_password.required' => 'Mật khẩu tạm không được để trống.',
            'new_temp_password.min'      => 'Mật khẩu tạm phải có ít nhất 6 ký tự.',
        ]);

        $user->update([
            'password_hash' => Hash::make($request->new_temp_password),
        ]);

        return redirect()->route('users.show', $user->id)
            ->with('success', "Đã đặt lại mật khẩu tạm cho <strong>{$user->username}</strong>.")
            ->with('temp_password_info', "Mật khẩu tạm mới: <strong>{$request->new_temp_password}</strong>. Hãy thông báo cho người dùng để họ đổi mật khẩu ngay.");
    }

    public function ajaxSearch(Request $request)
    {
        $keyword = $request->keyword;
        $roleFilter = $request->role;

        $users = User::with('roles')
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('username', 'like', "%{$keyword}%")
                      ->orWhere('full_name', 'like', "%{$keyword}%")
                      ->orWhere('email', 'like', "%{$keyword}%")
                      ->orWhere('phone_number', 'like', "%{$keyword}%");
                });
            })
            ->when($roleFilter, function ($query) use ($roleFilter) {
                $query->whereHas('roles', function ($q) use ($roleFilter) {
                    $q->where('role_code', $roleFilter);
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
                    'roles'        => $user->roles->map(fn($r) => [
                        'role_code' => $r->role_code,
                        'role_name' => $r->role_name,
                    ])->values(),
                ];
            });

        return response()->json($users);
    }
}
