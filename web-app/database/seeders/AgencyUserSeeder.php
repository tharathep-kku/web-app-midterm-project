<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AgencyUserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('finder_users')->insert([
            [
                'username' => 'security_kku',
                'fullname' => 'งานรักษาความปลอดภัย มข.',
                'email' => 'security@kku.ac.th',
                'phone' => '043-009-101',
                'role' => 'agency',
            ],
            [
                'username' => 'library_kku',
                'fullname' => 'สำนักหอสมุด มข.',
                'email' => 'library@kku.ac.th',
                'phone' => '043-009-102',
                'role' => 'agency',
            ],
            [
                'username' => 'studentaffairs_kku',
                'fullname' => 'กองพัฒนานักศึกษาและศิษย์เก่าสัมพันธ์',
                'email' => 'studentaffairs@kku.ac.th',
                'phone' => '043-009-103',
                'role' => 'agency',
            ],
        ]);
    }
}
