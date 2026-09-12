<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>คลังประกาศของคืน - KKU Return</title>
</head>

<body>
    <h1><strong>KKU Return: lost and found</strong></h1>
    <p>คลังประกาศของคืน: ของที่ได้รับคืนเจ้าของไปแล้วเกิน 6 เดือน จึงถูกย้ายออกจากหน้าแรก</p>

    @include('partials.menu')
    <br><br>
    <hr>

    <h2><strong>รายการที่เก็บเข้าคลังแล้ว</strong></h2>

    <p>พบ {{ $items->count() }} รายการ</p>

    <table border="1" cellspacing="2" cellpadding="0">
        <thead>
            <tr>
                <th>ชื่อสิ่งของ</th>
                <th>รูป</th>
                <th>หมวดหมู่</th>
                <th>สถานที่</th>
                <th>วันที่พบ</th>
                <th>วันที่คืนเจ้าของ</th>
                <th></th>
            </tr>
        </thead>
        <tbody align="center">
            @forelse ($items as $item)
                <tr>
                    <td>{{ $item->title }}</td>

                    <td>
                        @if (!empty($item->image_url))
                            <img src="{{ asset($item->image_url) }}"
                                 alt="{{ $item->title }}"
                                 width="80" height="80"
                                 onerror="this.replaceWith('ไม่มีรูป')">
                        @else
                            ไม่มีรูป
                        @endif
                    </td>

                    <td>{{ $item->category_name }}</td>
                    <td>{{ $item->location }}</td>

                    <td>{{ date('d/m/Y', strtotime($item->event_date)) }}</td>
                    <td>{{ date('d/m/Y', strtotime($item->returned_date)) }}</td>

                    <td><a href="/items/{{ $item->id }}">ดูรายละเอียด</a></td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">
                        ยังไม่มีรายการใดถูกเก็บเข้าคลัง —
                        <a href="{{ route('home') }}">กลับไปดูประกาศที่ยังใช้งานอยู่</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

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
