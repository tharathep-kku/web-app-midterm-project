<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div>
            <h2 class="text-lg font-semibold">จุดรับ-ส่งคืนของภายในมข.</h2>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">
                แต่ละหมุดคือหน่วยงานที่รับฝาก/แจกจ่ายของหาย — กดที่หมุดหรือรายการด้านล่างเพื่อดูของที่อยู่ที่หน่วยงานนั้น
            </p>
        </div>

        <div
            id="returnUnitsMap"
            class="relative min-h-[420px] w-full overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700"
        ></div>

        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            @foreach ($returnUnits as $unit)
                <a
                    href="{{ route('return-units.show', $unit->id) }}"
                    class="block rounded-xl border border-neutral-200 p-4 hover:border-neutral-400 dark:border-neutral-700 dark:hover:border-neutral-500"
                >
                    <div class="font-medium">{{ $unit->name }}</div>
                    <div class="text-sm text-neutral-500 dark:text-neutral-400">{{ $unit->description }}</div>
                    <div class="mt-1 text-sm">ของที่อยู่ที่นี่: {{ $unit->items_count }} ชิ้น</div>
                </a>
            @endforeach
        </div>
    </div>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css"
        data-navigate-once
    >
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"
        data-navigate-once
    ></script>

    <script>
        (function () {
            const returnUnits = @json($returnUnitsForMap);

            let mapInstance = null;

            function initReturnUnitsMap() {
                const container = document.getElementById('returnUnitsMap');
                if (!container || typeof L === 'undefined' || returnUnits.length === 0) {
                    return;
                }

                // ป้องกันการสร้างแผนที่ซ้ำตอนสลับหน้าไปมาด้วย wire:navigate
                if (mapInstance) {
                    mapInstance.remove();
                    mapInstance = null;
                }

                mapInstance = L.map(container).setView([returnUnits[0].lat, returnUnits[0].lng], 15);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors',
                    maxZoom: 19,
                }).addTo(mapInstance);

                const redDotIcon = L.divIcon({
                    className: '',
                    html: '<div style="width:16px;height:16px;border-radius:50%;background:#e11d48;border:2px solid white;box-shadow:0 0 2px rgba(0,0,0,0.5);"></div>',
                    iconSize: [16, 16],
                    iconAnchor: [8, 8],
                });

                const bounds = [];

                returnUnits.forEach((unit) => {
                    const marker = L.marker([unit.lat, unit.lng], { icon: redDotIcon }).addTo(mapInstance);
                    const mapsUrl = 'https://www.google.com/maps/search/?api=1&query=' + unit.lat + ',' + unit.lng;

                    marker.bindPopup(
                        '<div style="min-width:180px;">' +
                            '<strong>' + unit.name + '</strong><br>' +
                            (unit.description ? unit.description + '<br>' : '') +
                            'ของที่อยู่ที่นี่: ' + unit.items_count + ' ชิ้น<br>' +
                            '<a href="' + unit.show_url + '">ดูรายการของ</a> &middot; ' +
                            '<a href="' + mapsUrl + '" target="_blank" rel="noopener">เปิดใน Google Maps</a>' +
                        '</div>'
                    );

                    bounds.push([unit.lat, unit.lng]);
                });

                if (bounds.length > 1) {
                    mapInstance.fitBounds(bounds, { padding: [30, 30] });
                }
            }

            initReturnUnitsMap();
            document.addEventListener('livewire:navigated', initReturnUnitsMap);
        })();
    </script>
</x-layouts::app>
