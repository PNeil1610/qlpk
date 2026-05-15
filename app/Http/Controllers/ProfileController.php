<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        return view('auth.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'email' => ['nullable', 'email', 'max:150'], 
            'phone_number' => ['nullable', 'string', 'max:15'], 
            'address' => ['nullable', 'string', 'max:255'],
            'national_id' => ['nullable', 'string', 'max:50', \Illuminate\Validation\Rule::unique('users')->ignore($user->id)],
        ], [
            'email.email' => 'Địa chỉ email không hợp lệ.',
            'email.max' => 'Email không được vượt quá 150 ký tự.',
            'phone_number.max' => 'Số điện thoại không được vượt quá 15 ký tự.',
            'national_id.unique' => 'Số CCCD/CMND đã tồn tại trên hệ thống.',
        ]);

        $user->email = $request->email;
        $user->phone_number = $request->phone_number;
        $user->address = $request->address;
        $user->national_id = $request->national_id;
        
        $user->save();

        // Đồng bộ cập nhật thông tin qua bảng Patient (nếu User này là bệnh nhân)
        if ($user->patient) {
            $user->patient->email = $request->email;
            $user->patient->phone_number = $request->phone_number;
            $user->patient->address = $request->address;
            $user->patient->national_id = $request->national_id;
            $user->patient->save();
        }

        return back()->with('success', 'Thông tin cá nhân đã được cập nhật thành công!');
    }

    public function changePassword()
    {
        $user = Auth::user();
        return view('auth.change_password', compact('user'));
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'new_password.required'     => 'Vui lòng nhập mật khẩu mới.',
            'new_password.min'          => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'new_password.confirmed'    => 'Xác nhận mật khẩu không khớp.',
        ]);

        // Kiểm tra mật khẩu hiện tại
        if (!Hash::check($request->current_password, $user->password_hash)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.'])->withInput();
        }

        $user->password_hash = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('profile')
            ->with('success', 'Đổi mật khẩu thành công! Hãy đăng nhập lại nếu cần.');
    }
}