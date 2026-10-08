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
        <p>เข้าสู่ระบบในนาม: <strong>{{ $admin->fullname }}</strong> ({{ $admin->email }})</p>
    @else
        <p>บัญชีนี้ยังไม่ได้ผูกกับข้อมูลแอดมิน</p>
    @endif

    <hr>

    @if ($admin)
        <h2><strong>รายการรอยืนยันการส่งมอบ</strong></h2>
        <p>รายการที่มีผู้แจ้งว่าส่งมอบของแล้ว รอแอดมินตรวจหลักฐาน</p>

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
                        <td>{{ $handover->reporter ? $handover->reporter->fullname : ($handover->reporter_name ?? 'ไม่ทราบชื่อ') }}</td>
                        <td>
                            @if ($handover->evidence_url)
                                <img src="{{ asset($handover->evidence_url) }}" alt="หลักฐานของ {{ $handover->title }}" width="80"><br>
                            @endif
                            {{ $handover->evidence_note ? $handover->evidence_note : 'ไม่มีหลักฐานแนบ' }}
                        </td>
                        <td>{{ $handover->status }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.handover.confirm', $handover->id) }}"
                                onsubmit="return confirm('ตรวจสอบหลักฐานแล้ว ต้องการยืนยันว่าได้รับของแล้วใช่หรือไม่?');">
                                @csrf
                                @method('PUT')
                                <button type="submit">ยืนยัน</button>
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

        <h2><strong>ข้อมูลหลังบ้าน: โพสต์ทั้งหมด</strong></h2>

        <form method="GET" action="{{ route('admin.index') }}">
            <label for="approval_status">สถานะอนุมัติ:</label>
            <select id="approval_status" name="approval_status">
                <option value="" {{ $approval_status === '' ? 'selected' : '' }}>-- ทั้งหมด --</option>
                <option value="รออนุมัติ" {{ $approval_status === 'รออนุมัติ' ? 'selected' : '' }}>รออนุมัติ</option>
                <option value="อนุมัติแล้ว" {{ $approval_status === 'อนุมัติแล้ว' ? 'selected' : '' }}>อนุมัติแล้ว</option>
                <option value="ไม่อนุมัติ" {{ $approval_status === 'ไม่อนุมัติ' ? 'selected' : '' }}>ไม่อนุมัติ</option>
            </select>

            <label for="type">ประเภท:</label>
            <select id="type" name="type">
                <option value="" {{ $type === '' ? 'selected' : '' }}>-- ทั้งหมด --</option>
                <option value="found" {{ $type === 'found' ? 'selected' : '' }}>พบของ</option>
                <option value="lost" {{ $type === 'lost' ? 'selected' : '' }}>ของหาย</option>
            </select>

            <label for="keyword">ชื่อสิ่งของ:</label>
            <input type="text" id="keyword" name="keyword" value="{{ $keyword }}">

            <button type="submit">กรองข้อมูล</button>
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
                    <th>สถานที่ / จุดสถานที่</th>
                    <th>วันที่พบ/หาย</th>
                    <th>หลักฐานยืนยัน</th>
                    <th>สถานะอนุมัติ</th>
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
                        <td>{{ $item->owner_name }}<br>({{ $item->owner_role }})</td>
                        <td>{{ $item->location }}<br>{{ $item->place_point ? $item->place_point : '-' }}</td>
                        <td>{{ $item->event_date }}</td>
                        <td>
                            @if ($item->evidence_url)
                                <img src="{{ asset($item->evidence_url) }}" alt="หลักฐานของ {{ $item->title }}" width="80"><br>
                            @endif
                            {{ $item->evidence_note ? $item->evidence_note : 'ไม่มีหลักฐานแนบ' }}
                        </td>
                        <td>
                            {{ $item->approval_status }}
                            @if ($item->reject_reason)
                                <br>({{ $item->reject_reason }})
                            @endif
                        </td>
                        <td>
                        {{-- แสดงปุ่มเฉพาะโพสต์ที่ยังไม่ได้ตรวจ ตรวจแล้วกดซ้ำไม่ได้ --}}
                        @if ($item->approval_status === 'รออนุมัติ')
                            <form method="POST" action="{{ route('admin.approve', $item->id) }}"
                                onsubmit="return confirm('ยืนยันการอนุมัติโพสต์นี้หรือไม่?');">
                                @csrf
                                @method('PUT')
                                <button type="submit">อนุมัติ</button>
                            </form>

                            <form method="POST" action="{{ route('admin.reject', $item->id) }}"
                                onsubmit="return confirm('ยืนยันการไม่อนุมัติโพสต์นี้หรือไม่?');">
                                @csrf
                                @method('PUT')
                                <input type="text" name="reject_reason" placeholder="เหตุผล" required>
                                <button type="submit">ไม่อนุมัติ</button>
                            </form>
                        @else
                            ดำเนินการเรียบร้อย
                        @endif
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
