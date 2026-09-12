<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $item->title }} - KKU Return</title>
</head>

<body>
    <h1><strong>KKU Return: lost and found</strong></h1>
    @include('partials.menu')
    <p>รายละเอียดโพสต์ของ <strong>{{ $agency->fullname }}</strong></p>
    <hr>

    @if (session('success'))
        <p><strong>{{ session('success') }}</strong></p>
    @endif

    <h2><strong>{{ $item->title }}</strong></h2>

    <table border="1" cellspacing="2" cellpadding="0">
        <tr>
            <th>รหัสโพสต์</th>
            <td>{{ $item->id }}</td>
        </tr>
        <tr>
            <th>ประเภทประกาศ</th>
            <td>{{ $item->type === 'found' ? 'พบของ' : 'ของหาย' }}</td>
        </tr>
        <tr>
            <th>หมวดหมู่</th>
            <td>{{ $item->category_name }}</td>
        </tr>
        <tr>
            <th>รายละเอียด</th>
            <td>{{ $item->description ? $item->description : '-' }}</td>
        </tr>
        <tr>
            <th>สถานที่</th>
            <td>{{ $item->location }}</td>
        </tr>
        <tr>
            <th>จุดสถานที่</th>
            <td>{{ $item->place_point }}</td>
        </tr>
        <tr>
            <th>วันที่พบ/หาย</th>
            <td>{{ $item->event_date }}</td>
        </tr>
        <tr>
            <th>สถานะของสิ่งของ</th>
            <td>{{ $item->status }}</td>
        </tr>
        <tr>
            <th>รูปสิ่งของ</th>
            <td>
                @if ($item->image_url)
                    <img src="{{ asset($item->image_url) }}" alt="{{ $item->title }}" width="150">
                @else
                    -
                @endif
            </td>
        </tr>
    </table>

    <br>
    <h3><strong>หลักฐานยืนยัน</strong></h3>

    <table border="1" cellspacing="2" cellpadding="0">
        <tr>
            <th>รูปหลักฐาน</th>
            <td>
                @if ($item->evidence_url)
                    <img src="{{ asset($item->evidence_url) }}" alt="หลักฐานของ {{ $item->title }}" width="150">
                @else
                    -
                @endif
            </td>
        </tr>
        <tr>
            <th>คำอธิบายหลักฐาน</th>
            <td>{{ $item->evidence_note ? $item->evidence_note : '-' }}</td>
        </tr>
    </table>

    <br>
    <h3><strong>ผลการตรวจสอบจากแอดมิน</strong></h3>

    <table border="1" cellspacing="2" cellpadding="0">
        <tr>
            <th>สถานะอนุมัติ</th>
            <td>{{ $item->approval_status }}</td>
        </tr>
        <tr>
            <th>ผู้ตรวจสอบ</th>
            <td>{{ $approver_name }}</td>
        </tr>
        <tr>
            <th>วันเวลาที่ตรวจสอบ</th>
            <td>{{ $item->approved_at ? $item->approved_at : '-' }}</td>
        </tr>
        <tr>
            <th>เหตุผลที่ไม่อนุมัติ</th>
            <td>{{ $item->reject_reason ? $item->reject_reason : '-' }}</td>
        </tr>
    </table>

    <br>
    <a href="{{ route('agency.index') }}"><button type="button">กลับไปหน้ารายการ</button></a>

    <br><br>
    <hr>
    <footer>
        <p>*หมายเหตุ: หากโพสต์ไม่ผ่านการอนุมัติ กรุณาแก้ไขหลักฐานยืนยันตามเหตุผลที่แอดมินระบุ แล้วโพสต์ใหม่อีกครั้ง</p>
    </footer>
</body>

</html>
