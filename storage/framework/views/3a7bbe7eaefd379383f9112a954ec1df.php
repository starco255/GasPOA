<?php $__env->startSection('title', 'Fuatilia Agizo la Jumla'); ?>
<?php $__env->startSection('page-title', 'Fuatilia Agizo #' . $order->order_number); ?>

<?php $__env->startSection('content'); ?>
<?php
    $wholesalerName = $order->wholesaler->business_name ?? 'Muuzaji Jumla';
    $wholesalerPhone = $order->wholesaler->phone_number ?? 'Haijulikani';
    $wholesalerAddress = $order->wholesaler->physical_address ?? 'Haijabainishwa';
    $statusLabel = [
        'confirmed' => 'Imethibitishwa',
        'processing' => 'Inashughulikiwa',
        'dispatched' => 'Imesafirishwa',
        'delivered' => 'Imekamilika',
        'cancelled' => 'Imefutwa',
    ][$order->status] ?? ucfirst($order->status);

    // ✅ Retailer values with fallback
    $retailerName = $order->retailer->business_name ?? 'Duka Langu';
    $retailerAddr = $retailerAddress ?? $order->retailer->physical_address ?? 'Haijabainishwa';
    $retLat = $retailerLat ?? ($order->retailer->shop_latitude ?? -6.792354);
    $retLng = $retailerLng ?? ($order->retailer->shop_longitude ?? 39.208328);

    // Google Maps directions URL
    $googleMapsUrl = "https://www.google.com/maps/dir/{$wholesalerLat},{$wholesalerLng}/{$retLat},{$retLng}";
?>

<div class="row g-4">
    
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted">Agizo #</span>
                        <span class="fw-bold fs-5"><?php echo e($order->order_number); ?></span>
                        <span class="badge bg-secondary rounded-pill px-3 py-2"><?php echo e($statusLabel); ?></span>
                    </div>
                    <span class="text-muted"><?php echo e($order->created_at->format('d M Y, H:i')); ?></span>
                </div>
            </div>
        </div>
    </div>

    
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold"><i class="bi bi-geo-alt-fill text-danger me-2"></i>Ramani ya Usafirishaji</h5>
                <a href="<?php echo e($googleMapsUrl); ?>" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill" title="Fungua kwenye Google Maps">
                    <i class="bi bi-box-arrow-up-right"></i> Google Maps
                </a>
            </div>
            <div class="card-body p-3">
                <div id="wholesaleTrackingMap" style="height: 320px; border-radius: 12px;"></div>
                
                <div class="mt-2 d-flex flex-wrap gap-3 small text-muted">
                    <div class="d-flex align-items-center gap-1">
                        <i class="bi bi-circle-fill text-success"></i> <strong>Ghala (<?php echo e($wholesalerName); ?>)</strong>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <i class="bi bi-circle-fill text-danger"></i> <strong>Duka Langu (<?php echo e($retailerName); ?>)</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold"><i class="bi bi-truck me-2"></i>Taarifa za Usafirishaji</h5>
            </div>
            <div class="card-body">
                <p class="mb-1"><strong><?php echo e($wholesalerName); ?></strong></p>
                <p class="mb-1"><i class="bi bi-telephone"></i> <?php echo e($wholesalerPhone); ?></p>
                <p class="mb-1"><i class="bi bi-geo-alt"></i> <?php echo e($wholesalerAddress); ?></p>
                <hr>
                <h6>Dereva / Gari:</h6>
                <p class="fw-bold"><?php echo e($driverDetails); ?></p>
                <hr>
                <h6>Bidhaa Zilizoagizwa:</h6>
                <ul class="list-unstyled small">
                    <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($item->product->name); ?> x<?php echo e($item->quantity); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                <p><strong>Jumla:</strong> TZS <?php echo e(number_format($order->total_amount)); ?></p>
            </div>
        </div>

        
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold"><i class="bi bi-chat-dots me-2"></i>Mawasiliano</h5>
            </div>
            <div class="card-body">
                <a href="tel:<?php echo e($wholesalerPhone); ?>" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-telephone"></i> Piga Simu
                </a>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="<?php echo e(route('retailer.orders.wholesale')); ?>" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm">
        <i class="bi bi-arrow-left"></i> Rudi kwenye Orodha
    </a>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #wholesaleTrackingMap { border: 1px solid #ddd; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const greenIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
            iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
        });
        const redIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
            iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
        });

        const wholesalerLat = <?php echo e($wholesalerLat); ?>;
        const wholesalerLng = <?php echo e($wholesalerLng); ?>;
        const retailerLat = <?php echo e($retLat); ?>;
        const retailerLng = <?php echo e($retLng); ?>;

        const map = L.map('wholesaleTrackingMap').setView([wholesalerLat, wholesalerLng], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        L.marker([wholesalerLat, wholesalerLng], {icon: greenIcon})
            .addTo(map)
            .bindPopup('<strong>🏭 <?php echo e($wholesalerName); ?></strong><br><?php echo e($wholesalerAddress); ?>');

        const retailerMarker = L.marker([retailerLat, retailerLng], {icon: redIcon})
            .addTo(map)
            .bindPopup('<strong>🏪 <?php echo e($retailerName); ?></strong><br><?php echo e($retailerAddr); ?>');

        L.polyline([
            [wholesalerLat, wholesalerLng],
            [retailerLat, retailerLng]
        ], {
            color: '#FF6B35', weight: 4, opacity: 0.8, dashArray: '10, 10'
        }).addTo(map);

        const group = new L.featureGroup([
            map._layers[Object.keys(map._layers)[1]], // marker ya kwanza
            map._layers[Object.keys(map._layers)[2]]  // marker ya pili
        ]);
        map.fitBounds(group.getBounds().pad(0.2));
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views\retailer\orders\wholesale_tracking.blade.php ENDPATH**/ ?>