<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $returnUnit->name }} - KKU Return</title>
</head>

<body>
    <h1><strong>KKU Return: lost and found</strong></h1>

    <a href="{{ route('home') }}">Home</a> /
    <a href="{{ route('dashboard') }}">Dashboard</a> /
    <a href="{{ route('search.index') }}">Search</a>
    <br><br>
    <hr>

    <h2><strong>{{ $returnUnit->name }}</strong></h2>
    <p>{{ $returnUnit->description }}</p>

    <iframe
        width="100%"
        height="300"
        style="border:0; max-width: 600px;"
        loading="lazy"
        src="https://www.google.com/maps?q={{ $returnUnit->latitude }},{{ $returnUnit->longitude }}&z=16&output=embed">
    </iframe>
    <br><br>

    <a href="https://www.google.com/maps/dir/?api=1&destination={{ $returnUnit->latitude }},{{ $returnUnit->longitude }}" target="_blank" rel="noopener">
        <button type="button">นำทางไปด้วย Google Maps</button>
    </a>

    <br><br>
    <hr>

    @forelse ($itemsByStatus as $status => $items)
        <h3>{{ $status }} ({{ $items->count() }})</h3>
        <table border="1" cellspacing="2" cellpadding="4">
            <thead>
                <tr>
                    <th>สิ่งของ</th>
                    <th>ภาพ</th>
                    <th>หมวดหมู่</th>
                    <th>วันที่พบ</th>
                    <th>รายละเอียด</th>
                </tr>
            </thead>
            <tbody align="center">
                @foreach ($items as $item)
                    <tr>
                        <td><a href="{{ route('item.show', $item->id) }}">{{ $item->title }}</a></td>
                        <td>
                            @if ($item->image_url)
                                <img src="{{ asset($item->image_url) }}" alt="{{ $item->title }}" width="80">
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $item->category?->name ?? 'อื่นๆ' }}</td>
                        <td>{{ $item->event_date }}</td>
                        <td>{{ $item->description }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
    @empty
        <p>ยังไม่มีของที่หน่วยงานนี้</p>
    @endforelse

    <br>
    <a href="{{ route('dashboard') }}"><button type="button">กลับไป Dashboard</button></a>

    <br>
    <hr>
    <footer>
        <p>*หมายเหตุ: แพลตฟอร์มนี้เป็นเพียงพื้นที่สาธารณะสำหรับเชื่อมโยงข้อมูลฟรี
            ไม่มีส่วนเกี่ยวข้องหรือรับประกันความถูกต้องของข้อมูล การส่งมอบสิ่งของ หรือการธุรกรรมใดๆ ระหว่างผู้ใช้งาน</p>
    </footer>
</body>

</html>
