<?php $__env->startSection('title', 'Historia ya Mauzo ya Jumla'); ?>
<?php $__env->startSection('page-title', 'Ripoti ya Mauzo ya Jumla'); ?>

<?php $__env->startSection('content'); ?>

<!-- <div class="row g-3 mb-4">
<div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-danger bg-opacity-10">
            <div class="card-body py-3">
                <h6 class="text-muted mb-1">Yaliyofutwa</h6>
                <h3 class="mb-0"><?php echo e($cancelledOrders ?? 0); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-warning bg-opacity-10">
            <div class="card-body py-3">
                <h6 class="text-muted mb-1">Yanayosubiri</h6>
                <h3 class="mb-0"><?php echo e($pendingOrders ?? 0); ?></h3>
            </div>
        </div>
    </div>
</div>  -->


<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('wholesaler.orders.history')); ?>" class="row g-3">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Hali ya Agizo</label>
                <select class="form-select form-select-sm" name="status" onchange="this.form.submit()">
                    <option value="">Zote</option>
                    <option value="delivered" <?php echo e(request('status') == 'delivered' ? 'selected' : ''); ?>>Imekamilika</option>
                    <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Inasubiri</option>
                    <option value="processing" <?php echo e(request('status') == 'processing' ? 'selected' : ''); ?>>Inashughulikiwa</option>
                    <option value="dispatched" <?php echo e(request('status') == 'dispatched' ? 'selected' : ''); ?>>Imesafirishwa</option>
                    <option value="cancelled" <?php echo e(request('status') == 'cancelled' ? 'selected' : ''); ?>>Imefutwa</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Kuanzia</label>
                <input type="date" class="form-control form-control-sm" name="date_from" 
                       value="<?php echo e(request('date_from')); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Hadi</label>
                <input type="date" class="form-control form-control-sm" name="date_to" 
                       value="<?php echo e(request('date_to')); ?>">
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-search"></i> Chuja
                </button>
                <?php if(request()->anyFilled(['status', 'date_from', 'date_to'])): ?>
                    <a href="<?php echo e(route('wholesaler.orders.history')); ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-circle"></i> Futa
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>


