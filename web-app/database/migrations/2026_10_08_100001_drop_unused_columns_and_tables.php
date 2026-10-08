<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ลบคอลัมน์และตารางที่ไม่มีโค้ดใช้แล้ว
     * - items: ระบบอนุมัติโพสต์ถูกเอาออก, place_point/color/brand ไม่มีฟอร์มไหนบันทึก
     * - users: ไม่ใช้ยืนยันอีเมลและ 2FA แล้ว
     * - passkeys, password_reset_tokens: ไม่ใช้ passkey และการรีเซ็ตรหัสผ่านทางอีเมลแล้ว
     */
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn([
                'place_point',
                'color',
                'brand',
                'approval_status',
                'reject_reason',
                'approved_by',
                'approved_at',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'email_verified_at',
                'two_factor_secret',
                'two_factor_recovery_codes',
                'two_factor_confirmed_at',
            ]);
        });

        Schema::dropIfExists('passkeys');
        Schema::dropIfExists('password_reset_tokens');
    }

    public function down(): void
    {
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('passkeys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('credential_id')->unique();
            $table->json('credential');
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('email_verified_at')->nullable();
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();
        });

        Schema::table('items', function (Blueprint $table) {
            $table->string('place_point')->nullable();
            $table->string('color')->nullable();
            $table->string('brand')->nullable();
            $table->string('approval_status')->default('รออนุมัติ');
            $table->string('reject_reason')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
        });
    }
};
