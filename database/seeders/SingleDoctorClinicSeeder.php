<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Room;
use App\Models\Staff;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SingleDoctorClinicSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Đảm bảo tồn tại các vai trò cốt lõi
        $adminRole  = Role::firstOrCreate(['role_code' => 'ADMIN'], ['role_name' => 'Quản trị viên']);
        $doctorRole = Role::firstOrCreate(['role_code' => 'DOCTOR'], ['role_name' => 'Bác sĩ']);

        // Gán các quyền quản trị cơ bản (nếu hệ thống có dùng bảng roles_permission)
        if (Schema::hasTable('roles_permission')) {
            DB::table('roles_permission')->updateOrInsert(
                ['role_id' => $adminRole->id, 'permission' => 'patient:write'],
                ['status' => 1]
            );
            DB::table('roles_permission')->updateOrInsert(
                ['role_id' => $adminRole->id, 'permission' => 'patient:delete'],
                ['status' => 1]
            );
        }

        // 2. Khởi tạo 1 Khoa duy nhất
        $department = Department::firstOrCreate(
            ['dept_code' => 'KLS'],
            ['dept_name' => 'Khoa Lâm Sàng', 'status' => 1]
        );

        // 3. Khởi tạo 1 Phòng khám duy nhất
        $singleRoom = Room::firstOrCreate(
            ['room_code' => 'PK-SINGLE'],
            [
                'room_name' => 'Phòng Khám Duy Nhất',
                'room_type' => Room::TYPE_EXAMINATION,
                'department_id' => $department->id,
                'status' => Room::STATUS_AVAILABLE
            ]
        );

        // 4. Tạo tài khoản Bác sĩ kiêm Admin
        $doctorUser = User::firstOrCreate(
            ['username' => 'bacsi_admin'],
            [
                'password_hash' => Hash::make('123456'), // Mật khẩu mẫu
                'full_name' => 'BS. Nguyễn Văn A',
                'email' => 'bacsi.admin@gmail.com',
                'phone_number' => '0901234567',
                'status' => 1
            ]
        );

        // Gán ĐỒNG THỜI cả 2 quyền: ADMIN (để quản lý) và DOCTOR (để khám bệnh)
        $doctorUser->roles()->syncWithoutDetaching([$adminRole->id, $doctorRole->id]);

        // 5. Tạo thông tin Staff liên kết và gán cứng vào phòng khám duy nhất
        Staff::firstOrCreate(
            ['user_id' => $doctorUser->id],
            [
                'staff_type' => 'Doctor',
                'staff_code' => 'BS001',
                'full_name' => $doctorUser->full_name,
                'email' => $doctorUser->email,
                'phone_number' => $doctorUser->phone_number,
                'room_id' => $singleRoom->id, // Chỉ định phòng khám này
                'department_id' => $department->id,
                'specialty' => 'Đa Khoa',
                'status' => 1
            ]
        );
    }
}
