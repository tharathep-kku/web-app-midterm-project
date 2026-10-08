@extends('layouts.site')

@section('title', 'แจ้งของหาย')
@section('intro')
    @include('partials.intro')
@endsection

@section('content')
    <h2><strong>แจ้งของหาย / แจ้งพบของ</strong></h2>
    <p>กรอกข้อมูลให้ละเอียดที่สุด เพื่อให้เจ้าของหรือผู้ที่เก็บได้ค้นหาเจอได้ง่าย</p>

    <dialog id="kkuDialog" style="max-width: 420px; width: 90%; max-height: 90vh; overflow: auto; border: 1px solid #999; border-radius: 8px; padding: 0;">
        <div style="padding: 10px 16px; background: #f0f0f0; border-bottom: 1px solid #ccc;">
            <strong>KKU Return</strong>
        </div>
        <div id="kkuDialogMessage" style="padding: 16px; white-space: pre-line;"></div>
        <!-- รูปที่เลือกไว้ (แสดงเฉพาะตอนยืนยันข้อมูล) -->
        <div id="kkuDialogImages" style="padding: 0 16px 16px;"></div>
        <div style="padding: 10px 16px; text-align: right; border-top: 1px solid #ccc;">
            <button type="button" id="kkuDialogCancel">ยกเลิก</button>
            <button type="button" id="kkuDialogOk">ตกลง</button>
        </div>
    </dialog>

    <script>
        // images = [{ label: 'ชื่อรูป', src: 'ที่อยู่รูป' }, ...] (ไม่ส่งมาก็ได้)
        function kkuDialog(message, showConfirm, images) {
            const dialog = document.getElementById('kkuDialog');
            const okButton = document.getElementById('kkuDialogOk');
            const cancelButton = document.getElementById('kkuDialogCancel');
            const imageBox = document.getElementById('kkuDialogImages');

            document.getElementById('kkuDialogMessage').textContent = message;

            imageBox.innerHTML = '';
            (images || []).forEach(function (image) {
                const label = document.createElement('div');
                label.textContent = image.label;

                const img = document.createElement('img');
                img.src = image.src;
                img.alt = image.label;
                img.style.cssText = 'max-width: 100%; max-height: 180px; margin: 4px 0 10px; border: 1px solid #ccc;';

                imageBox.appendChild(label);
                imageBox.appendChild(img);
            });
            cancelButton.style.display = showConfirm ? 'inline-block' : 'none';

            return new Promise(function (resolve) {
                function close(result) {
                    okButton.onclick = null;
                    cancelButton.onclick = null;
                    dialog.oncancel = null;
                    dialog.close();
                    resolve(result);
                }

                okButton.onclick = function () { close(true); };
                cancelButton.onclick = function () { close(false); };
                dialog.oncancel = function (event) { event.preventDefault(); close(false); }; // กด Esc = ยกเลิก

                dialog.showModal();
                okButton.focus();
            });
        }
    </script>

    @if (session('success'))
        <script>
            kkuDialog(@json(session('success')), false).then(function () {
                window.location.href = @json(route('home'));
            });
        </script>
    @endif

    @if ($errors->any())
        <h3>ข้อมูลไม่ถูกต้อง</h3>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form id="postForm" method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data">
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
        function setupPreview(inputId, previewId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);

            input.addEventListener('change', function () {
                const file = input.files[0];

                if (preview.src) {
                    URL.revokeObjectURL(preview.src);
                }

                if (file && file.size > 2 * 1024 * 1024) {
                    const sizeMb = (file.size / 1024 / 1024).toFixed(2);
                    kkuDialog('ไฟล์ "' + file.name + '" มีขนาด ' + sizeMb + ' MB\nกรุณาเลือกรูปที่ไม่เกิน 2 MB', false);
                    input.value = '';
                    preview.removeAttribute('src');
                    preview.style.display = 'none';
                    return;
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

        const postForm = document.getElementById('postForm');

        postForm.addEventListener('submit', function (event) {
            event.preventDefault(); 

            const field = (id) => document.getElementById(id).value.trim() || '-';
            const selectedText = (id) => {
                const select = document.getElementById(id);
                return select.value ? select.options[select.selectedIndex].text : '-';
            };
            const fileName = (id) => {
                const input = document.getElementById(id);
                return input.files.length > 0 ? input.files[0].name : '-';
            };

            const isFound = postTypeFound.checked;
            const lines = [
                'กรุณาตรวจสอบข้อมูลก่อนส่ง',
                '',
                'ประเภทการแจ้ง: ' + (isFound ? 'พบของ' : 'ของหาย'),
                'ชื่อสิ่งของ: ' + field('itemName'),
                'หมวดหมู่: ' + selectedText('category'),
                'สถานที่: ' + field('location'),
                'วันที่: ' + field('date'),
                'รายละเอียด: ' + field('description'),
                'รูปสิ่งของ: ' + fileName('image'),
            ];

            if (isFound) {
                if (depositYes.checked) {
                    lines.push('ฝากของไว้ที่: ' + selectedText('returnUnit'));
                    lines.push('รูปตอนฝากของ: ' + fileName('depositImage'));
                } else {
                    lines.push('ฝากของ: ไม่ได้ฝาก (เก็บไว้กับตัวเอง)');
                }
            }

            lines.push('ชื่อผู้แจ้ง: ' + field('reporterName'));
            lines.push('เบอร์โทรติดต่อ: ' + field('phone'));
            lines.push('');
            lines.push('ยืนยันการส่งข้อมูลหรือไม่?');

            // รูปที่เลือกไว้ ใช้รูปตัวอย่างที่แสดงอยู่ในฟอร์มแล้ว
            const images = [];
            const imagePreview = document.getElementById('imagePreview');
            const depositImagePreview = document.getElementById('depositImagePreview');

            if (imagePreview.getAttribute('src')) {
                images.push({ label: 'รูปสิ่งของ:', src: imagePreview.src });
            }
            if (isFound && depositYes.checked && depositImagePreview.getAttribute('src')) {
                images.push({ label: 'รูปตอนฝากของ:', src: depositImagePreview.src });
            }

            kkuDialog(lines.join('\n'), true, images).then(function (confirmed) {
                if (confirmed) {
                    postForm.submit();
                }
            });
        });
    </script>
@endsection