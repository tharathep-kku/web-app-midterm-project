<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าแรก - KKU Return</title>
</head>

<body>
    <h1><strong>KKU Return: lost and found</strong></h1>
    <p>Welcome to KKU Return ที่จะช่วยคุณตามหาของสำคัญ หรือเจ้าของที่พัดพรากไปเอง</p>
    <p>ศูนย์รวมแจ้งของหายและแจ้งพบของภายในมหาวิทยาลัยขอนแก่น ปลอดภัย ตรวจสอบได้ ลดความเสี่ยงจากการแอบอ้าง</p>

    <ul>
        <li>มีหลักฐานยืนยันการส่งมอบทุกครั้ง</li>
    </ul>
    @include('partials.menu')
    <br><br>
    <hr>

    <p>เก็บของได้ลงประกาศไว้ ของหายค้นหาก่อนแจ้ง เพื่อให้ของกลับไปหาเจ้าของเร็วที่สุด</p>


    <form method="GET" action="{{ route('home') }}">
        <label for="from">เมื่อวันที่</label>
        <input type="date" id="from" name="from" value="{{ $from }}">

        <label for="to">ถึง</label>
        <input type="date" id="to" name="to" value="{{ $to }}">

        <button type="submit">กรอง</button>
        <a href="{{ route('home') }}">ล้างตัวกรอง</a>
    </form>

    <!-- ปุ่มลัดช่วงเวลา -->
    <p>
        <a href="{{ route('home', ['from' => date('Y-m-d', strtotime('-7 days')), 'to' => date('Y-m-d')]) }}">7 วันล่าสุด</a> ·
        <a href="{{ route('home', ['from' => date('Y-m-d', strtotime('-30 days')), 'to' => date('Y-m-d')]) }}">30 วันล่าสุด</a> ·
        <a href="{{ route('home', ['from' => date('Y-m-d', strtotime('-3 months')), 'to' => date('Y-m-d')]) }}">3 เดือนล่าสุด</a> ·
        <a href="{{ route('home') }}">ทั้งหมด</a>
    </p>

    <!-- ---------- จำนวนผลลัพธ์ ---------- -->
    <p aria-live="polite">
        พบ {{ $items->total() }} รายการ
        @if ($from !== '' || $to !== '')
            ระหว่าง
            {{ $from !== '' ? date('d/m/Y', strtotime($from)) : 'เริ่มต้น' }}
            ถึง
            {{ $to !== '' ? date('d/m/Y', strtotime($to)) : 'ปัจจุบัน' }}
        @endif
    </p>

    <!-- ---------- ตาราง ---------- -->
    <table border="1" cellspacing="2" cellpadding="0">
        <thead>
            <tr>
                <th>สิ่งของ</th>
                <th>ภาพของหาย</th>
                <th>หมวดหมู่</th>
                <th>สถานที่พบ</th>
                <th>ชื่อผู้ใช้</th>
                <th>วันที่พบ</th>
                <th>สถานะ</th>
                <th>ติดต่อ</th>
            </tr>
        </thead>
        <tbody align="center">
            @forelse ($items as $item)
                <tr>
                    <td>{{ $item->title }}</td>
                    <td>
                        @if ($item->image_url)
                            <img src="{{ asset($item->image_url) }}" alt="{{ $item->title }}" width="100">
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $item->category_name }}</td>
                    <td>{{ $item->location }}</td>
                    <td>{{ $item->reporter_name }}</td>
                    <td>{{ $item->event_date }}</td>
                    <td>{{ $item->status }}</td>
                    <td>
                        <a href="{{ route('item.show', $item->id) }}"><button type="button">More</button></a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">
                        ไม่มีประกาศในช่วงวันที่ที่เลือก —
                        <a href="{{ route('home', ['from' => date('Y-m-d', strtotime('-30 days')), 'to' => date('Y-m-d')]) }}">ลองขยายเป็น 30 วันล่าสุด</a>
                        หรือ <a href="{{ route('agency.create') }}">ลงประกาศตามหาของ</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div>
        {{ $items->links('partials.pagination') }}
    </div>

    <p><a href="{{ route('archive.home') }}">ดูรายการที่เก็บเข้าคลังแล้ว</a></p>

    <hr>

    <!-- ---------- ท้ายหน้า ---------- -->
    <footer>
        <p>*หมายเหตุ: แพลตฟอร์มนี้เป็นเพียงพื้นที่สาธารณะสำหรับเชื่อมโยงข้อมูลฟรี
            ไม่มีส่วนเกี่ยวข้องหรือรับประกันความถูกต้องของข้อมูล การส่งมอบสิ่งของ
            หรือการธุรกรรมใดๆ ระหว่างผู้ใช้งาน</p>

        <strong>ช่องทางติดต่อ</strong>
        <ul>
            <li>อีเมล: kkureturn01@kku.ac.th</li>
            <li>โทรศัพท์: 012-345-6789</li>
            <li>Facebook: KKU Return</li>
        </ul>
    </footer>

</body>

</html>
