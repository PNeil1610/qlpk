<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DemoRBACSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::create(['role_code' => 'ADMIN', 'role_name' => 'Quản trị viên']);
        $patientRole = Role::create(['role_code' => 'PATIENT', 'role_name' => 'Bệnh nhân']);

        DB::table('roles_permission')->insert([
            ['role_id' => $adminRole->id, 'permission' => 'patient:delete', 'status' => 1],
            ['role_id' => $adminRole->id, 'permission' => 'patient:write', 'status' => 1],
        ]);

        DB::table('roles_permission')->insert([
            ['role_id' => $patientRole->id, 'permission' => 'profile:read', 'status' => 1],
        ]);

        $admin = User::create([
            'username' => 'admin_demo',
            'password_hash' => Hash::make('123456'),
            'full_name' => 'Sếp Tổng',
            'status' => 1
        ]);
        $admin->roles()->attach($adminRole->id);

        $patient = User::create([
            'username' => 'benhnhan_demo',
            'password_hash' => Hash::make('123456'),
            'full_name' => 'Nguyễn Văn Bệnh Nhân',
            'status' => 1
        ]);
        $patient->roles()->attach($patientRole->id);
    }
}