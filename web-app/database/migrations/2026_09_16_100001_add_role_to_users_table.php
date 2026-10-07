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
        Schema::table('users', function (Blueprint $table) {
            // role ของบัญชีที่ล็อกอิน: user / agency / admin
            $table->string('role')->default('user');
            // ผูกบัญชีล็อกอินเข้ากับข้อมูลคนในตาราง finder_users
            $table->unsignedBigInteger('finder_user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
            $table->dropColumn('finder_user_id');
        });
    }
};
