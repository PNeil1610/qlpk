<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Patient;

class PatientDataSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('username', 'benhnhan_demo')->first();

        if ($user) {
            Patient::create([
                'user_id' => $user->id,
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