<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'สหรัฐ งามเริ่ด', 'email' => 'sarath.n@kku.ac.th', 'phone' => '081-234-5671', 'role' => 'user'],
            ['name' => 'ปิยะดา เพชรมณี', 'email' => 'piyada.p@kkumail.com', 'phone' => '081-234-5672', 'role' => 'user'],
            ['name' => 'ธนกร ศรีสุข', 'email' => 'thanakorn.s@kku.ac.th', 'phone' => '081-234-5673', 'role' => 'user'],
            ['name' => 'กัลยา วงศ์ษา', 'email' => 'kanlaya.w@kkumail.com', 'phone' => '081-234-5674', 'role' => 'user'],
            ['name' => 'อภิสิทธิ์ บุญมา', 'email' => 'apisit.b@kku.ac.th', 'phone' => '081-234-5675', 'role' => 'user'],
            ['name' => 'นภัสสร ทองดี', 'email' => 'napatsorn.t@kkumail.com', 'phone' => '081-234-5676', 'role' => 'user'],
            ['name' => 'วีรพงษ์ แก้วมณี', 'email' => 'weerapong.k@kku.ac.th', 'phone' => '081-234-5677', 'role' => 'user'],
            ['name' => 'สุดารัตน์ จันทร์เพ็ญ', 'email' => 'sudarat.j@kkumail.com', 'phone' => '081-234-5678', 'role' => 'user'],
            ['name' => 'ณัฐพล เกษมสุข', 'email' => 'natthapon.k@kku.ac.th', 'phone' => '081-234-5679', 'role' => 'user'],
            ['name' => 'พิมพ์ชนก รุ่งเรือง', 'email' => 'pimchanok.r@kku.ac.th', 'phone' => '081-234-5680', 'role' => 'admin'],
        ];

        foreach ($users as $user) {
            // ถ้ามีบัญชีอีเมลนี้อยู่แล้วไม่ต้องสร้างซ้ำ
            if (User::where('email', $user['email'])->first() !== null) {
                continue;
            }

            User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'phone' => $user['phone'],
                'role' => $user['role'],
                'password' => 'password',
            ]);
        }
    }
}
