<?php $__env->startSection('title', 'Dashboard ya Duka'); ?>
<?php $__env->startSection('page-title', 'Habari, ' . Auth::user()->full_name . '! 🏪'); ?>

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
                        <h6 class="text-muted mb-1">Mauzo Leo</h6>
                        <h3 class="mb-0">TZS <?php echo e(number_format($todaySales ?? 0)); ?></h3>
                        <?php if($salesChange > 0): ?>
                            <small class="text-success"><i class="bi bi-arrow-up"></i> <?php echo e($salesChange); ?>%</small>
                        <?php elseif($salesChange < 0): ?>
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
                        <i class="bi bi-bell fs-3 text-warning"></i>
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
                    <div class="flex-shrink-0 bg-info bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-truck fs-3 text-info"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Yanayoendelea</h6>
                        <h3 class="mb-0"><?php echo e($activeOrdersCount ?? 0); ?></h3>
                        <small class="text-muted">Maagizo yanashughulikiwa</small>
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
                        <i class="bi bi-calendar-check fs-3 text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Mauzo Mwezi</h6>
                        <h3 class="mb-0">TZS <?php echo e(number_format($monthlySales ?? 0)); ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row g-3 mb-4">
    
    <div class="col-md-4">
        <a href="<?php echo e(route('retailer.orders.incoming')); ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-4 bg-primary bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <i class="bi bi-bell-fill fs-1" style="color: #0d6efd;"></i>
                    <h6 class="mt-2 mb-0">Maagizo Mapya ya Wateja</h6>
                    <?php if($newOrdersCount > 0): ?>
                        <span class="badge bg-primary mt-1"><?php echo e($newOrdersCount); ?> yanasubiri</span>
                    <?php endif; ?>
                </div>
            </div>
        </a>
    </div>

    
    <div class="col-md-4">
        <a href="<?php echo e(route('retailer.orders.active')); ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="background-color: rgba(111, 66, 193, 0.1);">
                <div class="card-body text-center py-3">
                    <i class="bi bi-truck fs-1" style="color: #6f42c1;"></i>
                    <h6 class="mt-2 mb-0">Maagizo Yanayoendelea</h6>
                    <?php if($activeOrdersCount > 0): ?>
                        <span class="badge mt-1" style="background-color: #6f42c1; color: white;"><?php echo e($activeOrdersCount); ?> yanashughulikiwa</span>
                    <?php endif; ?>
                </div>
            </div>
        </a>
    </div>
    
    
    <div class="col-md-4">
        <a href="<?php echo e(route('retailer.procurement.browse')); ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-4 bg-success bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <i class="bi bi-cart-plus fs-1" style="color: #198754;"></i>
                    <h6 class="mt-2 mb-0">Agiza Gesi Jumla</h6>
                    <small class="text-muted">Kutoka kwa Wholesaler</small>
                </div>
            </div>
        </a>
    </div>
    
</div>





<div class="mb-4">
    <div class="d-flex align-items-center mb-3">
        <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
            <i class="bi bi-people-fill fs-4 text-primary"></i>
        </div>
        <h4 class="fw-bold mb-0">Maagizo kutoka kwa Wateja-(Consumers)</h4>
    </div>
    
    <div class="row g-4">
        
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold">
                        <i class="bi bi-inbox me-2 text-warning"></i>
                        Yanayosubiri Kukubaliwa
                    </h5>
                    <a href="<?php echo e(route('retailer.orders.incoming')); ?>" class="text-decoration-none">
                        OnaYote <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Mteja</th>
                                    <th>Bidhaa</th>
                                    <th>Umbali</th>
                                    <th>Jumla</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $incomingOrders->take(2) ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="fw-medium"><?php echo e($order['number']); ?></td>
                                    <td><?php echo e($order['customer']); ?></td>
                                    <td><?php echo e($order['service']); ?></td>
                                    <td><?php echo e($order['distance']); ?></td>
                                    <td>TZS <?php echo e(number_format($order['total'])); ?></td>
                                    <td>
                                        <a href="<?php echo e(route('retailer.orders.incoming')); ?>" class="btn btn-sm btn-outline-warning">
                                            <i class="bi bi-eye"></i> Shughulikia
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="bi bi-check-circle fs-1 d-block mb-2 opacity-50"></i>
                                        Hakuna maagizo yanayosubiri.
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold">
                        <i class="bi bi-truck me-2 text-info"></i>
                        Yanayoendelea / Yaliyokamilika
                    </h5>
                    <a href="<?php echo e(route('retailer.orders.history')); ?>" class="text-decoration-none">
                        OnaYote <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body">
        <div class="table-responsive">
        <table class="table table-hover align-middle">
        <thead>
        <tr>
            <th>#</th>
            <th>Mteja</th>
            <th>Bidhaa</th>
            <th>Jumla</th>
            <th>Hali</th>
            <th>Tarehe</th>
        </tr>
    </thead>
    <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $recentSales->take(3) ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
            <td class="fw-medium"><?php echo e($order['number']); ?></td>
            <td><?php echo e($order['customer']); ?></td>
            <td><?php echo e($order['service']); ?></td>
            <td>TZS <?php echo e(number_format($order['total'])); ?></td>
            <td>
                <?php if($order['status'] == 'delivered'): ?>
                    <span class="badge bg-success">Imekamilika</span>
                <?php elseif($order['status'] == 'accepted'): ?>
                    <span class="badge bg-info">Imekubaliwa</span>
                <?php elseif($order['status'] == 'picked_up'): ?>
                    <span class="badge bg-primary">Imeshachukuliwa</span>
                <?php elseif($order['status'] == 'out_for_delivery'): ?>
                    <span class="badge bg-warning text-dark">Njiani</span>
                <?php elseif($order['status'] == 'cancelled'): ?>
                    <span class="badge bg-danger">Imefutwa</span>
                <?php else: ?>
                    <span class="badge bg154secondary"><?php echo e($order['status']); ?></span>
                <?php endif; ?>
            </td>
            <td>
                
                <?php echo e($order['time']); ?>

            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr>
            <td colspan="6" class="text-center py-4 text-muted">
                <i class="bi bi-receipt fs-1 d-block mb-2 opacity-50"></i>
                Hakuna maagizo ya hivi karibuni.
            </td>
        </tr>
        <?php endif; ?>
    </tbody>
    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




