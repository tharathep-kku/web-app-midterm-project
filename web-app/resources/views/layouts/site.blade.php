<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - KKU Return</title>
</head>

<body>
    <h1><strong>KKU Return: lost and found</strong></h1>
    @yield('intro')
    @include('partials.menu')
    <hr>

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
</body>

</html>
