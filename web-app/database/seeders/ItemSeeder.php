<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'user_id' => 1,
                'category_id' => 1,
                'type' => 'found',
                'title' => 'กุญแจรถเก๋งสีดำ',
                'description' => 'พวงกุญแจมีจี้รูปหัวใจติดอยู่ด้วย',
                'location' => 'อาคารวิทยวิภาส',
                'event_date' => '2026-08-20',
                'image_url' => 'images/items/carkey.png',
                'status' => 'ยังไม่พบเจ้าของ',
            ],
            [
                'user_id' => 1,
                'category_id' => 2,
                'type' => 'found',
                'title' => 'กระเป๋าตังค์สีน้ำตาล',
                'description' => 'มีบัตรนักศึกษาอยู่ข้างใน',
                'location' => 'อาคารพจน์ สารสิน',
                'event_date' => '2026-01-28',
                'image_url' => 'images/items/money.png',
                'status' => 'ได้รับคืนแล้ว',
                'returned_date' => '2026-02-10',
            ],
            [
                'user_id' => 1,
                'category_id' => 3,
                'type' => 'found',
                'title' => 'กำไลข้อมือ',
                'description' => 'กำไลเงินลายดอกไม้',
                'location' => 'ศูนย์ประชุมกาญจนาภิเษก',
                'event_date' => '2026-08-19',
                'image_url' => 'images/items/ring.png',
                'status' => 'ได้รับคืนแล้ว',
                'returned_date' => '2026-08-25',
            ],
            [
                'user_id' => 2,
                'category_id' => 4,
                'type' => 'lost',
                'title' => 'บัตรประจำตัวนักศึกษา',
                'description' => 'ทำหายระหว่างเดินจากหอพักมาคณะ',
                'location' => 'ศูนย์อาหารคอมเพล็กซ์',
                'event_date' => '2026-08-25',
                'image_url' => 'images/items/studentID.png',
                'status' => 'ยังไม่พบเจ้าของ',
            ],
            [
                'user_id' => 2,
                'category_id' => 5,
                'type' => 'found',
                'title' => 'หูฟังไร้สายสีขาว',
                'description' => 'ยี่ห้อ Apple อยู่ในเคสสีขาว',
                'location' => 'อาคาร SC09',
                'event_date' => '2026-08-15',
                'image_url' => 'images/items/earphone.png',
                'status' => 'รอแอดมินยืนยัน',
            ],
            [
                'user_id' => 3,
                'category_id' => 6,
                'type' => 'lost',
                'title' => 'ร่มพับสีดำ',
                'description' => 'ร่มพับขนาดเล็กสีดำล้วน',
                'location' => 'อาคารพจน์ สารสิน',
                'event_date' => '2026-08-10',
                'image_url' => 'images/items/umbella_black.png',
                'status' => 'ยังไม่พบเจ้าของ',
            ],
            [
                'user_id' => 3,
                'category_id' => 1,
                'type' => 'lost',
                'title' => 'กุญแจหอพัก',
                'description' => 'พวงกุญแจสีเงินมีป้ายเลขห้อง',
                'location' => 'ศูนย์อาหารคอมเพล็กซ์',
                'event_date' => '2026-08-22',
                'image_url' => 'images/items/dorm_key.png',
                'status' => 'ยังไม่พบเจ้าของ',
            ],
            [
                'user_id' => 4,
                'category_id' => 2,
                'type' => 'lost',
                'title' => 'กระเป๋าสตางค์ใบเล็กสีดำ',
                'description' => 'มีเงินสดและบัตร ATM อยู่ข้างใน',
                'location' => 'ศูนย์ประชุมกาญจนาภิเษก',
                'event_date' => '2026-08-05',
                'image_url' => 'images/items/wallet_black.png',
                'status' => 'รอแอดมินยืนยัน',
            ],
            [
                'user_id' => 5,
                'category_id' => 3,
                'type' => 'found',
                'title' => 'ต่างหูมุก',
                'description' => 'ต่างหูมุกคู่ ขนาดเล็ก',
                'location' => 'อาคารวิทยวิภาส',
                'event_date' => '2026-08-28',
                'image_url' => 'images/items/earring.png',
                'status' => 'ยังไม่พบเจ้าของ',
            ],
            [
                'user_id' => 6,
                'category_id' => 4,
                'type' => 'found',
                'title' => 'สมุดโน้ตปกสีฟ้า',
                'description' => 'มีลายมือเขียนสรุปวิชาเคมี',
                'location' => 'อาคารพจน์ สารสิน',
                'event_date' => '2026-08-12',
                'image_url' => 'images/items/notebook_blue.png',
                'status' => 'ยังไม่พบเจ้าของ',
            ],
            [
                'user_id' => 7,
                'category_id' => 5,
                'type' => 'lost',
                'title' => 'พาวเวอร์แบงค์สีดำ',
                'description' => 'ยี่ห้อ Xiaomi ความจุ 10000mAh',
                'location' => 'ศูนย์อาหารคอมเพล็กซ์',
                'event_date' => '2026-08-18',
                'image_url' => 'images/items/powerbank_black.png',
                'status' => 'ยังไม่พบเจ้าของ',
            ],
            [
                'user_id' => 8,
                'category_id' => 6,
                'type' => 'found',
                'title' => 'แว่นตากันแดด',
                'description' => 'แว่นกันแดดกรอบสีน้ำตาล',
                'location' => 'ศูนย์ประชุมกาญจนาภิเษก',
                'event_date' => '2026-08-07',
                'image_url' => 'images/items/sunglasses_black.png',
                'status' => 'ได้รับคืนแล้ว',
                'returned_date' => '2026-08-12',
            ],
            [
                'user_id' => 9,
                'category_id' => 1,
                'type' => 'found',
                'title' => 'กุญแจรถจักรยานยนต์',
                'description' => 'พวงกุญแจติดตุ๊กตาการ์ตูน',
                'location' => 'อาคารวิทยวิภาส',
                'event_date' => '2026-08-30',
                'image_url' => 'images/items/motorcycle_key.png',
                'status' => 'ยังไม่พบเจ้าของ',
            ],
            [
                'user_id' => 10,
                'category_id' => 2,
                'type' => 'lost',
                'title' => 'กระเป๋าเงินใบยาวสีแดง',
                'description' => 'มีเหรียญและบัตรสมาชิกร้านกาแฟ',
                'location' => 'อาคารพจน์ สารสิน',
                'event_date' => '2026-08-14',
                'image_url' => 'images/items/wallet_red.png',
                'status' => 'รอแอดมินยืนยัน',
            ],
        ];

        foreach ($items as $i => $item) {
            if (!isset($item['returned_date'])) {
                $items[$i]['returned_date'] = null;
            }
        }

        DB::table('items')->insert($items);
    }
}
