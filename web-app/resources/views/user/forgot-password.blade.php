@extends('layouts.site')

@section('title', 'ลืมรหัสผ่าน')

@section('intro')
    @include('partials.intro')
@endsection

@section('content')
    <h2><strong>ลืมรหัสผ่าน</strong></h2>
    <p>กรอกอีเมลและชื่อให้ตรงกับบัญชี แล้วตั้งรหัสผ่านใหม่ได้เลย</p>

    @if ($errors->any())
        <h3>ตั้งรหัสผ่านใหม่ไม่สำเร็จ</h3>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('password.reset.simple') }}">
        @csrf

        <label for="email">อีเมล:</label><br>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus><br><br>

        <label for="name">ชื่อ (ตามที่สมัครไว้):</label><br>
        <input type="text" id="name" name="name" value="{{ old('name') }}" required><br><br>

        <label for="password">รหัสผ่านใหม่:</label>
        <small>อย่างน้อย 8 ตัวอักษร ต้องมีพิมพ์ใหญ่ พิมพ์เล็ก และตัวเลข</small><br>
        <input type="password" id="password" name="password" placeholder="เช่น KkuReturn26" required><br><br>

        <label for="password_confirmation">ยืนยันรหัสผ่านใหม่:</label><br>
        <input type="password" id="password_confirmation" name="password_confirmation" required><br><br>

        <button type="submit">ตั้งรหัสผ่านใหม่</button>
    </form>

    <p><a href="{{ route('login') }}">กลับไปหน้าเข้าสู่ระบบ</a></p>
@endsection
