@extends('layouts.site')

@section('title', 'โปรไฟล์')
@section('intro')
    @include('partials.intro')
@endsection

@section('content')
    <h2><strong>โปรไฟล์</strong></h2>

    @if (session('success'))
        <p><strong>{{ session('success') }}</strong></p>
    @endif

    @if (session('error'))
        <p><strong>{{ session('error') }}</strong></p>
    @endif

    @if ($errors->any())
        <h3>ข้อมูลไม่ถูกต้อง</h3>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <h3>ข้อมูลส่วนตัว</h3>
    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('PUT')

        <label for="name">ชื่อ:</label><br>
        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required><br><br>

        <label for="email">อีเมล:</label><br>
        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required><br><br>

        <button type="submit">บันทึก</button>
    </form>

    {{-- โพสต์ของฉัน แสดงเฉพาะ user ทั่วไป --}}
    @if ($user->role === 'user')
        <hr>

        <h3>โพสต์ของฉัน</h3>
        <table border="1" cellspacing="2" cellpadding="0">
            <thead>
                <tr>
                    <th>ชื่อสิ่งของ</th>
                    <th>ประเภท</th>
                    <th>หมวดหมู่</th>
                    <th>สถานที่</th>
                    <th>วันที่พบ/หาย</th>
                    <th>สถานะ</th>
                    <th>จัดการ</th>
                    <th>ส่งคืนแล้ว</th>
                </tr>
            </thead>
            <tbody align="center">
                @forelse ($items as $item)
                    <tr>
                        <td>{{ $item->title }}</td>
                        <td>{{ $item->type === 'found' ? 'พบของ' : 'ของหาย' }}</td>
                        <td>{{ $item->category ? $item->category->name : 'อื่นๆ' }}</td>
                        <td>{{ $item->location }}</td>
                        <td>{{ $item->event_date }}</td>
                        <td>{{ $item->status }}</td>
                        <td>
                            @if ($item->canEdit())
                                <form method="GET" action="{{ route('posts.edit', $item->id) }}">
                                    <button>แก้ไข</button>
                                </form>
                                <form method="POST" action="{{ route('posts.destroy', $item->id) }}"
                                    onsubmit="return confirm('ยืนยันการลบโพสต์นี้?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit">ลบ</button>
                                </form>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if ($item->canSubmitEvidence())
                                @if ($item->status === 'หลักฐานไม่ถูกต้อง')
                                    <small>หลักฐานเดิมไม่ผ่าน กรุณาส่งใหม่</small><br>
                                @endif
                                <form method="POST" action="{{ route('posts.evidence', $item->id) }}" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <input type="file" name="evidence" accept="image/*" required><br>
                                    <small>jpeg, png, gif, webp, avif ไม่เกิน 2 MB</small><br>
                                    <input type="text" name="evidence_note" placeholder="หมายเหตุ เช่น ชื่อผู้รับคืน"><br>
                                    <button type="submit">ยืนยัน</button>
                                </form>
                            @elseif ($item->status === 'รอแอดมินยืนยัน')
                                รอแอดมินตรวจหลักฐาน
                            @else
                                ส่งคืนเรียบร้อย
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">ยังไม่มีโพสต์ของคุณ</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    <hr>

    <h3>เปลี่ยนรหัสผ่าน</h3>
    <form method="POST" action="{{ route('profile.password') }}">
        @csrf
        @method('PUT')

        <label for="current_password">รหัสผ่านปัจจุบัน:</label><br>
        <input type="password" id="current_password" name="current_password" required><br><br>

        <label for="password">รหัสผ่านใหม่:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <label for="password_confirmation">ยืนยันรหัสผ่านใหม่:</label><br>
        <input type="password" id="password_confirmation" name="password_confirmation" required><br><br>

        <button type="submit">เปลี่ยนรหัสผ่าน</button>
    </form>

    <hr>

    <h3>ลบบัญชี</h3>
    <p>เมื่อลบบัญชีแล้วจะกู้คืนไม่ได้ กรุณากรอกรหัสผ่านเพื่อยืนยัน</p>
    <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('ยืนยันการลบบัญชี?')">
        @csrf
        @method('DELETE')

        <label for="delete_password">รหัสผ่าน:</label><br>
        <input type="password" id="delete_password" name="delete_password" required><br><br>

        <button type="submit">ลบบัญชี</button>
    </form>
@endsection
