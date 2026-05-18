<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Thêm cột prescribed_by vào bảng prescription để lưu bác sĩ kê đơn.
     */
    public function up(): void
    {
        Schema::table('prescription', function (Blueprint $table) {
            $table->unsignedBigInteger('prescribed_by')->nullable()->after('consultation_id');
            $table->foreign('prescribed_by')->references('id')->on('staff')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescription', function (Blueprint $table) {
            $table->dropForeign(['prescribed_by']);
            $table->dropColumn('prescribed_by');
        });
    }
};
