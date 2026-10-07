<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\FinderUser;

class LoginUserSeeder extends Seeder
{
    public function run(): void
    {
        // สร้างบัญชีล็อกอินให้ทุกคนใน finder_users โดยใช้อีเมลเดียวกัน รหัสผ่านเริ่มต้นคือ password
        $finderUsers = FinderUser::all();

        foreach ($finderUsers as $finderUser) {
            // ถ้ามีบัญชีอีเมลนี้อยู่แล้วไม่ต้องสร้างซ้ำ
            if (User::where('email', $finderUser->email)->first() !== null) {
                continue;
            }

            $user = new User();
            $user->name = $finderUser->fullname;
            $user->email = $finderUser->email;
            $user->password = 'password';
            $user->role = $finderUser->role;
            $user->finder_user_id = $finderUser->id;
            $user->save();
        }
    }
}
