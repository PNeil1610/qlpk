<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Room;
use App\Models\Staff;
use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * SingleDoctorClinicSeeder — Phiên bản Keycloak
 *
 * Seed này tạo dữ liệu nghiệp vụ (Phòng, Khoa, Nhân sự).
 * KHÔNG tạo password vì Keycloak quản lý xác thực.
 * KHÔNG gán DB roles vì roles đến từ Keycloak JWT token.
 *
 * Để đăng nhập với tài khoản bacsi_admin:
 *   1. Vào Keycloak Admin Console (http://localhost:8180/admin)
 *   2. Realm: qlpk → Users → Create user
 *   3. Username: bacsi_admin, Email: bacsi.admin@gmail.com
 *   4. Assign realm roles: ADMIN, DOCTOR
 *   5. Set password: 123456 (temporary = false)
 *   6. Login vào app → hệ thống sẽ tự liên kết keycloak_sub
 */
class SingleDoctorClinicSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo Khoa duy nhất
        $department = Department::firstOrCreate(
            ['dept_code' => 'KLS'],
            ['dept_name' => 'Khoa Lâm Sàng', 'status' => 1]
        );

        // 2. Tạo Phòng khám duy nhất
        $singleRoom = Room::firstOrCreate(
            ['room_code' => 'PK-SINGLE'],
            [
                'room_name'     => 'Phòng Khám Duy Nhất',
                'room_type'     => Room::TYPE_EXAMINATION,
                'department_id' => $department->id,
                'status'        => Room::STATUS_AVAILABLE,
            ]
        );

        // 3. Tạo placeholder User cho bacsi_admin (sẽ được liên kết với Keycloak khi login lần đầu)
        // keycloak_sub = null vì chưa biết UUID từ Keycloak
        $doctorUser = User::firstOrCreate(
            ['username' => 'bacsi_admin'],
            [
                'keycloak_sub' => null, // Sẽ được cập nhật tự động khi login qua Keycloak
                'full_name'    => 'BS. Nguyễn Văn A',
                'email'        => 'bacsi.admin@gmail.com',
                'phone_number' => '0901234567',
                'status'       => 1,
                // KHÔNG cần password_hash — Keycloak quản lý mật khẩu
            ]
        );

        // 4. Tạo hồ sơ Staff liên kết với User placeholder
        Staff::firstOrCreate(
            ['user_id' => $doctorUser->id],
            [
                'staff_type'    => 'Doctor',
                'staff_code'    => 'BS001',
                'full_name'     => $doctorUser->full_name,
                'email'         => $doctorUser->email,
                'phone_number'  => $doctorUser->phone_number,
                'room_id'       => $singleRoom->id,
                'department_id' => $department->id,
                'specialty'     => 'Đa Khoa',
                'status'        => 1,
            ]
        );

        $this->command->info('✅ SingleDoctorClinicSeeder hoàn tất!');
        $this->command->newLine();
        $this->command->line('📌 Bước tiếp theo để đăng nhập:');
        $this->command->line('   1. Truy cập Keycloak Admin: http://localhost:8180/admin (admin/admin)');
        $this->command->line('   2. Chọn Realm: qlpk → Users → Add user');
        $this->command->line('   3. Username: bacsi_admin | Email: bacsi.admin@gmail.com');
        $this->command->line('   4. Credentials → Set Password: 123456 (Temporary: OFF)');
        $this->command->line('   5. Role mappings → Assign: ADMIN, DOCTOR');
        $this->command->line('   6. Đăng nhập app → hệ thống tự liên kết keycloak_sub ✓');
    }
}
