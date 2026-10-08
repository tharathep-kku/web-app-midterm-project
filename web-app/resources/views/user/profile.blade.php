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
    <p>แก้ไขชื่อและอีเมลที่ใช้เข้าสู่ระบบ</p>
    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('PUT')

        <label for="name">ชื่อ:</label><br>
        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required><br><br>
        
        <label for="email">อีเมล:</label><br>
        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required><br><br>

        <label for="phone">เบอร์โทรศัพท์:</label><br>
        <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" maxlength="12" pattern="0[0-9]{2}-?[0-9]{3}-?[0-9]{4}" title="กรอกเบอร์โทร 10 หลัก ขึ้นต้นด้วย 0 เช่น 081-234-5678 หรือ 0812345678" required><br><br>

        <button type="submit">บันทึก</button>
    </form>

        <hr>

        <h3>โพสต์ของฉัน</h3>
        <p>ดูและจัดการโพสต์แจ้งของหาย/พบของที่คุณเคยลงไว้ ใช้ตัวกรองด้านล่างเพื่อหารายการที่ต้องการให้เจอง่ายขึ้น</p>

        <form method="GET" action="{{ route('profile.edit') }}">
            <label for="type">ประเภท:</label>
            <select id="type" name="type">
                <option value="" {{ $type === '' ? 'selected' : '' }}>-- ทั้งหมด --</option>
                <option value="found" {{ $type === 'found' ? 'selected' : '' }}>พบของ</option>
                <option value="lost" {{ $type === 'lost' ? 'selected' : '' }}>ของหาย</option>
            </select>

            <label for="status">สถานะ:</label>
            <select id="status" name="status">
                <option value="" {{ $status === '' ? 'selected' : '' }}>-- ทั้งหมด --</option>
                @foreach ($statuses as $st)
                    <option value="{{ $st }}" {{ $status === $st ? 'selected' : '' }}>{{ $st }}</option>
                @endforeach
            </select>

            <label for="category">หมวดหมู่:</label>
            <select id="category" name="category">
                <option value="" {{ $category === '' ? 'selected' : '' }}>-- ทั้งหมด --</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ (string) $category === (string) $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <br><br>

            <label for="keyword">ชื่อสิ่งของ:</label>
            <input type="text" id="keyword" name="keyword" value="{{ $keyword }}">

            <label for="location">สถานที่:</label>
            <input type="text" id="location" name="location" list="profileLocationList" value="{{ $location }}">
            <datalist id="profileLocationList">
                @foreach ($locations as $loc)
                    <option value="{{ $loc }}">
                @endforeach
            </datalist>


            <label for="start_date">ช่วงวันที่พบ/หาย ตั้งแต่:</label>
            <input type="date" id="start_date" name="start_date" value="{{ $start_date }}">

            <label for="end_date">ถึง:</label>
            <input type="date" id="end_date" name="end_date" value="{{ $end_date }}">

            <button type="submit">กรองข้อมูล</button>
            <a href="{{ route('profile.edit') }}"><button type="button">ล้างตัวกรอง</button></a>
        </form>

        <br>

        <table border="1" cellspacing="2" cellpadding="0">
            <thead>
                <tr>
                    <th>สิ่งของ</th>
                    <th>ภาพของที่หาย</th>
                    <th>ประเภท</th>
                    <th>หมวดหมู่</th>
                    <th>สถานที่</th>
                    <th>รายละเอียดเพิ่มเติม</th>
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
                        <td style="text-align: center;">
                            @if ($item->image_url)
                                <img src="{{ asset($item->image_url) }}" alt="{{ $item->title }}" width="100">
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $item->type === 'found' ? 'พบของ' : 'ของหาย' }}</td>
                        <td>{{ $item->category ? $item->category->name : 'อื่นๆ' }}</td>
                        <td>{{ $item->location }}</td>
                        <td>{{ $item->description }}</td>
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
                        <td colspan="10">ยังไม่มีโพสต์ของคุณ</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    

    <hr>

    <h3>เปลี่ยนรหัสผ่าน</h3>
    <p>ตั้งรหัสผ่านใหม่เพื่อความปลอดภัยของบัญชี</p>
    <form method="POST" action="{{ route('profile.password') }}">
        @csrf
        @method('PUT')

        <label for="current_password">รหัสผ่านปัจจุบัน:</label><br>
        <input type="password" id="current_password" name="current_password" required><br><br>

        <label for="password">รหัสผ่านใหม่:</label><br>
        <input type="password" id="password" name="password" placeholder="เช่น KkuReturn26" required><br>
        <span style="color: red;">*<small>อย่างน้อย 8 ตัวอักษร ต้องมีพิมพ์ใหญ่ พิมพ์เล็ก และตัวเลข</small></span><br><br>

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
