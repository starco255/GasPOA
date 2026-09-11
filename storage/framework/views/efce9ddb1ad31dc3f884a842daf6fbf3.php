<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'latitude' => -6.792354, 
    'longitude' => 39.208328, 
    'height' => '300px', 
    'zoom' => 15,
    'mapId' => null
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'latitude' => -6.792354, 
    'longitude' => 39.208328, 
    'height' => '300px', 
    'zoom' => 15,
    'mapId' => null
]); ?>
<?php foreach (array_filter(([
    'latitude' => -6.792354, 
    'longitude' => 39.208328, 
    'height' => '300px', 
    'zoom' => 15,
    'mapId' => null
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<?php
    $uniqueMapId = $mapId ?? 'map-' . uniqid();
?>

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
            
            const lat = <?php echo e($latitude); ?>;
            const lng = <?php echo e($longitude); ?>;
            
            // Tengeneza ramani
            this.map = L.map('<?php echo e($uniqueMapId); ?>').setView([lat, lng], <?php echo e($zoom); ?>);
            
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
        id="<?php echo e($uniqueMapId); ?>" 
        class="w-100 h-100 rounded-3 shadow-sm" 
        style="height: <?php echo e($height); ?>; min-height: 200px; background: #e9ecef;"
    ></div>
    
    
    <div class="text-muted small mt-2 d-flex align-items-center gap-2">
        <i class="bi bi-geo-alt-fill text-danger"></i>
        <span>Lat: <?php echo e(number_format($latitude, 6)); ?>, Lng: <?php echo e(number_format($longitude, 6)); ?></span>
        <span class="ms-auto">
            <i class="bi bi-info-circle"></i> Buruta ili kuzungusha, scroll ili kuvuta karibu
        </span>
    </div>
</div>


<?php if (! $__env->hasRenderedOnce('7c0ac95e-e0fc-45bb-9e4b-786866a30070')): $__env->markAsRenderedOnce('7c0ac95e-e0fc-45bb-9e4b-786866a30070'); ?>
    <?php $__env->startPush('scripts'); ?>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <?php $__env->stopPush(); ?>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views\components\map-viewer.blade.php ENDPATH**/ ?>