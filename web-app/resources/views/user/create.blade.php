<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แจ้งของหาย - KKU Return</title>
</head>

<body>
    <h1><strong>KKU Return: lost and found</strong></h1>
    @include('partials.menu')

    <hr>

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
        <br><br>

        <hr>
        <h3><strong>ข้อมูลติดต่อ</strong></h3>

        <label for="reporterName">ชื่อผู้แจ้ง:</label><br>
        <input type="text" id="reporterName" name="reporterName" placeholder="เช่น สมชาย ใจดี" value="{{ old('reporterName') }}"><br><br>

        <label for="phone">เบอร์โทรติดต่อ:</label><br>
        <input type="tel" id="phone" name="phone" placeholder="เช่น 081-234-5678" value="{{ old('phone') }}" maxlength="20"><br><br>

        <button type="submit">ส่งข้อมูลการแจ้ง</button>
        <a href="{{ route('home') }}"><button type="button">ยกเลิก</button></a>
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