<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <?php if(isset($orders) && count($orders) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Agizo #</th>
                            <th>Duka (Retailer)</th>
                            <th>Bidhaa</th>
                            <th>Jumla (TZS)</th>
                            <th>Malipo</th>
                            <th>Hali</th>
                            <th>Tarehe</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="fw-medium"><?php echo e($order->order_number); ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2" 
                                         style="width: 36px; height: 36px;">
                                        <span class="fw-bold text-secondary" style="font-size: 0.8rem;">
                                            <?php echo e(strtoupper(substr($order->retailer->business_name ?? 'D', 0, 1))); ?>

                                        </span>
                                    </div>
                                    <div>
                                        <span class="fw-medium"><?php echo e($order->retailer->business_name ?? 'Duka'); ?></span>
                                        <br>
                                        <small class="text-muted"><?php echo e(\Illuminate\Support\Str::limit($order->delivery_address, 25)); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php
                                    $itemsList = $order->items->map(function($item) {
                                        return ($item->product->name ?? 'Bidhaa') . ' x' . $item->quantity;
                                    })->implode(', ');
                                ?>
                                <span class="text-truncate d-inline-block" style="max-width: 180px;" 
                                      title="<?php echo e($itemsList); ?>">
                                    <?php echo e(\Illuminate\Support\Str::limit($itemsList, 30)); ?>

                                </span>
                            </td>
                            <td class="fw-semibold">TZS <?php echo e(number_format($order->total_amount)); ?></td>
                            <td>
                                <?php if($order->payment_status == 'paid'): ?>
                                    <span class="badge bg-success rounded-pill px-3 py-2">
                                        <i class="bi bi-check-circle-fill me-1"></i> Imelipwa
                                    </span>
                                <?php elseif($order->payment_status == 'partial'): ?>
                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                                        <i class="bi bi-hourglass-split me-1"></i> Sehemu
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary rounded-pill px-3 py-2">
                                        <i class="bi bi-clock me-1"></i> Haijalipwa
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($order->status == 'pending'): ?>
                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-2">Inasubiri</span>
                                <?php elseif($order->status == 'confirmed'): ?>
                                    <span class="badge bg-info rounded-pill px-3 py-2">Imethibitishwa</span>
                                <?php elseif($order->status == 'processing'): ?>
                                    <span class="badge bg-primary rounded-pill px-3 py-2">Inashughulikiwa</span>
                                <?php elseif($order->status == 'dispatched'): ?>
                                    <span class="badge bg-warning rounded-pill px-3 py-2">Imesafirishwa</span>
                                <?php elseif($order->status == 'delivered'): ?>
                                    <span class="badge bg-success rounded-pill px-3 py-2">
                                        <i class="bi bi-check-circle-fill me-1"></i> Imekamilika
                                    </span>
                                <?php elseif($order->status == 'cancelled'): ?>
                                    <span class="badge bg-danger rounded-pill px-3 py-2">
                                        <i class="bi bi-x-circle-fill me-1"></i> Imefutwa
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="text-muted small"><?php echo e($order->created_at->format('d M Y')); ?></span>
                                <br>
                                <small class="text-muted" style="font-size: 0.7rem;">
                                    <i class="bi bi-clock"></i> <?php echo e($order->created_at->format('H:i')); ?>

                                </small>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary rounded-circle" 
                                        style="width: 36px; height: 36px; padding: 0;"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#orderModal<?php echo e($order->id); ?>">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            
            
            <div class="d-flex justify-content-center p-3">
                <?php echo e($orders->links()); ?>

            </div>
            
            
            <div class="card-footer bg-white border-0 py-3">
                <strong>Jumla ya Mauzo: TZS <?php echo e(number_format($totalSales ?? 0)); ?></strong>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <div class="display-1 text-muted mb-3"><i class="bi bi-inbox"></i></div>
                <h5 class="fw-bold">Hakuna Maagizo ya Jumla</h5>
                <p class="text-muted mb-4">Hakuna maagizo yanayofanana na vigezo vilivyochaguliwa.</p>
                <a href="<?php echo e(route('wholesaler.orders.history')); ?>" class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-arrow-repeat me-1"></i> Onyesha Yote
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>


<?php if(isset($orders)): ?>
    <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="modal fade" id="orderModal<?php echo e($order->id); ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-box-seam me-2 text-primary"></i>
                        Agizo #<?php echo e($order->order_number); ?>

                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3 h-100">
                                <h6 class="fw-bold mb-2"><i class="bi bi-shop me-2"></i>Duka (Retailer)</h6>
                                <p class="mb-1 fw-semibold"><?php echo e($order->retailer->business_name ?? 'Duka'); ?></p>
                                <p class="mb-1 text-muted small">
                                    <i class="bi bi-geo-alt me-1"></i><?php echo e($order->delivery_address); ?>

                                </p>
                                <?php if($order->retailer->user): ?>
                                <p class="mb-0 text-muted small">
                                    <i class="bi bi-telephone me-1"></i><?php echo e($order->retailer->user->phone_number ?? 'Haipo'); ?>

                                </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3 h-100">
                                <h6 class="fw-bold mb-2"><i class="bi bi-info-circle me-2"></i>Maelezo ya Agizo</h6>
                                <p class="mb-1 small"><i class="bi bi-calendar me-2"></i><?php echo e($order->created_at->format('d M Y, H:i')); ?></p>
                                <p class="mb-1 small">
                                    <i class="bi bi-credit-card me-2"></i>
                                    <?php if($order->payment_status == 'paid'): ?> <span class="badge bg-success">Imelipwa</span>
                                    <?php elseif($order->payment_status == 'partial'): ?> <span class="badge bg-warning">Sehemu</span>
                                    <?php else: ?> <span class="badge bg-secondary">Haijalipwa</span> <?php endif; ?>
                                </p>
                                <?php if($order->delivered_at): ?>
                                <p class="mb-0 small text-success">
                                    <i class="bi bi-check-circle me-1"></i>Imekamilika: <?php echo e($order->delivered_at->format('d M Y, H:i')); ?>

                                </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3"><i class="bi bi-cart me-2"></i>Bidhaa Zilizoagizwa</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr><th>Bidhaa</th><th class="text-center">Idadi</th><th class="text-end">Bei/Unit</th><th class="text-end">Jumla</th></tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="fw-medium"><?php echo e($item->product->name ?? 'Bidhaa'); ?></td>
                                    <td class="text-center"><?php echo e($item->quantity); ?></td>
                                    <td class="text-end">TZS <?php echo e(number_format($item->price_per_item)); ?></td>
                                    <td class="text-end fw-semibold">TZS <?php echo e(number_format($item->quantity * $item->price_per_item)); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr><th colspan="3" class="text-end">Jumla Kuu:</th><th class="text-end text-primary fs-5">TZS <?php echo e(number_format($order->total_amount)); ?></th></tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Funga
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .badge.bg-success { background-color: #d1e7dd !important; color: #0f5132 !important; }
    .badge.bg-danger { background-color: #f8d7da !important; color: #842029 !important; }
    .badge.bg-warning { background-color: #fff3cd !important; color: #856404 !important; }
    .badge.bg-info { background-color: #e6f3ff !important; color: #0056b3 !important; }
    .badge.bg-primary { background-color: #cfe2ff !important; color: #084298 !important; }
    .bg-opacity-10 { --bs-bg-opacity: 0.1; }
    
    .table tbody tr {
        transition: all 0.2s ease;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.04);
    }
    
    .text-truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .pagination { gap: 5px; }
    .page-link { border-radius: 10px !important; border: none; padding: 0.5rem 1rem; color: #6c757d; }
    .page-item.active .page-link { background: linear-gradient(145deg, #FF6B35, #E85D2C); color: white; }
</style>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views\wholesaler\orders\history.blade.php ENDPATH**/ ?>