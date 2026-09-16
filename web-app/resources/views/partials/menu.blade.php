<nav>
    <a href="{{ route('home') }}">หน้าแรก</a> /
    <a href="{{ route('search.home') }}">ค้นหา</a> /

    @guest
        <a href="{{ route('login') }}">เข้าสู่ระบบ</a>
    @endguest

    @auth
        <!-- เมนูแยกตาม role ของบัญชีที่ล็อกอิน -->
        @if (auth()->user()->role === 'home')
            <a href="{{ route('agency.index') }}">หน่วยงาน</a> /
        @elseif (auth()->user()->role === 'admin')
            <a href="{{ route('admin.home') }}">แอดมิน</a> /
            <a href="{{ route('admin.stats') }}">สถิติ</a> /
        @else
            <a href="{{ route('posts.create') }}">แจ้งของหาย</a> /
        @endif
        <a href="{{ route('profile.edit') }}">โปรไฟล์</a> /

        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
            @csrf
            <button type="submit">ออกจากระบบ</button>
        </form>
        ({{ auth()->user()->name }})
    @endauth
<!-- </nav>

    <a href="{{ route('home') }}">Home</a> /
    <a href="{{ route('login') }}">Login</a> /
    <a href="#">Report a lost item</a> /
    <a href="{{ route('search.home') }}">Search</a> /
    <a href="{{ route('dashboard') }}">Dashboard</a> /
    <a href="{{ route('profile.edit') }}">Profile</a> -->