<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categories')->insert([
            ['name' => 'กุญแจ'],
            ['name' => 'กระเป๋าตังค์'],
            ['name' => 'เครื่องประดับ'],
            ['name' => 'เอกสาร'],
            ['name' => 'อุปกรณ์อิเล็กทรอนิกส์'],
            ['name' => 'อื่นๆ'],
        ]);
    }
}
