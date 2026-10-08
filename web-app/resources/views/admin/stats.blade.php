<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สถิติ - KKU Return</title>
</head>

<body>
    <h1><strong>KKU Return: lost and found</strong></h1>
    @include('partials.menu')
    <p>สถิติของระบบ (ดูโดย {{ $admin->fullname }})</p>
    <hr>

    <h2><strong>ภาพรวม</strong></h2>

    <ul>
        <li>โพสต์ทั้งหมด {{ $total_item }} รายการ</li>
        <li>ประกาศพบของ {{ $found_item }} รายการ / ประกาศของหาย {{ $lost_item }} รายการ</li>
        <li>ได้รับคืนแล้ว {{ $returned_item }} รายการ / ยังไม่พบเจ้าของ {{ $waiting_owner }} รายการ / รอแอดมินยืนยัน {{ $waiting_confirm }} รายการ</li>
        <li>ได้รับของแล้ว {{ $received_item }} รายการ / หลักฐานไม่ถูกต้อง {{ $invalid_evidence }} รายการ</li>
        <li>อัตราการได้รับคืน {{ number_format($return_rate, 2) }} % ของโพสต์ทั้งหมด</li>
        <li>ช่วงวันที่ของข้อมูล {{ $first_date ? $first_date : '-' }} ถึง {{ $last_date ? $last_date : '-' }}</li>
    </ul>

    <h2><strong>สถิติแยกตามหมวดหมู่</strong></h2>

    <table border="1" cellspacing="2" cellpadding="0">
        <thead>
            <tr>
                <th>หมวดหมู่</th>
                <th>จำนวนที่แจ้ง</th>
                <th>ได้รับคืนแล้ว</th>
            </tr>
        </thead>
        <tbody align="center">
            @foreach ($category_stats as $row)
                <tr>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['total'] }}</td>
                    <td>{{ $row['returned'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <br>
    <h2><strong>สถิติแยกตามสถานที่</strong></h2>

    <table border="1" cellspacing="2" cellpadding="0">
        <thead>
            <tr>
                <th>สถานที่</th>
                <th>จำนวนที่แจ้ง</th>
            </tr>
        </thead>
        <tbody align="center">
            @forelse ($location_stats as $row)
                <tr>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['total'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">ยังไม่มีข้อมูล</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <br>
    <h2><strong>ผู้ใช้งานในระบบ</strong></h2>

    <ul>
        <li>ผู้ใช้ทั้งหมด {{ $total_user }} คน</li>
        <li>ผู้ใช้ทั่วไป {{ $normal_user }} คน</li>
        <li>แอดมิน {{ $admin_user }} คน</li>
    </ul>

    <br>
    <hr>
    <footer>
        <p>*หมายเหตุ: ตัวเลขทั้งหมดคำนวณจากข้อมูลในฐานข้อมูล ณ เวลาที่เปิดหน้านี้</p>
    </footer>
</body>

</html>
