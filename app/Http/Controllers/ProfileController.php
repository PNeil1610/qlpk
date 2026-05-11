<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        ], [
            'email.email' => 'Địa chỉ email không hợp lệ.',
            'email.max' => 'Email không được vượt quá 150 ký tự.',
            'phone_number.max' => 'Số điện thoại không được vượt quá 15 ký tự.'
        ]);

        $user->email = $request->email;
        $user->phone_number = $request->phone_number;
        
        $user->save();

        // Đồng bộ cập nhật thông tin qua bảng Patient (nếu User này là bệnh nhân)
        if ($user->patient) {
            $user->patient->email = $request->email;
            $user->patient->phone_number = $request->phone_number;
            $user->patient->save();
        }

        return back()->with('success', 'Thông tin cá nhân đã được cập nhật thành công!');
    }
}