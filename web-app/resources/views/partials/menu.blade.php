<nav class="navbar navbar-expand-lg bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}"
           style="font-family:'Baloo 2',sans-serif; color:var(--b-found);">
            KKU <span style="color:var(--b-lost);">RETURN</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">หน้าแรก</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('search.home') }}">ค้นหา</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('location') }}">จุดรับ-ส่งคืนของ</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('archive.home') }}">คลัง</a></li>
                @auth
                    @if (auth()->user()->role === 'admin')
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.index') }}">แอดมิน</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.stats') }}">สถิติ</a></li>
                    @endif
                @endauth
            </ul>

            <div class="d-flex align-items-center gap-2">
                <a class="btn btn-primary btn-pop btn-sm" href="{{ route('posts.create') }}">+ แจ้งของ</a>
                @guest
                    <a class="btn btn-outline-secondary btn-sm" href="{{ route('login') }}">เข้าสู่ระบบ</a>
                @endguest
                @auth
                    <a class="nav-link" href="{{ route('profile.edit') }}">{{ auth()->user()->name }}</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary btn-sm">ออกจากระบบ</button>
                    </form>
                @endauth
            </div>
        </div>
    </div>
</nav>

<div class="rainbow-bar"></div>