<div class="mb-4">
    <div class="d-flex align-items-center mb-3">
        <div class="bg-success bg-opacity-10 p-2 rounded-3 me-3">
            <i class="bi bi-box-seam fs-4 text-success"></i>
        </div>
        <h4 class="fw-bold mb-0">Maagizo Bidhaa Mpya ya Jumla-(Wholesale)</h4>
    </div>
    
    <div class="row g-4">
        
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold">
                        <i class="bi bi-clock-history me-2 text-success"></i>
                        Maagizo ya Jumla Yanayosubiri
                    </h5>
                    <a href="<?php echo e(route('retailer.procurement.wholesale_orders')); ?>" class="text-decoration-none">
                        OnaYote <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body">
                    <?php if(isset($allWholesaleOrders) && count($allWholesaleOrders) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Wholesaler</th>
                                        <th>Hali</th>
                                        <th>Jumla</th>
                                        <th>Tarehe</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $allWholesaleOrders->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="fw-medium"><?php echo e($order['order_number']); ?></td>
                                        <td><?php echo e($order['wholesaler']); ?></td>
                                        <td>
                                            <?php if($order['status'] == 'pending'): ?>
                                                <span class="badge bg-warning text-dark">Inasubiri</span>
                                            <?php elseif($order['status'] == 'confirmed'): ?>
                                                <span class="badge bg-info">Imethibitishwa</span>
                                            <?php elseif($order['status'] == 'processing'): ?>
                                                <span class="badge bg-primary">Inashughulikiwa</span>
                                            <?php elseif($order['status'] == 'dispatched'): ?>
                                                <span class="badge bg-warning">Imesafirishwa</span>
                                            <?php elseif($order['status'] == 'delivered'): ?>
                                                <span class="badge bg-success">Imepokelewa</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><?php echo e(ucfirst($order['status'])); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>TZS <?php echo e(number_format($order['total'])); ?></td>
                                        <td><?php echo e($order['created_at']); ?></td>
                                        <td>
                                            <a href="<?php echo e(route('retailer.procurement.wholesale_orders')); ?>" class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-box fs-1 d-block mb-2 opacity-50"></i>
                            <p class="mb-3">Hakuna maagizo ya jumla bado.</p>
                            <a href="<?php echo e(route('retailer.procurement.browse')); ?>" class="btn btn-success">
                                <i class="bi bi-cart-plus"></i> Agiza Bidhaa kwa Jumla
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="col-lg-5">
            
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="fw-bold"><i class="bi bi-trophy me-2 text-warning"></i>Bidhaa Zinazouzwa Sana</h5>
                </div>
                <div class="card-body">
                    <?php $__empty_1 = true; $__currentLoopData = $topProducts ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0 me-3">
                            <span class="badge bg-light text-dark rounded-pill px-3 py-2">#<?php echo e($index + 1); ?></span>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0"><?php echo e($product->name); ?></h6>
                            <small class="text-muted">Imeuzwa: <?php echo e($product->total_sold); ?> mitungi</small>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-muted text-center py-3">Bado hakuna mauzo.</p>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="fw-bold"><i class="bi bi-shop me-2 text-primary"></i>Muhtasari wa Duka</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Hali ya Duka:</span>
                        <span>
                            <?php if($businessProfile->is_open ?? false): ?>
                                <span class="text-success"><i class="bi bi-check-circle-fill"></i> Liko Wazi</span>
                            <?php else: ?>
                                <span class="text-danger"><i class="bi bi-x-circle-fill"></i> Limefungwa</span>
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Usafirishaji:</span>
                        <span>
                            <?php if($businessProfile->can_deliver ?? false): ?>
                                <span class="text-success">Inapatikana</span>
                            <?php else: ?>
                                <span class="text-secondary">Haipatikani</span>
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Umbali wa Huduma:</span>
                        <span><?php echo e($businessProfile->service_radius_km ?? 5); ?> km</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Ada ya Usafirishaji:</span>
                        <span>TZS <?php echo e(number_format($businessProfile->delivery_fee_per_km ?? 0)); ?>/km</span>
                    </div>
                    <hr>
                    <a href="<?php echo e(route('retailer.settings.shop')); ?>" class="btn btn-outline-primary w-100">
                        <i class="bi bi-gear"></i> Mipangilio ya Duka
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        body {
            background: linear-gradient(145deg, #a3a3e4 0%, #b3b896 100%);
            font-family: 'Inter', sans-serif;
        }
        
        /* Sidebar Styling */
        .sidebar {
            background: linear-gradient(180deg, #1A1A2E 0%, #16213E 100%);
            box-shadow: 5px 0 20px rgba(0, 0, 0, 0.05);
            min-height: 100vh;
            color: white;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.7);
            padding: 0.8rem 1.25rem;
            border-radius: 12px;
            margin-bottom: 6px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .sidebar .nav-link:hover {
            background: linear-gradient(145deg, #7575b9 0%, #b3b896 100%);
            color: white;
        }
        
        .sidebar .nav-link.active {
            background: linear-gradient(145deg, #7575b9, #E85D2C);
            color: white;
            box-shadow: 0 5px 15px rgba(255, 107, 53, 0.3);
        }
        
        .sidebar .nav-link i {
            width: 24px;
            margin-right: 8px;
        }
        
        /* Profaili ya Mtumiaji kwenye Sidebar */
        .sidebar-profile {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 1.2rem 1rem;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
        }
        
        .sidebar-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(145deg, #7575b9 0%, #b3b896 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.3rem;
            box-shadow: 0 5px 15px rgba(255, 107, 53, 0.3);
        }
        
        .sidebar-user-info {
            flex: 1;
        }
        
        .sidebar-user-name {
            font-weight: 700;
            color: white;
            font-size: 1rem;
            line-height: 1.2;
        }
        
        .sidebar-user-role {
            display: inline-block;
            background: rgba(255, 255, 255, 0.15);
            padding: 0.2rem 0.8rem;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: rgba(255, 255, 255, 0.9);
            margin-top: 5px;
        }
        
        .sidebar-logo {
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(145deg, #FF6B35, #F9C22E);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-align: center;
            margin-bottom: 1rem;
        }
        
        .sidebar-logo i {
            -webkit-text-fill-color: #FF6B35;
            margin-right: 8px;
        }
        
        .sidebar-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin: 1rem 0;
        }
        
        /* Topbar Styling */
        .topbar {
            background: white;
            padding: 0.9rem 1.8rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            border-bottom: 1px solid #f0f0f0;
        }
        
        .topbar h5 {
            font-weight: 700;
            color: #1A1A2E;
        }
        
        .topbar .btn-light {
            background: #f8f9fa;
            border: none;
            border-radius: 30px;
            padding: 0.5rem 1.2rem;
            font-weight: 500;
        }
        
        .topbar .btn-light:hover {
            background: #e9ecef;
        }
        
        /* Cards Styling */
        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08) !important;
        }
        
        /* Table Styling */
        .table thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.5px;
            color: rgba(255, 255, 255, 0.9);
            border-bottom-width: 1px;
        }
        
        .table tbody tr {
            transition: background-color 0.2s;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        /* Section Headers */
        .section-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        /* Mobile Menu */
        .mobile-menu {
            background: white;
            border-top: 1px solid #e2e8f0;
            padding: 0.5rem 0;
        }
        
        /* Responsive */
        @media (max-width: 767.98px) {
            .sidebar {
                display: none;
            }
            
            .topbar {
                padding: 0.7rem 1rem;
            }
            
            .card-body {
                padding: 1rem;
            }
        }
    </style>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views\retailer\dashboard.blade.php ENDPATH**/ ?>