<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('room', function (Blueprint $table) {
            $table->dropColumn(['floor', 'capacity']);
        });

        Schema::table('room_detail', function (Blueprint $table) {
            $table->integer('floor')->nullable();
            $table->integer('capacity')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_detail', function (Blueprint $table) {
            $table->dropColumn(['floor', 'capacity']);
        });

        Schema::table('room', function (Blueprint $table) {
            $table->integer('floor')->nullable();
            $table->integer('capacity')->nullable();
        });
    }
};
