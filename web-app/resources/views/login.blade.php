<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - KKU Return</title>
</head>

<body>
    <h1><strong>KKU Return: lost and found</strong></h1>
    @include('partials.menu')

    <hr>

    <h2><strong>เข้าสู่ระบบ</strong></h2>
    <p>ใช้หน้านี้หน้าเดียวสำหรับทุกบัญชี ระบบจะแสดงเมนูตามประเภทบัญชี (ผู้ใช้ทั่วไป / หน่วยงาน / แอดมิน)</p>

    @if (session('status'))
        <p><strong>{{ session('status') }}</strong></p>
    @endif

    @if ($errors->any())
        <h3>เข้าสู่ระบบไม่สำเร็จ</h3>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <!-- ส่งไปที่ route login.store ของ Fortify เพื่อตรวจอีเมลกับรหัสผ่าน -->
    <form method="POST" action="{{ route('login.store') }}">
        @csrf

        <label for="email">อีเมล:</label><br>
        <input type="email" id="email" name="email" placeholder="เช่น email@kku.ac.th" value="{{ old('email') }}" required autofocus><br><br>

        <label for="password">รหัสผ่าน:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <input type="checkbox" id="remember" name="remember" @checked(old('remember'))>
        <label for="remember">จดจำการเข้าสู่ระบบ</label>
        <br><br>

        <button type="submit">เข้าสู่ระบบ</button>
    </form>

    <br>
    <hr>

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
