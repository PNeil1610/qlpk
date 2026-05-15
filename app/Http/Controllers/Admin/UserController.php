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

    public function create()
    {
        $roles = Role::where('status', 1)->get();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username'     => 'required|string|max:100|unique:users,username',
            'full_name'    => 'required|string|max:150',
            'email'        => 'nullable|email|max:150|unique:users,email',
            'phone_number' => 'nullable|string|max:15|unique:users,phone_number',
            'address'      => 'nullable|string|max:255',
            'national_id'  => 'nullable|string|max:50|unique:users,national_id',
            'role_id'      => 'required|exists:roles,id',
            'temp_password'=> 'required|string|min:6',
        ], [
            'username.required'     => 'Tên đăng nhập không được để trống.',
            'username.unique'       => 'Tên đăng nhập đã tồn tại.',
            'full_name.required'    => 'Họ và tên không được để trống.',
            'email.email'           => 'Địa chỉ email không hợp lệ.',
            'email.unique'          => 'Email đã được sử dụng bởi tài khoản khác.',
            'phone_number.unique'   => 'Số điện thoại đã được sử dụng bởi tài khoản khác.',
            'national_id.unique'    => 'Số CCCD/CMND đã tồn tại trên hệ thống.',
            'role_id.required'      => 'Vui lòng chọn vai trò cho tài khoản.',
            'role_id.exists'        => 'Vai trò không hợp lệ.',
            'temp_password.required'=> 'Mật khẩu tạm không được để trống.',
            'temp_password.min'     => 'Mật khẩu tạm phải có ít nhất 6 ký tự.',
        ]);

        return DB::transaction(function () use ($request) {
            $user = User::create([
                'username'      => $request->username,
                'full_name'     => $request->full_name,
                'email'         => $request->email,
                'phone_number'  => $request->phone_number,
                'address'       => $request->address,
                'national_id'   => $request->national_id,
                'password_hash' => Hash::make($request->temp_password),
                'status'        => 1,
            ]);

            $user->roles()->attach($request->role_id, ['status' => 1]);

            return redirect()
                ->route('users.index')
                ->with('success', "Tạo tài khoản <strong>{$user->username}</strong> thành công!")
                ->with('temp_password_info', "Mật khẩu tạm thời: <strong>{$request->temp_password}</strong>. Hãy thông báo cho người dùng để họ đổi mật khẩu ngay.");
        });
    }

    public function show($id)
    {
        $user = User::with('roles')->findOrFail($id);
        return view('users.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::with('roles')->findOrFail($id);
        $roles = Role::where('status', 1)->get();
        $userRoleId = $user->roles->first()?->id;
        return view('users.edit', compact('user', 'roles', 'userRoleId'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'full_name'    => 'required|string|max:150',
            'email'        => ['nullable', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'phone_number' => ['nullable', 'string', 'max:15', Rule::unique('users', 'phone_number')->ignore($user->id)],
            'address'      => 'nullable|string|max:255',
            'national_id'  => ['nullable', 'string', 'max:50', Rule::unique('users', 'national_id')->ignore($user->id)],
            'status'       => 'required|in:0,1',
            'role_id'      => 'required|exists:roles,id',
        ], [
            'full_name.required'  => 'Họ và tên không được để trống.',
            'email.email'         => 'Địa chỉ email không hợp lệ.',
            'email.unique'        => 'Email đã được sử dụng bởi tài khoản khác.',
            'phone_number.unique' => 'Số điện thoại đã được sử dụng bởi tài khoản khác.',
            'national_id.unique'  => 'Số CCCD/CMND đã tồn tại trên hệ thống.',
            'status.required'     => 'Vui lòng chọn trạng thái tài khoản.',
            'role_id.required'    => 'Vui lòng chọn vai trò.',
        ]);

        $user->update([
            'full_name'    => $request->full_name,
            'email'        => $request->email,
            'phone_number' => $request->phone_number,
            'address'      => $request->address,
            'national_id'  => $request->national_id,
            'status'       => $request->status,
        ]);

        // Cập nhật role (sync: xóa cũ, thêm mới)
        $user->roles()->detach();
        $user->roles()->attach($request->role_id, ['status' => 1]);

        return redirect()
            ->route('users.index')
            ->with('success', "Tài khoản <strong>{$user->username}</strong> đã được cập nhật thành công!");
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
