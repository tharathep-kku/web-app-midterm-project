<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinderUserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('finder_users')->insert([
            [
                'username' => 'sarath01',
                'fullname' => 'สหรัฐ งามเริ่ด',
                'email' => 'sarath.n@kku.ac.th',
                'phone' => '081-234-5671',
                'role' => 'user',
            ],
            [
                'username' => 'piyada02',
                'fullname' => 'ปิยะดา เพชรมณี',
                'email' => 'piyada.p@kkumail.com',
                'phone' => '081-234-5672',
                'role' => 'user',
            ],
            [
                'username' => 'thanakorn03',
                'fullname' => 'ธนกร ศรีสุข',
                'email' => 'thanakorn.s@kku.ac.th',
                'phone' => '081-234-5673',
                'role' => 'user',
            ],
            [
                'username' => 'kanlaya04',
                'fullname' => 'กัลยา วงศ์ษา',
                'email' => 'kanlaya.w@kkumail.com',
                'phone' => '081-234-5674',
                'role' => 'user',
            ],
            [
                'username' => 'apisit05',
                'fullname' => 'อภิสิทธิ์ บุญมา',
                'email' => 'apisit.b@kku.ac.th',
                'phone' => '081-234-5675',
                'role' => 'user',
            ],
            [
                'username' => 'napatsorn06',
                'fullname' => 'นภัสสร ทองดี',
                'email' => 'napatsorn.t@kkumail.com',
                'phone' => '081-234-5676',
                'role' => 'user',
            ],
            [
                'username' => 'weerapong07',
                'fullname' => 'วีรพงษ์ แก้วมณี',
                'email' => 'weerapong.k@kku.ac.th',
                'phone' => '081-234-5677',
                'role' => 'user',
            ],
            [
                'username' => 'sudarat08',
                'fullname' => 'สุดารัตน์ จันทร์เพ็ญ',
                'email' => 'sudarat.j@kkumail.com',
                'phone' => '081-234-5678',
                'role' => 'user',
            ],
            [
                'username' => 'natthapon09',
                'fullname' => 'ณัฐพล เกษมสุข',
                'email' => 'natthapon.k@kku.ac.th',
                'phone' => '081-234-5679',
                'role' => 'user',
            ],
            [
                'username' => 'pimchanok10',
                'fullname' => 'พิมพ์ชนก รุ่งเรือง',
                'email' => 'pimchanok.r@kku.ac.th',
                'phone' => '081-234-5680',
                'role' => 'admin',
            ],
        ]);
    }
}
