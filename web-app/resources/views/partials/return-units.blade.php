<div id="returnUnitsMap" style="height: 420px; width: 100%;"></div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>

<script>
    const returnUnits = @json($returnUnitsForMap);

    if (returnUnits.length > 0) {
        const map = L.map('returnUnitsMap').setView([returnUnits[0].lat, returnUnits[0].lng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19,
        }).addTo(map);

        const bounds = [];

        returnUnits.forEach((unit) => {
            const mapsUrl = 'https://www.google.com/maps/search/?api=1&query=' + unit.lat + ',' + unit.lng;

            L.marker([unit.lat, unit.lng]).addTo(map).bindPopup(
                '<strong>' + unit.name + '</strong><br>' +
                (unit.description ? unit.description + '<br>' : '') +
                'ของที่อยู่ที่นี่: ' + unit.items_count + ' ชิ้น<br>' +
                '<a href="' + unit.show_url + '">ดูรายการของ</a> / ' +
                '<a href="' + mapsUrl + '" target="_blank" rel="noopener">เปิดใน Google Maps</a>'
            );

            bounds.push([unit.lat, unit.lng]);
        });

        if (bounds.length > 1) {
            map.fitBounds(bounds, { padding: [30, 30] });
        }
    }
</script>