<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
        });

        // ย้ายเบอร์โทรจาก finder_users มาที่ users ตามที่ finder_user_id ผูกไว้
        // สร้างตารางเทียบ finder_users.id (เก่า) -> users.id (ใหม่) ไว้ก่อน
        $map = [];
        $users = DB::table('users')->whereNotNull('finder_user_id')->get();

        foreach ($users as $user) {
            $finderUser = DB::table('finder_users')->find($user->finder_user_id);

            if ($finderUser === null) {
                continue;
            }

            DB::table('users')->where('id', $user->id)->update([
                'phone' => $finderUser->phone,
            ]);

            $map[$finderUser->id] = $user->id;
        }

        // แก้ items.user_id ที่เคยชี้ไป finder_users.id ให้ชี้ไป users.id แทน
        // ต้องอัปเดตทีละแถวโดยอิง items.id (ไม่ใช่ user_id เดิม) เพราะถ้า match ด้วย
        // user_id เดิมแล้วช่วงเลข id ของสองตารางทับกัน แถวที่เพิ่งอัปเดตไปจะโดน
        // จับคู่ซ้ำในรอบถัดไปโดยไม่ตั้งใจ (ไอดีเก่า 1-10 บวก 1 ชนกับไอดีใหม่ 2-11)
        $items = DB::table('items')->whereNotNull('user_id')->get(['id', 'user_id']);

        foreach ($items as $item) {
            if (isset($map[$item->user_id])) {
                DB::table('items')->where('id', $item->id)->update([
                    'user_id' => $map[$item->user_id],
                ]);
            }
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('finder_user_id');
        });

        Schema::dropIfExists('finder_users');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('finder_users', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('fullname');
            $table->string('email');
            $table->string('phone');
            $table->string('role');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('finder_user_id')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone');
        });
    }
};
