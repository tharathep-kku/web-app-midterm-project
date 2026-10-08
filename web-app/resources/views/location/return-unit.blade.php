@extends('layouts.site')

@section('title', $returnUnit->name)

@section('intro')
    @include('partials.intro')
@endsection

@section('content')

    <h2><strong>{{ $returnUnit->name }}</strong></h2>
    <p>{{ $returnUnit->description }}</p>

    <iframe
        width="100%"
        height="300"
        style="border:0; max-width: 600px;"
        loading="lazy"
        src="https://www.google.com/maps?q={{ $returnUnit->latitude }},{{ $returnUnit->longitude }}&z=16&output=embed">
    </iframe>
    <br><br>

    <a href="https://www.google.com/maps/dir/?api=1&destination={{ $returnUnit->latitude }},{{ $returnUnit->longitude }}" target="_blank" rel="noopener">
        <button type="button">นำทางไปด้วย Google Maps</button>
    </a>

    <br><br>
    <hr>

    @forelse ($itemsByStatus as $status => $items)
        <h3>{{ $status }} ({{ $items->count() }})</h3>
        <table border="1" cellspacing="2" cellpadding="4">
            <thead>
                <tr>
                    <th>สิ่งของ</th>
                    <th>ภาพ</th>
                    <th>หมวดหมู่</th>
                    <th>วันที่พบ</th>
                    <th>รายละเอียด</th>
                    <th>ติดต่อ</th>
                </tr>
            </thead>
            <tbody align="center">
                @foreach ($items as $item)
                    <tr>
                        <td>{{ $item->title }}</td>
                        <td>
                            @if ($item->image_url)
                                <img src="{{ asset($item->image_url) }}" alt="{{ $item->title }}" width="80">
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $item->category?->name ?? 'อื่นๆ' }}</td>
                        <td>{{ $item->event_date }}</td>
                        <td>{{ $item->description }}</td>
                        <td><a href="{{ route('item.show', $item->id) }}"><button type="button">More</button></a></td>
                        
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
    @empty
        <p>ยังไม่มีของที่จุดรับ-ส่งคืนนี้</p>
    @endforelse

    <br>
    <a href="{{ route('location') }}"><button type="button">กลับไปหน้าจุดรับ-ส่งคืนของ</button></a>

@endsection
