<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReturnUnitSeeder extends Seeder
{
    /**
     * พิกัดด้านล่างเป็นค่าประมาณ (anchored รอบจุดศูนย์กลาง มข. ~16.4711, 102.8199)
     * ไม่ใช่พิกัดที่สำรวจจริง — ควรแก้ให้ตรงจุดจริงก่อนใช้งานจริง โดยคลิกขวาตำแหน่งจริงใน
     * Google Maps > "พิกัดนี้คืออะไร" แล้วคัดลอกค่า lat/lng มาแทน
     */
    public function run(): void
    {
        DB::table('return_units')->insert([
            [
                'name' => 'หน่วยรักษาความปลอดภัย (รปภ.) ประตูมอดินแดง',
                'description' => 'จุดรับแจ้ง/รับฝากของหายบริเวณประตูทางเข้าหลัก',
                'latitude' => 16.4680,
                'longitude' => 102.8175,
            ],
            [
                'name' => 'โรงพยาบาลศรีนครินทร์',
                'description' => 'จุดรับฝากของหายบริเวณเคาน์เตอร์ประชาสัมพันธ์โรงพยาบาล',
                'latitude' => 16.4655,
                'longitude' => 102.8230,
            ],
            [
                'name' => 'คณะวิทยาศาสตร์',
                'description' => 'จุดรับฝากของหายบริเวณห้องธุรการคณะวิทยาศาสตร์',
                'latitude' => 16.4735,
                'longitude' => 102.8225,
            ],
            [
                'name' => 'สำนักงานอธิการบดี',
                'description' => 'จุดรับฝากของหายบริเวณเคาน์เตอร์ประชาสัมพันธ์สำนักงานอธิการบดี',
                'latitude' => 16.4726,
                'longitude' => 102.8195,
            ],
            [
                'name' => 'หอสมุดกลาง',
                'description' => 'จุดรับฝากของหายบริเวณเคาน์เตอร์บริการหอสมุดกลาง มข.',
                'latitude' => 16.4715,
                'longitude' => 102.8215,
            ],
        ]);
    }
}
