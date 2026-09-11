@props([
    'latitude' => -6.792354, 
    'longitude' => 39.208328, 
    'height' => '300px', 
    'zoom' => 15,
    'mapId' => null
])

@php
    $uniqueMapId = $mapId ?? 'map-' . uniqid();
@endphp

<div 
    x-data="{
        map: null,
        marker: null,
        initMap() {
            // Pakia Leaflet CSS kama haipo
            if (!document.getElementById('leaflet-css')) {
                const link = document.createElement('link');
                link.id = 'leaflet-css';
                link.rel = 'stylesheet';
                link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                link.integrity = 'sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=';
                link.crossOrigin = '';
                document.head.appendChild(link);
            }
            
            // Pakia Leaflet JS kama haipo
            const loadLeaflet = () => {
                if (typeof L !== 'undefined') {
                    this.createMap();
                } else {
                    const script = document.createElement('script');
                    script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                    script.integrity = 'sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=';
                    script.crossOrigin = '';
                    script.onload = () => this.createMap();
                    document.head.appendChild(script);
                }
            };
            
            loadLeaflet();
        },
        createMap() {
            if (this.map) return;
            
            const lat = {{ $latitude }};
            const lng = {{ $longitude }};
            
            // Tengeneza ramani
            this.map = L.map('{{ $uniqueMapId }}').setView([lat, lng], {{ $zoom }});
            
            // Ongeza tiles za OpenStreetMap (BURE)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href=\'https://www.openstreetmap.org/copyright\'>OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(this.map);
            
            // Weka alama kwenye eneo husika
            this.marker = L.marker([lat, lng]).addTo(this.map);
            
            // Ongeza popup yenye maelezo
            this.marker.bindPopup(`
                <strong>Mahali Hapa</strong><br>
                Lat: ${lat.toFixed(6)}<br>
                Lng: ${lng.toFixed(6)}
            `).openPopup();
        }
    }"
    x-init="initMap()"
    class="map-wrapper"
>
    <div 
        id="{{ $uniqueMapId }}" 
        class="w-100 h-100 rounded-3 shadow-sm" 
        style="height: {{ $height }}; min-height: 200px; background: #e9ecef;"
    ></div>
    
    {{-- Maelezo ya chini (hiari) --}}
    <div class="text-muted small mt-2 d-flex align-items-center gap-2">
        <i class="bi bi-geo-alt-fill text-danger"></i>
        <span>Lat: {{ number_format($latitude, 6) }}, Lng: {{ number_format($longitude, 6) }}</span>
        <span class="ms-auto">
            <i class="bi bi-info-circle"></i> Buruta ili kuzungusha, scroll ili kuvuta karibu
        </span>
    </div>
</div>

{{-- Inahakikisha Alpine.js imepakiwa (inahitajika kwa mwingiliano) --}}
@once
    @push('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @endpush
@endonce