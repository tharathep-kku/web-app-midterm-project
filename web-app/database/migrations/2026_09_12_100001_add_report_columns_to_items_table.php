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
        Schema::table('items', function (Blueprint $table) {
            // จุดสถานที่ = จุดที่พบของแบบละเอียด เช่น ชั้น 2 ใต้โต๊ะริมหน้าต่าง
            $table->string('place_point')->nullable();
            // หลักฐานยืนยัน = รูปหลักฐานตอนเก็บของได้ + คำอธิบายหลักฐาน
            $table->string('evidence_url')->nullable();
            $table->text('evidence_note')->nullable();
            // สถานะการอนุมัติของแอดมิน
            $table->string('approval_status')->default('รออนุมัติ');
            $table->string('reject_reason')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('place_point');
            $table->dropColumn('evidence_url');
            $table->dropColumn('evidence_note');
            $table->dropColumn('approval_status');
            $table->dropColumn('reject_reason');
            $table->dropColumn('approved_by');
            $table->dropColumn('approved_at');
        });
    }
};
