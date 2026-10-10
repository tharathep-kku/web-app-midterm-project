<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - KKU Return</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;800&family=Kanit:wght@500;600&family=Noto+Sans+Thai:wght@400;500&display=swap" rel="stylesheet">
    <link href="{{ asset('css/theme.css') }}" rel="stylesheet">
</head>

<body>
    @include('partials.menu')

    <div class="container py-4">
        <h1 class="wordmark">KKU <span>RETURN</span><small>lost and found</small></h1>
        @yield('intro')

        @yield('content')

        <hr>
        <footer>
            <p>@yield('note', '*หมายเหตุ: แพลตฟอร์มนี้เป็นเพียงพื้นที่สาธารณะสำหรับเชื่อมโยงข้อมูลฟรี ไม่มีส่วนเกี่ยวข้องหรือรับประกันความถูกต้องของข้อมูล การส่งมอบสิ่งของ หรือการธุรกรรมใดๆ ระหว่างผู้ใช้งาน')</p>

            <strong>ช่องทางติดต่อ</strong>
            <ul>
                <li>อีเมล: kkureturn01@kku.ac.th</li>
                <li>โทรศัพท์: 012-345-6789</li>
                <li>Facebook: KKU Return</li>
            </ul>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>