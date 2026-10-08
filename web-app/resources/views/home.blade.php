@extends('layouts.site')

@section('title', 'หน้าแรก')

@section('intro')
    @include('partials.intro')
@endsection

@section('content')
    <h2>ศูนย์รวมแจ้งของหายและพบของมหาวิทยาลัยขอนแก่น</h2>
    <p>ปลอดภัย ตรวจสอบได้ มีหลักฐานยืนยันการส่งมอบทุกครั้ง</p>
    <p>
        ประกาศทั้งหมด <strong>{{ $totalItem }}</strong> รายการ ·
        ยังไม่พบเจ้าของ <strong>{{ $waitingOwner }}</strong> รายการ ·
        ได้รับคืนแล้ว <strong>{{ $returnedItem }}</strong> รายการ ·
        รอแอดมินยืนยัน <strong>{{ $waitingConfirm }}</strong> รายการ
    </p>

    <!-- ให้ของที่พิมค้นหาเชื่อมไปกับหน้า search -->
    <form method="GET" action="{{ route('search.home') }}">
        <input type="hidden" name="searched" value="1">
        <label for="quick">ค้นหาสิ่งของ:</label>
        <input type="text" id="quick" name="item_name" placeholder="เช่น กระเป๋าตังค์สีน้ำตาล">
        <button type="submit">ค้นหา</button>
        <a href="{{ route('home') }}"><button type="button">ล้างคำค้นหา</button></a>
    </form>

    <!-- กรองพบของกับของหาย -->
    <p>
        ประเภท:
        <a href="{{ route('home') }}">ทั้งหมด</a> ·
        <a href="{{ route('home', ['type' => 'found']) }}">พบของ</a> ·
        <a href="{{ route('home', ['type' => 'lost']) }}">ของหาย</a>
    </p>

    <!-- ปุ่มลัดช่วงเวลา -->
    <p>
        ช่วงเวลา:
        <a href="{{ route('home') }}">ทั้งหมด</a> ·
        <a href="{{ route('home', ['from' => date('Y-m-d', strtotime('-7 days')), 'to' => date('Y-m-d')]) }}">7 วันล่าสุด</a> ·
        <a href="{{ route('home', ['from' => date('Y-m-d', strtotime('-30 days')), 'to' => date('Y-m-d')]) }}">30 วันล่าสุด</a> ·
        <a href="{{ route('home', ['from' => date('Y-m-d', strtotime('-3 months')), 'to' => date('Y-m-d')]) }}">3 เดือนล่าสุด</a>
    </p>
    
    <!-- เก่าสุดใหม่สุด -->
    <p>
        เรียงตาม:
        <a href="{{ route('home', array_merge(request()->query(), ['sort' => 'new', 'page' => 1])) }}">ใหม่สุด</a> ·
        <a href="{{ route('home', array_merge(request()->query(), ['sort' => 'old', 'page' => 1])) }}">เก่าสุด</a>
    </p>

    <!-- ---------- จำนวนผลลัพธ์ ---------- -->
    <p aria-live="polite">
        พบ {{ $items->total() }} รายการที่ยังไม่เข้าคลัง
        @if ($from !== '' || $to !== '')
            ระหว่าง
            {{ $from !== '' ? date('d/m/Y', strtotime($from)) : 'เริ่มต้น' }}
            ถึง
            {{ $to !== '' ? date('d/m/Y', strtotime($to)) : 'ปัจจุบัน' }}
        @endif
    </p>

    <!-- ---------- ตาราง ---------- -->
    <table border="1" cellspacing="2" cellpadding="0">
        <thead>
            <tr>
                <th>สิ่งของ</th>
                <th>ภาพของหาย</th>
                <th>หมวดหมู่</th>
                <th>สถานที่พบ</th>
                <th>รายละเอียด</th>
                <th>วันที่พบ</th>
                <th>สถานะ</th>
                <th>ติดต่อ</th>
            </tr>
        </thead>
        <tbody align="center">
            @forelse ($items as $item)
                <tr>
                    <td>{{ $item->title }}</td>
                    <td>
                        @if ($item->image_url)
                            <img src="{{ asset($item->image_url) }}" alt="{{ $item->title }}" width="100">
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $item->category_name }}</td>
                    <td>{{ $item->location }}</td>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->event_date }}</td>
                    <td>{{ $item->status }}</td>
                    <td>
                        <a href="{{ route('item.show', $item->id) }}"><button type="button">More</button></a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">
                        ไม่มีประกาศในช่วงวันที่ที่เลือก —
                        <a href="{{ route('home', ['from' => date('Y-m-d', strtotime('-30 days')), 'to' => date('Y-m-d')]) }}">ลองขยายเป็น 30 วันล่าสุด</a>
                        หรือ <a href="{{ route('posts.create') }}">ลงประกาศตามหาของ</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div>
        {{ $items->links('partials.pagination') }}
    </div>

    <p><a href="{{ route('archive.home') }}">ดูรายการที่เก็บเข้าคลังแล้ว</a></p>
    <hr>

    <p>
        ไม่เจอของที่คุณตามหาใช่ไหม? ลงประกาศไว้ เผื่อมีคนเก็บได้แล้วนำมาคืน
        <br>
        <a href="{{ route('posts.create') }}"><button type="button">แจ้งของหาย / แจ้งพบของ</button></a>
    </p>

    <hr>

    <h2><strong>จุดรับ-ส่งคืนของ</strong></h2>
    <p>นำของที่เก็บได้ไปฝาก หรือไปรับของคืนได้ที่จุดเหล่านี้</p>
    @include('partials.return-units')

@endsection
