<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โพสต์ของที่เก็บได้ - KKU Return</title>
</head>

<body>
    <h1><strong>KKU Return: lost and found</strong></h1>
    @include('partials.menu')
    <p>โพสต์ในนามหน่วยงาน: <strong>{{ $agency->fullname }}</strong></p>
<hr>

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

    <h2><strong>โพสต์ของที่เก็บได้</strong></h2>

    <form method="POST" action="{{ route('agency.store') }}" enctype="multipart/form-data">
        @csrf

        <label for="title">ชื่อสิ่งของ: <span style="color: red;">*</span></label><br>
        <input type="text" id="title" name="title" placeholder="เช่น กระเป๋าตังค์สีน้ำตาล" value="{{ old('title') }}" required><br><br>

        <label for="type">ประเภทประกาศ: <span style="color: red;">*</span></label>
        <select id="type" name="type" required>
            <option value="found" @selected(old('type', 'found') === 'found')>พบของ</option>
            <option value="lost" @selected(old('type') === 'lost')>ของหาย</option>
        </select>
        <br><br>

        <!-- หมวดหมู่ -->
        <label for="category_id">หมวดหมู่: <span style="color: red;">*</span></label>
        <select id="category_id" name="category_id" required>
            <option value="">-- เลือกหมวดหมู่ --</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" @selected((string) old('category_id') === (string) $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
        <br><br>

        <!-- สถานที่ (เลือกจากรายการสถานที่ในและรอบ มข.) -->
        <label for="location">สถานที่ (อาคาร/บริเวณ): <span style="color: red;">*</span></label><br>
        <select id="location" name="location" required>
            <option value="">-- เลือกสถานที่ --</option>
            @foreach ($locations as $loc)
                <option value="{{ $loc }}" @selected(old('location') === $loc)>{{ $loc }}</option>
            @endforeach
        </select>
        <br><br>

        <label for="place_point">จุดสถานที่ (ระบุให้ละเอียด): <span style="color: red;">*</span></label><br>
        <input type="text" id="place_point" name="place_point" placeholder="เช่น ชั้น 2 โต๊ะอ่านหนังสือริมหน้าต่าง" value="{{ old('place_point') }}" required><br>
        <small>จุดที่เก็บของได้ หรือจุดที่นำของมาฝากไว้ เพื่อให้เจ้าของมารับได้ถูกที่</small>
        <br><br>

        <label for="event_date">วันที่พบ/วันที่หาย: <span style="color: red;">*</span></label><br>
        <input type="date" id="event_date" name="event_date" value="{{ old('event_date') }}" required><br><br>

        <!-- สี / ยี่ห้อ: แยกช่องเพื่อให้หน้าค้นหากรองได้ -->
        <label for="color">สี:</label>
        <select id="color" name="color">
            <option value="">-- ไม่ระบุ --</option>
            @foreach ($colors as $color)
                <option value="{{ $color }}" @selected(old('color') === $color)>{{ $color }}</option>
            @endforeach
        </select>
        @error('color') <div style="color: red;">{{ $message }}</div> @enderror
        <br><br>

        <label for="brand">ยี่ห้อ:</label><br>
        <input type="text" id="brand" name="brand" placeholder="เช่น Apple, Samsung, Casio (ถ้าไม่ทราบให้เว้นว่าง)" value="{{ old('brand') }}" maxlength="100">
        @error('brand') <div style="color: red;">{{ $message }}</div> @enderror
        <br><br>

        <label for="description">รายละเอียดของสิ่งของ:</label><br>
        <textarea id="description" name="description" placeholder="อธิบายลักษณะของสิ่งของ...">{{ old('description') }}</textarea>
        <br><br>

        <!-- รูปสิ่งของ: เปลี่ยนเป็นไฟล์อัปโหลดจริง -->
        <label for="image_url">รูปสิ่งของ:</label><br>
        <input type="file" id="image_url" name="image_url" accept="image/*">
        <br><img id="image_url_preview" alt="ตัวอย่างรูปสิ่งของ" style="display: none; max-width: 250px; max-height: 250px; margin-top: 8px; border: 1px solid #ccc;">
        @error('image_url') <div style="color: red;">{{ $message }}</div> @enderror
        <br><br>

        <hr>
        <h3><strong>หลักฐานยืนยัน</strong></h3>

        <!-- รูปหลักฐาน: เปลี่ยนเป็นไฟล์อัปโหลดจริง -->
        <label for="evidence_url">รูปหลักฐาน: <span style="color: red;">*</span></label><br>
        <input type="file" id="evidence_url" name="evidence_url" accept="image/*" required>
        <br><img id="evidence_url_preview" alt="ตัวอย่างรูปหลักฐาน" style="display: none; max-width: 250px; max-height: 250px; margin-top: 8px; border: 1px solid #ccc;">
        <small>รูปตอนรับของเข้าหน่วยงาน หรือรูปแบบฟอร์มรับฝากของที่มีลายเซ็นผู้ส่งมอบ</small>
        @error('evidence_url') <div style="color: red;">{{ $message }}</div> @enderror
        <br><br>

        <label for="evidence_note">คำอธิบายหลักฐาน: <span style="color: red;">*</span></label><br>
        <textarea id="evidence_note" name="evidence_note" placeholder="เช่น นักศึกษานำมาส่งที่เคาน์เตอร์ เวลา 14.30 น. มีบันทึกรับของเลขที่ 025/2569" required>{{ old('evidence_note') }}</textarea>
        <br><br>

        <button type="submit">ส่งโพสต์ให้แอดมินตรวจสอบ</button>
        <a href="{{ route('agency.index') }}"><button type="button">ยกเลิก</button></a>
    </form>

    <br>
    <hr>
    <footer>
        <p>*หมายเหตุ: โพสต์จะมีสถานะ "รออนุมัติ" จนกว่าแอดมินจะตรวจสอบหลักฐานยืนยันเรียบร้อย</p>
    </footer>
    <script>
        // แสดงรูปตัวอย่างทันทีที่เลือกไฟล์ เพื่อเช็คว่าเลือกรูปถูก
        function setupPreview(inputId, previewId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);

            input.addEventListener('change', function () {
                const file = input.files[0];

                if (preview.src) {
                    URL.revokeObjectURL(preview.src);
                }

                if (file && file.type.startsWith('image/')) {
                    preview.src = URL.createObjectURL(file);
                    preview.style.display = 'block';
                } else {
                    preview.removeAttribute('src');
                    preview.style.display = 'none';
                }
            });
        }

        setupPreview('image_url', 'image_url_preview');
        setupPreview('evidence_url', 'evidence_url_preview');
    </script>
</body>

</html>
