@extends('layouts.site')

@section('title', $item->title)

@section('intro')
    @include('partials.intro')
@endsection

@section('content')

    <h2><strong>รายละเอียด</strong></h2>

    <div style="margin-bottom: 20px;">
        @if ($item->image_url)
            <img src="{{ asset($item->image_url) }}" alt="{{ $item->title }}" width="200">
        @else
            <b>[ยังไม่มีรูปภาพประกอบ]</b>
        @endif
    </div>

    <ul>
        <li><b>ชื่อสิ่งของ:</b> {{ $item->title }}</li>
        <li><b>หมวดหมู่:</b> {{ $item->category?->name ?? 'อื่นๆ' }}</li>
        <li><b>สถานที่:</b> {{ $item->location }}</li>
        <li><b>วันที่:</b> {{ $item->event_date }}</li>
        <li><b>สถานะ:</b> {{ $item->status }}</li>
        <li><b>รายละเอียดเพิ่มเติม:</b> {{ $item->description ?: 'ไม่มีรายละเอียดเพิ่มเติม' }}</li>
    </ul>

    <h3>ข้อมูลการติดต่อ</h3>
    <ul>
        <li><b>ผู้แจ้ง:</b> {{ $item->reporter?->fullname ?? 'ไม่ทราบชื่อ' }}</li>
        <li><b>อีเมลติดต่อกลับ:</b> {{ $item->reporter?->email ?? 'ไม่ได้ระบุอีเมล' }}</li>
        <li><b>เบอร์โทรศัพท์ติดต่อกลับ:</b> {{ $item->reporter?->phone ?? 'ไม่ได้ระบุเบอร์โทรศัพท์' }}</li>
    </ul>

    <h3>จุดรับ-ส่งคืน</h3>
    @if ($item->returnUnit)
        <p><b>{{ $item->returnUnit->name }}</b> — {{ $item->returnUnit->description }}</p>

        <iframe
            width="100%"
            height="350"
            style="border:0; max-width: 600px;"
            loading="lazy"
            src="https://www.google.com/maps?q={{ $item->returnUnit->latitude }},{{ $item->returnUnit->longitude }}&z=16&output=embed">
        </iframe>
        <br><br>

        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $item->returnUnit->latitude }},{{ $item->returnUnit->longitude }}" target="_blank" rel="noopener">
            <button type="button">นำทางไปด้วย Google Maps</button>
        </a>
    @else
        <p>ยังไม่ได้ระบุจุดรับ-ส่งคืนสำหรับรายการนี้</p>
    @endif

    <br>
    <a href="{{ route('search.home') }}"><button type="button">กลับไปหน้าค้นหา</button></a>

@endsection