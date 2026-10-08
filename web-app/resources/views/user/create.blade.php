@extends('layouts.site')

@section('title', 'แจ้งของหาย')

@section('intro')
    @include('partials.intro')
@endsection

@section('content')
    <h2><strong>แจ้งของหาย / แจ้งพบของ</strong></h2>
    <p>กรอกข้อมูลให้ละเอียดที่สุด เพื่อให้เจ้าของหรือผู้ที่เก็บได้ค้นหาเจอได้ง่าย</p>

    @if (session('success'))
        <p><strong>{{ session('success') }}</strong></p>
    @endif

    @if ($errors->any())
        <h3>ข้อมูลไม่ถูกต้อง</h3>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <!-- ชื่อ field ต้องตรงกับที่ ItemController@store ตรวจ -->
    <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data">
        @csrf

        <label>ประเภทการแจ้ง: <span style="color: red;">*</span></label><br>
        <input type="radio" id="postTypeLost" name="postType" value="lost" @checked(old('postType', 'lost') === 'lost') required>
        <label for="postTypeLost">ของหาย</label>
        <input type="radio" id="postTypeFound" name="postType" value="found" @checked(old('postType') === 'found')>
        <label for="postTypeFound">พบของ</label>
        <br><br>

        <label for="itemName">ชื่อสิ่งของ: <span style="color: red;">*</span></label><br>
        <input type="text" id="itemName" name="itemName" placeholder="เช่น กระเป๋าตังค์สีน้ำตาล" value="{{ old('itemName') }}" required><br><br>

        <label for="category">หมวดหมู่: <span style="color: red;">*</span></label>
        <select id="category" name="category" required>
            <option value="">-- เลือกหมวดหมู่ --</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" @selected((string) old('category') === (string) $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
        <br><br>

        <label for="location">สถานที่หาย/สถานที่พบ: <span style="color: red;">*</span></label><br>
        <input type="text" id="location" name="location" list="locationList" placeholder="เช่น อาคารวิทยวิภาส" value="{{ old('location') }}" required autocomplete="off">
        <datalist id="locationList">
            @foreach ($locations as $loc)
                <option value="{{ $loc }}">
            @endforeach
        </datalist>
        <br><br>

        <label for="date">วันที่หาย/วันที่พบ: <span style="color: red;">*</span></label><br>
        <input type="date" id="date" name="date" value="{{ old('date') }}" max="{{ date('Y-m-d') }}" required><br><br>

        <label for="description">รายละเอียดเพิ่มเติม:</label><br>
        <textarea id="description" name="description" placeholder="อธิบายลักษณะของสิ่งของ เช่น สี ยี่ห้อ ของที่อยู่ข้างใน...">{{ old('description') }}</textarea>
        <br><br>

        <label for="image">รูปสิ่งของ (jpeg, png, jpg, gif ไม่เกิน 2 MB):</label><br>
        <input type="file" id="image" name="image" accept="image/*">
        <br>
        <!-- รูปตัวอย่าง: แสดงทันทีที่เลือกไฟล์ -->
        <img id="imagePreview" alt="ตัวอย่างรูปสิ่งของ" style="display: none; max-width: 250px; max-height: 250px; margin-top: 8px; border: 1px solid #ccc;">
        <br><br>

        <!-- ฝากของ: แสดงเฉพาะตอนเลือก "พบของ" -->
        <div id="depositSection">
            <label>ฝากของไว้ที่จุดรับ-ส่งคืนหรือไม่: <span style="color: red;">*</span></label><br>
            <input type="radio" id="depositNo" name="deposit" value="no" @checked(old('deposit', 'no') === 'no')>
            <label for="depositNo">ไม่ได้ฝาก (เก็บไว้กับตัวเอง)</label>
            <input type="radio" id="depositYes" name="deposit" value="yes" @checked(old('deposit') === 'yes')>
            <label for="depositYes">ฝากไว้แล้ว</label>
            <br><br>

            <div id="returnUnitBox">
                <label for="returnUnit">ฝากไว้ที่: <span style="color: red;">*</span></label>
                <select id="returnUnit" name="returnUnit">
                    <option value="">-- เลือกจุดรับ-ส่งคืน --</option>
                    @foreach ($returnUnits as $unit)
                        <option value="{{ $unit->id }}" @selected((string) old('returnUnit') === (string) $unit->id)>{{ $unit->name }}</option>
                    @endforeach
                </select>
                <br>
                <small>ดูตำแหน่งจุดรับ-ส่งคืนได้ที่เมนู "จุดรับ-ส่งคืนของ"</small>
                <br><br>

                <label for="depositImage">รูปตอนฝากของ (jpeg, png, jpg, gif ไม่เกิน 2 MB): <span style="color: red;">*</span></label><br>
                <input type="file" id="depositImage" name="depositImage" accept="image/*">
                <br>
                <small>ถ่ายรูปของคู่กับป้ายหรือเคาน์เตอร์ของจุดที่ฝาก เพื่อยืนยันว่าฝากไว้ที่นั่นจริง</small>
                <br>
                <!-- รูปตัวอย่างตอนฝากของ -->
                <img id="depositImagePreview" alt="ตัวอย่างรูปตอนฝากของ" style="display: none; max-width: 250px; max-height: 250px; margin-top: 8px; border: 1px solid #ccc;">
                <br><br>
            </div>
        </div>

        <hr>
        <h3><strong>ข้อมูลติดต่อ</strong></h3>

        <label for="reporterName">ชื่อผู้แจ้ง:</label><br>
        <input type="text" id="reporterName" name="reporterName" placeholder="เช่น สมชาย ใจดี" value="{{ old('reporterName') }}"><br><br>

        <label for="phone">เบอร์โทรติดต่อ:</label><br>
        <input type="tel" id="phone" name="phone" placeholder="เช่น 081-234-5678" value="{{ old('phone') }}" maxlength="20"><br><br>

        <button type="submit">ส่งข้อมูลการแจ้ง</button>
        <a href="{{ route('home') }}"><button type="button">ยกเลิก</button></a>
    </form>

    <script>
        // 1) แสดงรูปตัวอย่างทันทีที่เลือกไฟล์ (ใช้ได้ทั้งรูปสิ่งของและรูปตอนฝากของ)
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

        setupPreview('image', 'imagePreview');
        setupPreview('depositImage', 'depositImagePreview');

        // 2) ฝากของ: โชว์ส่วนนี้เฉพาะ "พบของ" และบังคับเลือกจุด + แนบรูปเมื่อเลือก "ฝากไว้แล้ว"
        const postTypeFound = document.getElementById('postTypeFound');
        const depositYes = document.getElementById('depositYes');
        const depositSection = document.getElementById('depositSection');
        const returnUnitBox = document.getElementById('returnUnitBox');
        const returnUnitSelect = document.getElementById('returnUnit');
        const depositImageInput = document.getElementById('depositImage');

        function updateDeposit() {
            const isFound = postTypeFound.checked;
            const isDeposit = isFound && depositYes.checked;

            depositSection.style.display = isFound ? 'block' : 'none';
            returnUnitBox.style.display = isDeposit ? 'block' : 'none';
            returnUnitSelect.required = isDeposit;
            depositImageInput.required = isDeposit;

            // ช่องที่ซ่อนอยู่จะถูก disabled เพื่อไม่ให้ส่งค่าค้างไปกับฟอร์ม
            document.querySelectorAll('input[name="deposit"]').forEach(function (radio) {
                radio.disabled = !isFound;
            });
            returnUnitSelect.disabled = !isDeposit;
            depositImageInput.disabled = !isDeposit;
        }

        document.querySelectorAll('input[name="postType"], input[name="deposit"]').forEach(function (radio) {
            radio.addEventListener('change', updateDeposit);
        });

        updateDeposit();
    </script>
@endsection