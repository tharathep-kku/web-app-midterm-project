@extends('layouts.site')

@section('title', 'แก้ไขโพสต์')

@section('content')
    <h2><strong>แก้ไขโพสต์</strong></h2>

    @if ($errors->any())
        <h3>ข้อมูลไม่ถูกต้อง</h3>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <!-- ใช้ชื่อ field เดียวกับหน้า create เพื่อให้ validate แบบเดียวกัน -->
    <form method="POST" action="{{ route('posts.update', $item->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <label>ประเภทการแจ้ง: <span style="color: red;">*</span></label><br>
        <input type="radio" id="postTypeLost" name="postType" value="lost" @checked(old('postType', $item->type) === 'lost') required>
        <label for="postTypeLost">ของหาย</label>
        <input type="radio" id="postTypeFound" name="postType" value="found" @checked(old('postType', $item->type) === 'found')>
        <label for="postTypeFound">พบของ</label>
        <br><br>

        <label for="itemName">ชื่อสิ่งของ: <span style="color: red;">*</span></label><br>
        <input type="text" id="itemName" name="itemName" value="{{ old('itemName', $item->title) }}" required><br><br>

        <label for="category">หมวดหมู่: <span style="color: red;">*</span></label>
        <select id="category" name="category" required>
            <option value="">-- เลือกหมวดหมู่ --</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" @selected((string) old('category', $item->category_id) === (string) $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
        <br><br>

        <label for="location">สถานที่หาย/สถานที่พบ: <span style="color: red;">*</span></label><br>
        <input type="text" id="location" name="location" list="locationList" value="{{ old('location', $item->location) }}" required autocomplete="off">
        <datalist id="locationList">
            @foreach ($locations as $loc)
                <option value="{{ $loc }}">
            @endforeach
        </datalist>
        <br><br>

        <label for="date">วันที่หาย/วันที่พบ: <span style="color: red;">*</span></label><br>
        <input type="date" id="date" name="date" value="{{ old('date', $item->event_date) }}" max="{{ date('Y-m-d') }}" required><br><br>

        <label for="description">รายละเอียดเพิ่มเติม:</label><br>
        <textarea id="description" name="description">{{ old('description', $item->description) }}</textarea>
        <br><br>

        <label>รูปปัจจุบัน:</label><br>
        @if ($item->image_url)
            <img src="{{ asset($item->image_url) }}" alt="{{ $item->title }}" width="150"><br>
        @else
            ไม่มีรูป<br>
        @endif
        <label for="image">เปลี่ยนรูปใหม่ (ไม่เลือก = ใช้รูปเดิม):</label><br>
        <input type="file" id="image" name="image" accept="image/*">
        <br><br>

        <hr>
        <h3><strong>ข้อมูลติดต่อ</strong></h3>

        <label for="reporterName">ชื่อผู้แจ้ง:</label><br>
        <input type="text" id="reporterName" name="reporterName" value="{{ old('reporterName', $item->reporter_name) }}"><br><br>

        <label for="phone">เบอร์โทรติดต่อ:</label><br>
        <input type="tel" id="phone" name="phone" value="{{ old('phone', $item->reporter_phone) }}" maxlength="20"><br><br>

        <button type="submit">บันทึกการแก้ไข</button>
        <a href="{{ route('my-posts.index') }}"><button type="button">ยกเลิก</button></a>
    </form>
@endsection