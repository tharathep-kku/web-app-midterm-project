<?php

// ข้อความ validation ภาษาไทย (rule ไหนไม่มีในไฟล์นี้จะใช้ข้อความภาษาอังกฤษของ Laravel แทน)
return [
    'confirmed' => ':attribute ไม่ตรงกับช่องยืนยัน',
    'current_password' => 'รหัสผ่านไม่ถูกต้อง',
    'date' => ':attribute ต้องเป็นวันที่',
    'email' => ':attribute ต้องเป็นอีเมลที่ถูกต้อง',
    'exists' => ':attribute ที่เลือกไม่มีในระบบ',
    'image' => ':attribute ต้องเป็นไฟล์รูปภาพ',
    'in' => ':attribute ที่เลือกไม่ถูกต้อง',
    'max' => [
        'file' => ':attribute ต้องมีขนาดไม่เกิน :max KB',
        'string' => ':attribute ต้องยาวไม่เกิน :max ตัวอักษร',
    ],
    'mimes' => ':attribute ต้องเป็นไฟล์ประเภท :values',
    'min' => [
        'string' => ':attribute ต้องยาวอย่างน้อย :min ตัวอักษร',
    ],
    'password' => [
        'letters' => ':attribute ต้องมีตัวอักษรอย่างน้อย 1 ตัว',
        'mixed' => ':attribute ต้องมีทั้งตัวพิมพ์ใหญ่และตัวพิมพ์เล็ก',
        'numbers' => ':attribute ต้องมีตัวเลขอย่างน้อย 1 ตัว',
        'symbols' => ':attribute ต้องมีสัญลักษณ์อย่างน้อย 1 ตัว',
        'uncompromised' => ':attribute นี้เคยรั่วไหลในระบบอื่น กรุณาใช้รหัสผ่านอื่น',
    ],
    'required' => 'กรุณากรอก:attribute',
    'string' => ':attribute ต้องเป็นข้อความ',
    'unique' => ':attribute นี้ถูกใช้แล้ว',

    'attributes' => [
        'name' => 'ชื่อ',
        'email' => 'อีเมล',
        'password' => 'รหัสผ่าน',
        'current_password' => 'รหัสผ่านปัจจุบัน',
        'delete_password' => 'รหัสผ่าน',
        'postType' => 'ประเภทการแจ้ง',
        'itemName' => 'ชื่อสิ่งของ',
        'category' => 'หมวดหมู่',
        'location' => 'สถานที่',
        'date' => 'วันที่',
        'description' => 'รายละเอียด',
        'image' => 'รูปภาพ',
        'reporterName' => 'ชื่อผู้แจ้ง',
        'phone' => 'เบอร์โทรศัพท์',
        'reject_reason' => 'เหตุผลที่ปฏิเสธ',
    ],
];
