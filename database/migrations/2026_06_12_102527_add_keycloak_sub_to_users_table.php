<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Tích hợp Keycloak OIDC
 *
 * KHÔNG XÓA bảng users, roles, users_roles — chỉ THÊM cột mới.
 * Bảng users vẫn là bridge giữa Keycloak identity và dữ liệu nghiệp vụ
 * (staff.user_id, patient.user_id vẫn trỏ vào users.id).
 *
 * Sau migration này:
 * - users.keycloak_sub (VARCHAR 36, UUID): định danh Keycloak chính
 * - users.password_hash: deprecated (nullable), Keycloak quản lý
 * - users.username: giữ lại để tương thích, Keycloak là nguồn chính
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Thêm cột keycloak_sub vào bảng users
        Schema::table('users', function (Blueprint $table) {
            // Thêm sau cột id
            $table->string('keycloak_sub', 36)->nullable()->unique()->after('id')
                  ->comment('Keycloak Subject ID (UUID) - định danh OIDC chính');

            // Cho phép password_hash nullable vì Keycloak quản lý password
            $table->string('password_hash', 255)->nullable()->change();

            // Cho phép username nullable (Keycloak là nguồn username)
            $table->string('username', 255)->nullable()->change();
        });

        // 2. Thêm index để tra cứu nhanh theo keycloak_sub
        // (đã có unique nên index tự động tạo)
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['keycloak_sub']);
            $table->dropColumn('keycloak_sub');

            // Revert về NOT NULL
            $table->string('password_hash', 255)->nullable(false)->change();
            $table->string('username', 255)->nullable(false)->change();
        });
    }
};
