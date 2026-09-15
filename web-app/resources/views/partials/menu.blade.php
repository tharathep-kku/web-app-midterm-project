<nav>
    <a href="{{ route('home') }}">หน้าแรก</a> /
    <a href="{{ route('search.index') }}">ค้นหา</a> /
    <a href="{{ route('agency.create') }}">แจ้งของหาย</a> /
    <!-- เมนูของ user เฉพาะ แสดงหลังเข้าสู่ระบบแล้วเท่านั้น -->
    @auth
        <a href="{{ route('agency.index') }}">หน่วยงาน</a> /
        <a href="{{ route('admin.index') }}">แอดมิน</a> /
        <a href="{{ route('admin.stats') }}">สถิติ</a> /
    @endauth
    <a href="{{ route('login') }}">เข้าสู่ระบบ</a> /
    <a href="{{ route('profile.edit') }}">โปรไฟล์</a>
</nav>
