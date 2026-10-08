@extends('layouts.site')

@section('title', 'ค้นหาของหาย')

@section('intro')
    @include('partials.intro')
@endsection

@section('content')

    <h2><strong>ค้นหาของหาย</strong></h2>

    <form id="searchForm" method="GET" action="{{ route('search.home') }}" onsubmit="document.getElementById('loadingMessage').style.display='block'; document.getElementById('searchBtn').disabled=true;">

        <input type="hidden" name="searched" value="1">

        <label for="type">ประเภทการแจ้ง:</label><br>
        <input type="radio" name="type" value="" {{ $type === '' ? 'checked' : '' }}>ทั้งหมด
        <input type="radio" name="type" value="found" {{ $type === 'found' ? 'checked' : '' }}>พบของ
        <input type="radio" name="type" value="lost" {{ $type === 'lost' ? 'checked' : '' }}>ของหาย
        <br><br>

        <label for="item_name">ชื่อสิ่งของ: </label><br>
        <input type="text" id="item_name" name="item_name" placeholder="เช่น กระเป๋าตังค์สีน้ำตาล" value="{{ $item_name }}"><br><br>

        <label for="category">หมวดหมู่:</label>
        <select id="category" name="category">
            <option value="" {{ $category === '' ? 'selected' : '' }}>-- เลือกหมวดหมู่ --</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" {{ (string) $category === (string) $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
        <br><br>

        <label for="location">สถานที่หาย/สถานที่พบ:</label>
        <br>
        <input type="text" id="location" name="location" list="locationList" placeholder="เช่น อาคารวิทยวิภาส" value="{{ $location }}">
        <!-- <datalist> เป็น element ของ HTML5 ที่ทำ autocomplete ให้กับ <input> -->
        <datalist id="locationList">
            @foreach ($locations as $loc)
                <option value="{{ $loc }}">
            @endforeach
        </datalist>
        <br><br>

        <label>ช่วงวันที่พบ/วันที่หาย: </label><br>
        ตั้งแต่ <input type="date" name="start_date" id="start_date" value="{{ $start_date }}">
        ถึง <input type="date" name="end_date" id="end_date" value="{{ $end_date }}"><br><br>

        <label for="status">สถานะ:</label>
        <select id="status" name="status">
            <option value="" {{ $status === '' ? 'selected' : '' }}>-- ทั้งหมด --</option>
            @foreach ($statuses as $st)
                <option value="{{ $st }}" {{ $status === $st ? 'selected' : '' }}>{{ $st }}</option>
            @endforeach
        </select>
        <br><br>
        
        <button type="submit" id="searchBtn" class="btn">ค้นหา</button>
        <a href="{{ route('search.home') }}"><button type="button">ล้างคำค้นหา</button></a>
    </form>

    <div id="loadingMessage" style="display: none;">
        <p>กำลังค้นหา กรุณารอสักครู่...</p>
    </div>

    @if (count($history) > 0)
        <h3><strong>ประวัติการค้นหา</strong></h3>
        <form action="{{ route('search.history.clear') }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit">ลบประวัติทั้งหมด</button>
        </form>
        <ul>
            @foreach ($history as $i => $h)
                <li>
                    <a href="{{ $h['url'] }}">{{ $h['label'] }}</a>
                    <form action="{{ route('search.history.clear-one', $i) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit">ลบ</button>
                    </form>
                </li>
            @endforeach
        </ul>
    @endif

    <br>

    @if ($searched)
    <div id="resultsContainer">
        <h3><strong>ผลการค้นหา:</strong></h3>

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
            <tbody id="searchResultsBody" align="center">
                @forelse ($items as $item)
                    <tr>
                        <td>{{ $item->title }}</td>
                        <td style="text-align: center;">
                            @if ($item->image_url)
                                <img src="{{ asset($item->image_url) }}" alt="{{ $item->title }}" width="100">
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $item->category?->name ?? 'อื่นๆ' }}</td>
                        <td>{{ $item->location }}</td>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->event_date }}</td>
                        <td>{{ $item->status }}</td>
                        <td style="text-align: center;">
                            <a href="{{ route('item.show', $item->id) }}"><button type="button">More</button></a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center;">ไม่พบสิ่งของที่ตรงกับเงื่อนไขการค้นหา</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div>
            {{ $items->links('partials.pagination') }}
        </div>
    </div>
    @endif

@endsection