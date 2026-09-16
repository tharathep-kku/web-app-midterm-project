<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>คลังประกาศของคืน - KKU Return</title>
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

    <h2><strong>รายการที่เก็บเข้าคลังแล้ว</strong></h2>
    <p>คลังประกาศของคืน: ของที่ได้รับคืนเจ้าของไปแล้วเกิน 6 เดือน จึงถูกย้ายออกจากหน้าแรก</p>

    <p>พบ {{ $items->total() }} รายการ</p>

    <table border="1" cellspacing="2" cellpadding="0">
        <thead>
            <tr>
                <th>สิ่งของ</th>
                <th>ภาพของหาย</th>
                <th>หมวดหมู่</th>
                <th>สถานที่พบ</th>
                <th>ชื่อผู้ใช้</th>
                <th>วันที่พบ</th>
                <th>วันที่คืนเจ้าของ</th>
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
                    <td>{{ $item->returned_date }}</td>
                    <td>{{ $item->status }}</td>
                    <td>
                        <a href="{{ route('item.show', $item->id) }}"><button type="button">More</button></a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">
                        ยังไม่มีรายการใดถูกเก็บเข้าคลัง —
                        <a href="{{ route('home') }}">กลับไปดูประกาศที่ยังใช้งานอยู่</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div>
        {{ $items->links('partials.pagination') }}
    </div>

    <br>
    <p><a href="{{ route('home') }}"><button type="button">กลับหน้าแรก</button></a></p>

    <br>
    <hr>
    <footer>
        <p>*หมายเหตุ: ประกาศในคลังยังไม่ถูกลบออกจากระบบ เก็บไว้เพื่อให้ตรวจสอบย้อนหลังได้</p>

        <strong>ช่องทางติดต่อ</strong>
        <ul>
            <li>อีเมล: kkureturn01@kku.ac.th</li>
            <li>โทรศัพท์: 012-345-6789</li>
            <li>Facebook: KKU Return</li>
        </ul>
    </footer>
</body>

</html>
