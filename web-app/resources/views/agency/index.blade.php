<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน่วยงาน - KKU Return</title>
</head>

<body>
    <h1><strong>KKU Return: lost and found</strong></h1>
    @include('partials.menu')
    <p>ส่วนของหน่วยงาน สำหรับโพสต์ของที่เก็บได้ พร้อมหลักฐานยืนยันและจุดสถานที่</p>
    <hr>

    @if (session('success'))
        <p><strong>{{ session('success') }}</strong></p>
    @endif

    @if (session('error'))
        <p><strong>{{ session('error') }}</strong></p>
    @endif

    @if ($agency)
        <p>เข้าสู่ระบบในนาม: <strong>{{ $agency->fullname }}</strong> ({{ $agency->email }} / {{ $agency->phone }})</p>
    @else
        <p>บัญชีนี้ยังไม่ได้ผูกกับข้อมูลหน่วยงาน กรุณาติดต่อแอดมิน</p>
    @endif

    <hr>

    <h2><strong>โพสต์ของหน่วยงาน</strong></h2>

    @if ($agency)
        <p><a href="{{ route('agency.create') }}"><button type="button">+ โพสต์ของที่เก็บได้</button></a></p>

        <table border="1" cellspacing="2" cellpadding="0">
            <thead>
                <tr>
                    <th>รหัส</th>
                    <th>สิ่งของ</th>
                    <th>ประเภท</th>
                    <th>หมวดหมู่</th>
                    <th>สถานที่</th>
                    <th>จุดสถานที่</th>
                    <th>วันที่พบ/หาย</th>
                    <th>หลักฐาน</th>
                    <th>สถานะอนุมัติ</th>
                    <th>ดู</th>
                </tr>
            </thead>
            <tbody align="center">
                @forelse ($items as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->title }}</td>
                        <td>{{ $item->type === 'found' ? 'พบของ' : 'ของหาย' }}</td>
                        <td>{{ $item->category_name }}</td>
                        <td>{{ $item->location }}</td>
                        <td>{{ $item->place_point }}</td>
                        <td>{{ $item->event_date }}</td>
                        <td>
                            @if ($item->evidence_url)
                                <img src="{{ asset($item->evidence_url) }}" alt="หลักฐานของ {{ $item->title }}" width="80">
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $item->approval_status }}</td>
                        <td>
                            <a href="{{ route('agency.show', $item->id) }}"><button type="button">รายละเอียด</button></a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10">ยังไม่มีโพสต์ของหน่วยงานนี้</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div>
            {{ $items->links('partials.pagination') }}
        </div>
    @endif

    <br>
    <hr>
    <footer>
        <p>*หมายเหตุ: โพสต์ของหน่วยงานทุกรายการต้องผ่านการอนุมัติจากแอดมินก่อน จึงจะแสดงเป็นข้อมูลที่ยืนยันแล้ว</p>
    </footer>
</body>

</html>
