<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * เพิ่มช่องสีและยี่ห้อของสิ่งของ เพื่อให้หน้าค้นหากรองได้
     */
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->string('color')->nullable()->after('description');
            $table->string('brand')->nullable()->after('color');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['color', 'brand']);
        });
    }
};
