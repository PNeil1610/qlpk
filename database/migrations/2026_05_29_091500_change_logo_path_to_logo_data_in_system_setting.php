<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Đổi cột logo_path (VARCHAR) sang logo_data (LONGTEXT - lưu Base64).
     */
    public function up(): void
    {
        Schema::table('system_setting', function (Blueprint $table) {
            // Thêm cột logo_data kiểu LONGTEXT để chứa ảnh Base64
            $table->longText('logo_data')->nullable()->after('additional_info')
                  ->comment('Dữ liệu logo phòng khám dạng Base64 (data:image/...;base64,...)');
        });

        // Xóa cột logo_path cũ (không còn dùng nữa)
        Schema::table('system_setting', function (Blueprint $table) {
            $table->dropColumn('logo_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_setting', function (Blueprint $table) {
            $table->string('logo_path', 500)->nullable()->after('additional_info')
                  ->comment('Đường dẫn logo phòng khám');
        });

        Schema::table('system_setting', function (Blueprint $table) {
            $table->dropColumn('logo_data');
        });
    }
};
