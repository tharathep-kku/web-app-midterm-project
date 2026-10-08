@extends('layouts.site')

@section('title', 'เข้าสู่ระบบ')

@section('intro')
    @include('partials.intro')
@endsection

@section('content')

    <h2><strong>เข้าสู่ระบบ</strong></h2>
    <p>ใช้หน้านี้หน้าเดียวสำหรับทุกบัญชี ระบบจะแสดงเมนูตามประเภทบัญชี (ผู้ใช้ทั่วไป / แอดมิน)</p>

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

    <p>
        ยังไม่มีบัญชี? <a href="{{ route('register') }}">สมัครสมาชิก</a> /
        <a href="{{ route('password.request') }}">ลืมรหัสผ่าน</a>
    </p>

    <br>
    <hr>

@endsection