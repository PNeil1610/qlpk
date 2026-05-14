<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MyRecordController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 📋 Xem hồ sơ bệnh án của bệnh nhân đang đăng nhập
     */
    public function index()
    {
        $user = Auth::user();

        $myPatientRecord = $user->patient;

        if (!$myPatientRecord) {
            return redirect()->back()->with('error', 'Hệ thống chưa ghi nhận hồ sơ bệnh án của bạn.');
        }

        return view('patients.my_record', compact('myPatientRecord'));
    }
}
