<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Patient;

class PatientDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tìm ông tài khoản benhnhan_demo
        $user = User::where('username', 'benhnhan_demo')->first();

        if ($user) {
            // 2. Tạo một hồ sơ bệnh án cho ông này
            Patient::create([
                'user_id' => $user->id, // Nối dây ở đây!
                'patient_code' => 'BN' . date('Y') . '001',
                'full_name' => $user->full_name,
                'gender' => 'Male',
                'phone_number' => '0987654321',
                'address' => 'Cần Thơ, Việt Nam',
                'status' => 1
            ]);
        }
    }
}