<?php $__env->startSection('title', 'Dashboard ya Jumla'); ?>
<?php $__env->startSection('page-title', 'Habari, ' . Auth::user()->full_name . '! 🏢'); ?>

<?php $__env->startSection('content'); ?>

<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-cash-stack fs-3 text-primary"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Mauzo ya Wiki Hii</h6>
                        <h3 class="mb-0">TZS <?php echo e(number_format($weeklySales ?? 0)); ?></h3>
                        <?php if(($salesChange ?? 0) > 0): ?>
                            <small class="text-success"><i class="bi bi-arrow-up"></i> <?php echo e($salesChange); ?>%</small>
                        <?php elseif(($salesChange ?? 0) < 0): ?>
                            <small class="text-danger"><i class="bi bi-arrow-down"></i> <?php echo e(abs($salesChange)); ?>%</small>
                        <?php else: ?>
                            <small class="text-muted">Hakuna mabadiliko</small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-warning bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-inbox fs-3 text-warning"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Maagizo Mapya</h6>
                        <h3 class="mb-0"><?php echo e($newOrdersCount ?? 0); ?></h3>
                        <small class="text-warning">Yanasubiri kukubaliwa</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-success bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-people fs-3 text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Wauzaji Rejareja</h6>
                        <h3 class="mb-0"><?php echo e($activeRetailersCount ?? 0); ?></h3>
                        <small class="text-muted">Walio hai</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-info bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-box-seam fs-3 text-info"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Ghala Langu</h6>
                        <h3 class="mb-0"><?php echo e(number_format($totalStock ?? 0)); ?></h3>
                        <small class="text-muted">Jumla ya mitungi</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Mauzo Mwezi Huu</h6>
                        <h4 class="mb-0">TZS <?php echo e(number_format($monthlySales ?? 0)); ?></h4>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Idadi ya Maagizo</h6>
                        <h4 class="mb-0"><?php echo e($totalOrdersMonth ?? 0); ?></h4>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-calendar-check fs-3 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Bidhaa Zinazouzwa Sana</h6>
                        <div class="mt-2">
                            <?php $__empty_1 = true; $__currentLoopData = $topProducts ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <span class="badge bg-light text-dark me-2 mb-1">
                                    <?php echo e($index + 1); ?>. <?php echo e($product->name); ?> (<?php echo e($product->total_sold); ?>)
                                </span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <span class="text-muted">Hakuna mauzo bado</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-trophy fs-3 text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold"><i class="bi bi-bell me-2 text-primary"></i>Maagizo Mapya kutoka kwa Wauzaji </h5>
                <a href="<?php echo e(route('wholesaler.orders.incoming')); ?>" class="text-decoration-none">Ona Yote <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Duka</th>
                                <th>Bidhaa</th>
                                <th>Jumla (TZS)</th>
                                <th>Muda</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            <?php
                                // Ikiwa ni Collection, tumia take(2). Ikiwa ni array, badilisha kwanza.
                                if ($incomingOrders instanceof \Illuminate\Support\Collection) {
                                    $sampleOrders = $incomingOrders->take(2);
                                } else {
                                    $sampleOrders = collect($incomingOrders ?? [])->take(2);
                                }
                            ?>
                            
                            <?php $__empty_1 = true; $__currentLoopData = $sampleOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="fw-medium"><?php echo e($order['number'] ?? $order->number ?? ''); ?></td>
                                <td><?php echo e($order['retailer'] ?? $order->retailer ?? ''); ?></td>
                                <td><?php echo e(Str::limit($order['items'] ?? $order->items ?? '', 30)); ?></td>
                                <td>TZS <?php echo e(number_format($order['total'] ?? $order->total ?? 0)); ?></td>
                                <td><?php echo e($order['time'] ?? $order->time ?? ''); ?></td>
                                <td>
                                    <a href="<?php echo e(route('wholesaler.orders.incoming')); ?>" class="btn btn-sm btn-outline-primary">
                                        Shughulikia
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                    Hakuna maagizo mapya kwa sasa.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                
                <?php
                    $totalOrders = ($incomingOrders instanceof \Illuminate\Support\Collection) 
                        ? $incomingOrders->count() 
                        : count($incomingOrders ?? []);
                ?>
                
            </div>
        </div>
    </div>

    
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold"><i class="bi bi-trophy me-2 text-warning"></i>Wauzaji Rejareja Wanaoongoza</h5>
            </div>
            <div class="card-body">
                <?php $__empty_1 = true; $__currentLoopData = $topRetailers ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $retailer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0 me-3">
                        <span class="badge bg-light text-dark rounded-pill px-3 py-2">#<?php echo e($index + 1); ?></span>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0"><?php echo e($retailer['name'] ?? $retailer->name ?? ''); ?></h6>
                        <small class="text-muted">Maagizo: <?php echo e($retailer['orders'] ?? $retailer->orders ?? 0); ?></small>
                    </div>
                    <div class="text-end">
                        <strong>TZS <?php echo e(number_format($retailer['total'] ?? $retailer->total ?? 0)); ?></strong>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-trophy fs-1 d-block mb-2 opacity-50"></i>
                    Hakuna mauzo bado.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold"><i class="bi bi-exclamation-triangle me-2 text-danger"></i>Bidhaa Zinazokaribia Kuisha</h5>
                <a href="<?php echo e(route('wholesaler.products.index')); ?>" class="text-decoration-none">Bidhaa Zote <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="card-body">
                <?php if(count($lowStockItems ?? []) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Bidhaa</th>
                                <th>Idadi</th>
                                <th>Kiwango cha Chini</th>
                                <th>Hali</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $lowStockItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($item['name'] ?? $item->name ?? ''); ?></td>
                                <td>
                                    <?php if(($item['quantity'] ?? $item->quantity ?? 0) == 0): ?>
                                        <span class="text-danger fw-bold">0</span>
                                    <?php else: ?>
                                        <?php echo e($item['quantity'] ?? $item->quantity ?? 0); ?>

                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($item['min'] ?? $item->min ?? 0); ?></td>
                                <td>
                                    <?php if(($item['quantity'] ?? $item->quantity ?? 0) == 0): ?>
                                        <span class="badge bg-danger">Imekwisha</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Agiza Upya</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-3 text-muted">
                    <i class="bi bi-check-circle fs-1 d-block mb-2 opacity-50"></i>
                    Hakuna bidhaa zinazokaribia kuisha.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .bg-opacity-10 { --bs-bg-opacity: 0.1; }
    
    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08) !important;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views\wholesaler\dashboard.blade.php ENDPATH**/ ?>