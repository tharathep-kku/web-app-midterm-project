@extends('layouts.site')

@section('title', 'สมัครสมาชิก')

@section('intro')
    @include('partials.intro')
@endsection

@section('content')
    <h2><strong>สมัครสมาชิก</strong></h2>

    @if ($errors->any())
        <h3>สมัครสมาชิกไม่สำเร็จ</h3>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <!-- ส่งไปที่ route register.store ของ Fortify (ใช้ CreateNewUser สร้างบัญชี) -->
    <form method="POST" action="{{ route('register.store') }}">
        @csrf

        <label for="name">ชื่อ:</label><br>
        <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus><br><br>

        <label for="email">อีเมล:</label><br>
        <input type="email" id="email" name="email" placeholder="เช่น email@kku.ac.th" value="{{ old('email') }}" required><br><br>

        <label for="password">รหัสผ่าน:</label>
        <small>อย่างน้อย 8 ตัวอักษร ต้องมีพิมพ์ใหญ่ พิมพ์เล็ก และตัวเลข</small><br>
        <input type="password" id="password" name="password" placeholder="เช่น KkuReturn26" required><br><br>
        

        <label for="password_confirmation">ยืนยันรหัสผ่าน:</label><br>
        <input type="password" id="password_confirmation" name="password_confirmation" required><br><br>

        <button type="submit">สมัครสมาชิก</button>
    </form>

    <p>มีบัญชีอยู่แล้ว? <a href="{{ route('login') }}">เข้าสู่ระบบ</a></p>
@endsection
