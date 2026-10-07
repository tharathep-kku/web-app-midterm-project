<ul>
    @forelse ($returnUnits as $unit)
        <li>
            <a href="{{ route('return-units.show', $unit->id) }}">{{ $unit->name }}</a>
            @if ($unit->description)
                - {{ $unit->description }}
            @endif
            (ของที่อยู่ที่นี่: {{ $unit->items_count }} ชิ้น)
        </li>
    @empty
        <li>ยังไม่มีจุดรับ-ส่งคืน</li>
    @endforelse
</ul>