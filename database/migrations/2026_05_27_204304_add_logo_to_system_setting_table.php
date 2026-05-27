<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Thêm cột logo_path để lưu đường dẫn logo phòng khám.
     */
    public function up(): void
    {
        Schema::table('system_setting', function (Blueprint $table) {
            $table->string('logo_path', 500)->nullable()->after('additional_info')
                  ->comment('Đường dẫn logo phòng khám (lưu trong storage/app/public/clinic_logos/)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_setting', function (Blueprint $table) {
            $table->dropColumn('logo_path');
        });
    }
};
