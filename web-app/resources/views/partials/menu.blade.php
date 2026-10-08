<nav>
    <a href="{{ route('home') }}">หน้าแรก</a> /
    <a href="{{ route('search.home') }}">ค้นหา</a> /

    @guest
        <a href="{{ route('login') }}">เข้าสู่ระบบ</a>
    @endguest

    @auth
        <!-- เมนูแยกตาม role ของบัญชีที่ล็อกอิน (user / admin) -->
        @if (auth()->user()->role === 'admin')
            <a href="{{ route('admin.index') }}">แอดมิน</a> /
            <a href="{{ route('admin.stats') }}">สถิติ</a> /
        @else
            <a href="{{ route('posts.create') }}">แจ้งของหาย</a> /
        @endif
        <a href="{{ route('location') }}">จุดรับ-ส่งคืนของ</a> /
        <a href="{{ route('profile.edit') }}">โปรไฟล์</a> /

        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
            @csrf
            <button type="submit">ออกจากระบบ</button>
        </form>
        ({{ auth()->user()->name }})
    @endauth
</nav>
