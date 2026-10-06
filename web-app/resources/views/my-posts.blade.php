@extends('layouts.site')

@section('title', 'โพสต์ของฉัน')

@section('content')
    <h2><strong>โพสต์ของฉัน</strong></h2>

    @if (session('success'))
        <p><strong>{{ session('success') }}</strong></p>
    @endif
    @if (session('error'))
    <p><strong>{{ session('error') }}</strong></p>
    @endif

    <table border="1" cellspacing="2" cellpadding="0">
        <thead>
            <tr>
                <th>ชื่อสิ่งของ</th>
                <th>ประเภท</th>
                <th>หมวดหมู่</th>
                <th>สถานที่</th>
                <th>วันที่พบ/หาย</th>
                <th>สถานะ</th>
                <th>สถานะอนุมัติ</th>
                <th>จัดการ</th>
            </tr>
        </thead>
        <tbody align="center">
            @forelse ($items as $item)
                <tr>
                    <td>{{ $item->title }}</td>
                    <td>{{ $item->type === 'found' ? 'พบของ' : 'ของหาย' }}</td>
                    <td>{{ $item->category ? $item->category->name : 'อื่นๆ' }}</td>
                    <td>{{ $item->location }}</td>
                    <td>{{ $item->event_date }}</td>
                    <td>{{ $item->status }}</td>
                    <td>{{ $item->approval_status }}</td>
                    <td><a href="{{ route('posts.edit', $item->id) }}">แก้ไข</a></td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">ยังไม่มีโพสต์ของคุณ</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection