@extends('layouts.site')

@section('title', 'หน้าแรก')

@section('intro')
    @include('partials.intro')
@endsection

@section('content')
    <div class="row align-items-center g-4 mb-4">
        <div class="col-lg-7">
            <h2 class="fw-bold mb-2">ศูนย์รวมแจ้งของหาย – พบของ ม.ขอนแก่น</h2>
            <p class="text-secondary">ปลอดภัย ตรวจสอบได้ มีหลักฐานยืนยันการส่งมอบทุกครั้ง</p>

            <form method="GET" action="{{ route('search.home') }}" class="d-flex gap-2 mb-3">
                <input type="hidden" name="searched" value="1">
                <input type="text" name="item_name" class="form-control rounded-pill"
                       placeholder="เช่น กระเป๋าตังค์สีน้ำตาล">
                <button type="submit" class="btn btn-primary btn-pop px-4">ค้นหา</button>
            </form>

            <a href="{{ route('posts.create') }}" class="btn btn-danger btn-pop">ฉันทำของหาย</a>
            <a href="{{ route('posts.create') }}" class="btn btn-primary btn-pop">ฉันเก็บของได้</a>
        </div>

        <div class="col-lg-5">
            <div class="row g-2 text-center">
                <div class="col-6">
                    <div class="p-3 rounded-4" style="background:rgba(66,141,255,.15);">
                        <div class="fs-3 fw-bold" style="font-family:'Baloo 2',sans-serif;">{{ $totalItem }}</div>
                        <small>ประกาศทั้งหมด</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 rounded-4" style="background:rgba(252,185,94,.25);">
                        <div class="fs-3 fw-bold" style="font-family:'Baloo 2',sans-serif;">{{ $waitingOwner }}</div>
                        <small>ยังไม่พบเจ้าของ</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 rounded-4" style="background:rgba(174,211,137,.3);">
                        <div class="fs-3 fw-bold" style="font-family:'Baloo 2',sans-serif;">{{ $returnedItem }}</div>
                        <small>ได้รับคืนแล้ว</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 rounded-4" style="background:rgba(94,151,211,.2);">
                        <div class="fs-3 fw-bold" style="font-family:'Baloo 2',sans-serif;">{{ $waitingConfirm }}</div>
                        <small>รอแอดมินยืนยัน</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
        <span class="text-secondary small">ประเภท:</span>
        <a href="{{ route('home') }}" class="btn btn-sm rounded-pill {{ $type === '' ? 'btn-primary' : 'btn-outline-secondary' }}">ทั้งหมด</a>
        <a href="{{ route('home', ['type' => 'found']) }}" class="btn btn-sm rounded-pill {{ $type === 'found' ? 'btn-primary' : 'btn-outline-secondary' }}">พบของ</a>
        <a href="{{ route('home', ['type' => 'lost']) }}" class="btn btn-sm rounded-pill {{ $type === 'lost' ? 'btn-danger' : 'btn-outline-secondary' }}">ของหาย</a>
    </div>

    @php
        $d7 = date('Y-m-d', strtotime('-7 days'));
        $d30 = date('Y-m-d', strtotime('-30 days'));
        $d3m = date('Y-m-d', strtotime('-3 months'));
        $today = date('Y-m-d');
    @endphp

    <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
        <span class="text-secondary small">ช่วงเวลา:</span>
        <a href="{{ route('home') }}" class="btn btn-sm rounded-pill {{ $from === '' ? 'btn-primary' : 'btn-outline-secondary' }}">ทั้งหมด</a>
        <a href="{{ route('home', ['from' => $d7, 'to' => $today]) }}" class="btn btn-sm rounded-pill {{ $from === $d7 ? 'btn-primary' : 'btn-outline-secondary' }}">7 วันล่าสุด</a>
        <a href="{{ route('home', ['from' => $d30, 'to' => $today]) }}" class="btn btn-sm rounded-pill {{ $from === $d30 ? 'btn-primary' : 'btn-outline-secondary' }}">30 วันล่าสุด</a>
        <a href="{{ route('home', ['from' => $d3m, 'to' => $today]) }}" class="btn btn-sm rounded-pill {{ $from === $d3m ? 'btn-primary' : 'btn-outline-secondary' }}">3 เดือนล่าสุด</a>
    </div>

    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
        <span class="text-secondary small">เรียงตาม:</span>
        <a href="{{ route('home', array_merge(request()->query(), ['sort' => 'new', 'page' => 1])) }}" class="btn btn-sm rounded-pill {{ $sort !== 'old' ? 'btn-primary' : 'btn-outline-secondary' }}">ใหม่สุด</a>
        <a href="{{ route('home', array_merge(request()->query(), ['sort' => 'old', 'page' => 1])) }}" class="btn btn-sm rounded-pill {{ $sort === 'old' ? 'btn-primary' : 'btn-outline-secondary' }}">เก่าสุด</a>
    </div>

    <!-- ---------- จำนวนผลลัพธ์ ---------- -->
    <p aria-live="polite">
        พบ {{ $items->total() }} รายการที่ยังไม่เข้าคลัง
        @if ($from !== '' || $to !== '')
            ระหว่าง
            {{ $from !== '' ? date('d/m/Y', strtotime($from)) : 'เริ่มต้น' }}
            ถึง
            {{ $to !== '' ? date('d/m/Y', strtotime($to)) : 'ปัจจุบัน' }}
        @endif
        : สำหรับของที่ได้รับการคืนแล้วจะถูกเก็บเข้าคลังภายใน 7 วันหลังจากเจ้าของได้รับคืน
    </p>

    <div class="table-responsive">
    <table class="table table-hover align-middle table-kku">
        <thead>
            <tr>
                <th>สิ่งของ</th>
                <th>ภาพของหาย</th>
                <th>ประเภท</th>
                <th>หมวดหมู่</th>
                <th>สถานที่</th>
                <th>รายละเอียดเพิ่มเติม</th>
                <th>วันที่พบ/หาย</th>
                <th>สถานะ</th>
                <th>ติดต่อ</th>
            </tr>
        </thead>
        <tbody align="center">
            @forelse ($items as $item)
                <tr>
                    <td class="text-start">{{ $item->title }}</td>
                    <td>
                        @if ($item->image_url)
                            <img src="{{ asset($item->image_url) }}" alt="{{ $item->title }}" width="100">
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $item->type === 'found' ? 'พบของ' : 'ของหาย' }}</td>
                    <td>{{ $item->category_name }}</td>
                    <td class="text-start">{{ $item->location }}</td>
                    <td class="text-start">{{ $item->description }}</td>
                    <td>{{ $item->event_date }}</td>
                    <td>{{ $item->status }}</td>
                    <td>
                        <a href="{{ route('item.show', $item->id) }}"><button type="button" class="btn btn-sm btn-outline-primary">More</button></a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">
                        ไม่มีประกาศในช่วงวันที่ที่เลือก —
                        <a href="{{ route('home', ['from' => date('Y-m-d', strtotime('-30 days')), 'to' => date('Y-m-d')]) }}">ลองขยายเป็น 30 วันล่าสุด</a>
                        หรือ <a href="{{ route('posts.create') }}">ลงประกาศตามหาของ</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>

    <div>
        {{ $items->links('partials.pagination') }}
    </div>

    <p><a href="{{ route('archive.home') }}">ดูรายการที่เก็บเข้าคลังแล้ว</a></p>
    <hr>

    <p>
        ไม่เจอของที่คุณตามหาใช่ไหม? ลงประกาศไว้ เผื่อมีคนเก็บได้แล้วนำมาคืน
        <br>
        <a href="{{ route('posts.create') }}"><button type="button" class="btn btn-primary btn-pop">แจ้งของหาย / แจ้งพบของ</button></a>
    </p>

    <hr>

    <h2><strong>จุดรับ-ส่งคืนของ</strong></h2>
    <p>นำของที่เก็บได้ไปฝาก หรือไปรับของคืนได้ที่จุดเหล่านี้</p>
    @include('partials.return-units')

@endsection
