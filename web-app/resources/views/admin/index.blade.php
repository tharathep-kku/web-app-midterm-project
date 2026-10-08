@extends('layouts.site')

@section('title', 'ข้อมูลหลังบ้าน')

@section('note', '*หมายเหตุ: การอนุมัติจะบันทึกชื่อแอดมินและเวลาที่ตรวจสอบไว้ทุกครั้ง เพื่อให้ตรวจสอบย้อนหลังได้')

@section('intro')
    @include('partials.intro')
@endsection

@section('content')
    <p>ส่วนของแอดมิน สำหรับอนุมัติโพสต์และดูข้อมูลหลังบ้านทั้งหมด</p>

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

    @if ($admin)
        <p>เข้าสู่ระบบในนาม: <strong>{{ $admin->name }}</strong> ({{ $admin->email }})</p>
    @else
        <p>บัญชีนี้ยังไม่ได้ผูกกับข้อมูลแอดมิน</p>
    @endif

    <hr>

    @if ($admin)
        <h2><strong>รายการรอยืนยันการส่งมอบ</strong></h2>
        <p style="color: red;">*รายการที่มีผู้แจ้งว่าส่งมอบของแล้ว รอแอดมินตรวจสอบหลักฐาน</p>

        <table border="1" cellspacing="2" cellpadding="0">
            <thead>
                <tr>
                    <th>รหัส</th>
                    <th>สิ่งของ</th>
                    <th>ชื่อผู้แจ้ง</th>
                    <th>หลักฐาน</th>
                    <th>สถานะปัจจุบัน</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody align="center">
                @forelse ($handovers as $handover)
                    <tr>
                        <td>{{ $handover->id }}</td>
                        <td>{{ $handover->title }}</td>
                        <td>{{ $handover->reporter ? $handover->reporter->name : ($handover->reporter_name ?? 'ไม่ทราบชื่อ') }}</td>
                        <td>
                            @if ($handover->evidence_url)
                                <img src="{{ asset($handover->evidence_url) }}" alt="หลักฐานของ {{ $handover->title }}" width="80"><br>
                            @endif

                            @if ($handover->evidence_note)
                                {{ $handover->evidence_note }}
                            @endif

                            {{-- ไม่มีทั้งรูปและหมายเหตุ ถึงจะถือว่าไม่มีหลักฐาน --}}
                            @if (! $handover->evidence_url && ! $handover->evidence_note)
                                ไม่มีหลักฐานแนบ
                            @endif
                        </td>
                        <td>{{ $handover->status }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.handover.confirm', $handover->id) }}"
                                onsubmit="return confirm('ตรวจสอบหลักฐานแล้ว ต้องการยืนยันว่าได้รับของแล้วใช่หรือไม่?');">
                                @csrf
                                @method('PUT')
                                <button type="submit">อนุมัติ</button>
                            </form>

                            <form method="POST" action="{{ route('admin.handover.reject', $handover->id) }}"
                                onsubmit="return confirm('ต้องการปฏิเสธหลักฐานนี้ใช่หรือไม่? (ผู้ใช้ต้องส่งหลักฐานใหม่)');">
                                @csrf
                                @method('PUT')
                                <button type="submit">ปฏิเสธ</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">ไม่มีรายการรอยืนยันการส่งมอบ</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <br>
        <hr>

        <h2><strong>ฐานข้อมูลผู้ใช้งาน</strong></h2>
        <p style="color: red;">* ข้อมูลส่วนบุคคล (เปิดเผยเฉพาะแอดมิน)</p>

        <table border="1" cellspacing="2" cellpadding="0">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>ชื่อ-นามสกุลจริง</th>
                    <th>อีเมล</th>
                    <th>เบอร์โทรศัพท์</th>
                    <th>วันที่สมัคร</th>
                    <th>สถานะบัญชี</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody align="center">
                @forelse ($users as $u)
                    <tr>
                        <td>U-{{ str_pad($u->id, 3, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $u->name }}</td>
                        <td>{{ $u->email }}</td>
                        <td>{{ $u->phone ?? '-' }}</td>
                        <td>{{ $u->created_at ? $u->created_at->format('d/m/Y') : '-' }}</td>
                        <td>{{ $u->is_banned ? 'ถูกระงับ' : 'ปกติ' }}</td>
                        <td>
                            @if ($u->role === 'admin')
                                แอดมิน
                            @else
                                <form method="POST" action="{{ route('admin.users.ban', $u->id) }}"
                                    onsubmit="return confirm('{{ $u->is_banned ? 'ปลดแบนบัญชีนี้?' : 'ระงับการใช้งานบัญชีนี้?' }}');">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit">{{ $u->is_banned ? 'ปลดการระงับบัญชี' : 'ระงับบัญชี' }}</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">ยังไม่มีผู้ใช้งาน</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <br>
        <hr>

        <h2><strong>โพสต์ทั้งหมด</strong></h2>

        <form method="GET" action="{{ route('admin.index') }}">
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
            <input type="text" id="location" name="location" list="adminLocationList" value="{{ $location }}">
            <datalist id="adminLocationList">
                @foreach ($locations as $loc)
                    <option value="{{ $loc }}">
                @endforeach
            </datalist>

            <label for="reporter">ชื่อผู้โพสต์:</label>
            <input type="text" id="reporter" name="reporter" value="{{ $reporter }}">
            <br><br>

            <label for="start_date">วันที่พบ/หาย ตั้งแต่:</label>
            <input type="date" id="start_date" name="start_date" value="{{ $start_date }}">

            <label for="end_date">ถึง:</label>
            <input type="date" id="end_date" name="end_date" value="{{ $end_date }}">

            <button type="submit">กรองข้อมูล</button>
            <a href="{{ route('admin.index') }}"><button type="button">ล้างตัวกรอง</button></a>
        </form>

        <br>

        <table border="1" cellspacing="2" cellpadding="0">
            <thead>
                <tr>
                    <th>รหัส</th>
                    <th>สิ่งของ</th>
                    <th>ประเภท</th>
                    <th>หมวดหมู่</th>
                    <th>ผู้โพสต์</th>
                    <th>สถานที่</th>
                    <th>วันที่พบ/หาย</th>
                    <th>หลักฐานยืนยัน</th>
                    <th>สถานะ</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody align="center">
                @forelse ($items as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->title }}</td>
                        <td>{{ $item->type === 'found' ? 'พบของ' : 'ของหาย' }}</td>
                        <td>{{ $item->category_name }}</td>
                        <td>{{ $item->owner_name }}</td>
                        <td>{{ $item->location }}</td>
                        <td>{{ $item->event_date }}</td>
                        <td>
                            @if ($item->evidence_url)
                                <img src="{{ asset($item->evidence_url) }}" alt="หลักฐานของ {{ $item->title }}" width="80"><br>
                            @endif

                            @if ($item->evidence_note)
                                {{ $item->evidence_note }}
                            @endif

                            {{-- ไม่มีทั้งรูปและหมายเหตุ ถึงจะถือว่าไม่มีหลักฐาน --}}
                            @if (! $item->evidence_url && ! $item->evidence_note)
                                ไม่มีหลักฐานแนบ
                            @endif
                        </td>
                        <td>{{ $item->status }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.destroy', $item->id) }}"
                                onsubmit="return confirm('ลบโพสต์นี้ออกจากระบบ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit">ลบโพสต์</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10">ไม่พบโพสต์ที่ตรงกับเงื่อนไข</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div>
            {{ $items->links('partials.pagination') }}
        </div>
    @endif

@endsection
