<?php $__env->startSection('title', 'Usimamizi wa Watumiaji'); ?>
<?php $__env->startSection('page-title', 'Usimamizi wa Watumiaji'); ?>

<?php $__env->startSection('content'); ?>


<ul class="nav nav-tabs mb-4" id="userTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="all-users-tab" data-bs-toggle="tab" data-bs-target="#all-users" type="button" role="tab">
            <i class="bi bi-people me-1"></i> 
            Watumiaji Wote 
            <span class="badge bg-primary ms-1"><?php echo e($totalUsers ?? 0); ?></span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="verify-tab" data-bs-toggle="tab" data-bs-target="#verify-pending" type="button" role="tab">
            <i class="bi bi-check-circle me-1"></i> 
            Zinazosubiri Uidhinishaji 
            <span class="badge bg-warning text-dark ms-1"><?php echo e($pendingVerifications ?? 0); ?></span>
        </button>
    </li>
</ul>


<div class="tab-content" id="userTabsContent">
    
    
    
    
    <div class="tab-pane fade show active" id="all-users" role="tabpanel">
        
        
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 pb-3">
                <div class="row align-items-center">
                    <div class="col-md-4 mb-2 mb-md-0">
                        <form id="filterForm" method="GET" action="<?php echo e(route('admin.users.index')); ?>">
                            <select class="form-select w-auto d-inline-block" name="type" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Aina Zote</option>
                                <option value="consumer" <?php echo e(request('type') == 'consumer' ? 'selected' : ''); ?>>Consumers</option>
                                <option value="retailer" <?php echo e(request('type') == 'retailer' ? 'selected' : ''); ?>>Retailers</option>
                                <option value="wholesaler" <?php echo e(request('type') == 'wholesaler' ? 'selected' : ''); ?>>Wholesalers</option>
                                <option value="admin" <?php echo e(request('type') == 'admin' ? 'selected' : ''); ?>>Admins</option>
                            </select>
                        
                            <select class="form-select w-auto d-inline-block ms-2" name="status" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Hali Zote</option>
                                <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>Active</option>
                                <option value="inactive" <?php echo e(request('status') == 'inactive' ? 'selected' : ''); ?>>Imefungwa</option>
                            </select>
                        </form>
                    </div>
                    <div class="col-md-8 text-md-end">
                        <form method="GET" action="<?php echo e(route('admin.users.index')); ?>" class="d-inline-flex w-100 w-md-auto">
                            <input type="text" name="search" class="form-control" placeholder="Tafuta kwa jina, simu..." value="<?php echo e(request('search')); ?>">
                            <button type="submit" class="btn btn-primary ms-2">
                                <i class="bi bi-search"></i>
                            </button>
                            <?php if(request()->filled('type') || request()->filled('status') || request()->filled('search')): ?>
                                <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-outline-secondary ms-2">
                                    <i class="bi bi-x-lg"></i> Futa
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Jina</th>
                                <th>Namba ya Simu</th>
                                <th>Barua Pepe</th>
                                <th>Aina</th>
                                <th>Hali</th>
                                <th>Tarehe ya Usajili</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $users ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr data-type="<?php echo e($user->user_type); ?>">
                                <td>#<?php echo e($user->id); ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                            <span class="fw-bold text-primary"><?php echo e(strtoupper(substr($user->full_name, 0, 1))); ?></span>
                                        </div>
                                        <div>
                                            <strong><?php echo e($user->full_name); ?></strong>
                                            <?php if($user->isBusinessUser()): ?>
                                                <br><small class="text-muted"><?php echo e($user->businessProfile->business_name ?? 'Hakuna duka'); ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo e($user->phone_number); ?></td>
                                <td><?php echo e($user->email ?? 'Haipo'); ?></td>
                                <td>
                                    <?php if($user->user_type == 'consumer'): ?>
                                        <span class="badge bg-primary">Mtumiaji</span>
                                    <?php elseif($user->user_type == 'retailer'): ?>
                                        <span class="badge bg-success">Muuza Rejareja</span>
                                    <?php elseif($user->user_type == 'wholesaler'): ?>
                                        <span class="badge bg-warning text-dark">Muuza Jumla</span>
                                    <?php elseif($user->user_type == 'admin'): ?>
                                        <span class="badge bg-danger">Admin</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?php echo e(ucfirst($user->user_type)); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($user->is_active): ?>
                                        <span class="badge bg-success">Active</span>
                                        <?php if($user->is_phone_verified): ?>
                                            <i class="bi bi-check-circle-fill text-success ms-1" title="Simu imethibitishwa"></i>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Imefungwa</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small><?php echo e($user->created_at->format('d M Y')); ?></small>
                                    <br>
                                    <small class="text-muted"><?php echo e($user->created_at->diffForHumans()); ?></small>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="<?php echo e(route('admin.users.show', $user->id)); ?>">
                                                    <i class="bi bi-eye"></i> Tazama Maelezo
                                                </a>
                                            </li>
                                            <?php if($user->id !== auth()->id()): ?>
                                                <li>
                                                    <form method="POST" action="<?php echo e(route('admin.users.toggle', $user->id)); ?>">
                                                        <?php echo csrf_field(); ?>
                                                        <?php if($user->is_active): ?>
                                                            <button type="submit" class="dropdown-item text-warning">
                                                                <i class="bi bi-lock"></i> Funga Akaunti
                                                            </button>
                                                        <?php else: ?>
                                                            <button type="submit" class="dropdown-item text-success">
                                                                <i class="bi bi-unlock"></i> Fungua Akaunti
                                                            </button>
                                                        <?php endif; ?>
                                                    </form>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form method="POST" action="<?php echo e(route('admin.users.destroy', $user->id)); ?>"
                                                          onsubmit="return confirm('Una uhakika? Akaunti na taarifa zake zote zitaondolewa kabisa. Hatua hii haiwezi kurejeshwa.');">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="bi bi-trash3"></i> Futa Mtumiaji
                                                        </button>
                                                    </form>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-people fs-1 d-block mb-2 opacity-50"></i>
                                    Hakuna watumiaji wanaolingana na vigezo vyako.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                <span>Jumla: <?php echo e($users->total() ?? 0); ?> watumiaji</span>
                <?php echo e($users->links()); ?>

            </div>
        </div>
    </div>
    
    
    
    
    <div class="tab-pane fade" id="verify-pending" role="tabpanel">

        
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-3">
                <form method="GET" action="<?php echo e(route('admin.users.index')); ?>" id="verifyFilterForm">
                    <input type="hidden" name="tab" value="verify">
                    <div class="row align-items-center">
                        <div class="col-md-4 mb-2 mb-md-0">
                            <select class="form-select" name="vtype" onchange="document.getElementById('verifyFilterForm').submit()">
                                <option value="">Aina Zote za Biashara</option>
                                <option value="retailer" <?php echo e(request('vtype') == 'retailer' ? 'selected' : ''); ?>>Wauzaji Rejareja</option>
                                <option value="wholesaler" <?php echo e(request('vtype') == 'wholesaler' ? 'selected' : ''); ?>>Wauzaji Jumla</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-2 mb-md-0">
                            <select class="form-select" name="vstatus" onchange="document.getElementById('verifyFilterForm').submit()">
                                <option value="">Hali Zote</option>
                                <option value="unverified" <?php echo e(request('vstatus') == 'unverified' ? 'selected' : ''); ?>>Zisizoidhinishwa</option>
                                <option value="verified" <?php echo e(request('vstatus') == 'verified' ? 'selected' : ''); ?>>Zimeidhinishwa</option>
                            </select>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <?php if(request()->filled('vtype') || request()->filled('vstatus')): ?>
                                <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-lg"></i> Futa Vichujio
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold"><i class="bi bi-check-circle me-2 text-warning"></i>Maombi ya Biashara (<?php echo e($pending->total() ?? 0); ?>)</h5>
            </div>
            <div class="card-body p-0">
                <?php $__empty_1 = true; $__currentLoopData = $pending; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $biz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="border-bottom p-4">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center mb-2">
                                <h6 class="fw-bold mb-0 me-2"><?php echo e($biz->business_name); ?></h6>
                                <?php if($biz->business_type == 'retailer'): ?>
                                    <span class="badge bg-success">Muuza Rejareja</span>
                                <?php else: ?>
                                    <span class="badge bg-primary">Muuza Jumla</span>
                                <?php endif; ?>
                                
                                <?php if($biz->is_open): ?>
                                    <span class="badge bg-success ms-2">Imeidhinishwa</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark ms-2">Hajaidhinishwa</span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="row">
                                <div class="col-sm-6">
                                    <p class="mb-1"><i class="bi bi-person me-2"></i> Mmiliki: <?php echo e($biz->user->full_name ?? 'Haijulikani'); ?></p>
                                    <p class="mb-1"><i class="bi bi-telephone me-2"></i> <?php echo e($biz->user->phone_number ?? 'Haipo'); ?></p>
                                </div>
                                <div class="col-sm-6">
                                    <p class="mb-1"><i class="bi bi-geo-alt me-2"></i> <?php echo e($biz->physical_address ?? 'Haipo'); ?></p>
                                    <p class="mb-1"><i class="bi bi-file-text me-2"></i> TIN: <?php echo e($biz->tinn_number ?? 'Haipo'); ?></p>
                                </div>
                            </div>
                            
                            <small class="text-muted">
                                <i class="bi bi-calendar me-1"></i> Ilisajiliwa: <?php echo e($biz->created_at->format('d M Y, H:i')); ?>

                            </small>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <div class="d-grid gap-2">
                                
                                <button type="button" class="btn btn-outline-primary btn-sm" 
                                        onclick="openCustomModal('detailsModal<?php echo e($biz->id); ?>')">
                                    <i class="bi bi-eye"></i> Tazama Maelezo
                                </button>
                                
                                <?php if(!$biz->is_open): ?>
                                    <form method="POST" action="<?php echo e(route('admin.verify.approve', $biz->id)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-success w-100" onclick="return confirm('Una uhakika unataka kuidhinisha biashara hii?')">
                                            <i class="bi bi-check-lg"></i> Idhinisha
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <button type="button" class="btn btn-outline-danger" 
                                        onclick="openCustomModal('rejectModal<?php echo e($biz->id); ?>')">
                                    <i class="bi bi-x-lg"></i> Kataa
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="custom-modal-overlay" id="detailsModal<?php echo e($biz->id); ?>">
                    <div class="custom-modal">
                        <div class="custom-modal-header">
                            <h5 class="custom-modal-title"><?php echo e($biz->business_name); ?></h5>
                            <button type="button" class="custom-modal-close" onclick="closeCustomModal('detailsModal<?php echo e($biz->id); ?>')">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                        <div class="custom-modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-3">Maelezo ya Biashara</h6>
                                    <p><strong>Jina:</strong> <?php echo e($biz->business_name); ?></p>
                                    <p><strong>Aina:</strong> <?php echo e($biz->business_type == 'retailer' ? 'Muuza Rejareja' : 'Muuza Jumla'); ?></p>
                                    <p><strong>TIN:</strong> <?php echo e($biz->tinn_number ?? 'Haipo'); ?></p>
                                    <p><strong>Anwani:</strong> <?php echo e($biz->physical_address ?? 'Haipo'); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-3">Maelezo ya Mmiliki</h6>
                                    <p><strong>Jina:</strong> <?php echo e($biz->user->full_name ?? 'Haijulikani'); ?></p>
                                    <p><strong>Simu:</strong> <?php echo e($biz->user->phone_number ?? 'Haipo'); ?></p>
                                    <p><strong>Barua Pepe:</strong> <?php echo e($biz->user->email ?? 'Haipo'); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="custom-modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="closeCustomModal('detailsModal<?php echo e($biz->id); ?>')">Funga</button>
                            <?php if(!$biz->is_open): ?>
                                <form method="POST" action="<?php echo e(route('admin.verify.approve', $biz->id)); ?>" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-success">Idhinisha</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                
                <div class="custom-modal-overlay" id="rejectModal<?php echo e($biz->id); ?>">
                    <div class="custom-modal">
                        <form method="POST" action="<?php echo e(route('admin.verify.reject', $biz->id)); ?>">
                            <?php echo csrf_field(); ?>
                            <div class="custom-modal-header">
                                <h5 class="custom-modal-title">Thibitisha Kukataa Biashara</h5>
                                <button type="button" class="custom-modal-close" onclick="closeCustomModal('rejectModal<?php echo e($biz->id); ?>')">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                            <div class="custom-modal-body">
                                <p>Una uhakika unataka kukataa biashara ya <strong><?php echo e($biz->business_name); ?></strong>?</p>
                                <label class="form-label">Sababu (Hiari):</label>
                                <select class="form-select" name="rejection_reason">
                                    <option value="">-- Chagua Sababu --</option>
                                    <option value="incomplete">Taarifa hazijakamilika</option>
                                    <option value="duplicate">Biashara imeshasajiliwa</option>
                                    <option value="invalid">Taarifa si sahihi</option>
                                    <option value="other">Nyingine</option>
                                </select>
                            </div>
                            <div class="custom-modal-footer">
                                <button type="button" class="btn btn-secondary" onclick="closeCustomModal('rejectModal<?php echo e($biz->id); ?>')">Ghairi</button>
                                <button type="submit" class="btn btn-danger">Kataa</button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-5">
                    <i class="bi bi-check-circle fs-1 text-muted"></i>
                    <h5 class="mt-3">Hakuna Maombi ya Biashara</h5>
                    <p class="text-muted">Biashara zote zimeshughulikiwa kwa sasa.</p>
                </div>
                <?php endif; ?>
            </div>
            <?php if($pending->hasPages()): ?>
            <div class="card-footer bg-white py-3">
                <?php echo e($pending->links()); ?>

            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Custom Modal Functions
    function openCustomModal(id) {
        document.getElementById(id).style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
    function closeCustomModal(id) {
        document.getElementById(id).style.display = 'none';
        document.body.style.overflow = '';
    }
    // Funga unapobofya nje ya modal
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('custom-modal-overlay')) {
            e.target.style.display = 'none';
            document.body.style.overflow = '';
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Activate correct tab based on URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if (tab === 'verify') {
            const verifyTab = document.getElementById('verify-tab');
            if (verifyTab) {
                new bootstrap.Tab(verifyTab).show();
            }
        }
        
        // Store active tab in localStorage
        document.querySelectorAll('#userTabs button').forEach(tab => {
            tab.addEventListener('shown.bs.tab', function(e) {
                localStorage.setItem('activeAdminTab', e.target.getAttribute('data-bs-target'));
            });
        });
        
        // Restore active tab
        const activeTab = localStorage.getItem('activeAdminTab');
        if (activeTab) {
            const tab = document.querySelector(`button[data-bs-target="${activeTab}"]`);
            if (tab) {
                new bootstrap.Tab(tab).show();
            }
        }
    });
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important;
    }
    .bg-opacity-10 {
        --bs-bg-opacity: 0.1;
    }
    .table thead th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.5px;
        color: #dddddd;
    }
    .pagination {
        margin-bottom: 0;
    }
    .nav-tabs .nav-link {
        color: #6c757d;
        font-weight: 500;
        border: none;
        padding: 0.75rem 1.5rem;
    }
    .nav-tabs .nav-link:hover {
        color: #0d6efd;
        border: none;
    }
    .nav-tabs .nav-link.active {
        color: #0d6efd;
        background-color: transparent;
        border-bottom: 3px solid #0d6efd;
    }

    /* ===== Custom Modal System (thabiti) ===== */
    .custom-modal-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        overflow-y: auto;
    }
    .custom-modal {
        position: relative;
        width: 90%;
        max-width: 500px;
        background: white;
        border-radius: 20px;
        box-shadow: 0 25px 50px rgba(0,0,0,0.25);
        margin: 50px auto;
        display: flex;
        flex-direction: column;
        max-height: 80vh;
    }
    .custom-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #eee;
    }
    .custom-modal-title {
        font-weight: 700;
        font-size: 1.25rem;
        margin: 0;
    }
    .custom-modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #666;
    }
    .custom-modal-body {
        padding: 1.25rem 1.5rem;
        overflow-y: auto;
        flex: 1;
    }
    .custom-modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        padding: 1.25rem 1.5rem;
        border-top: 1px solid #eee;
        background-color: white;
    }
    .custom-modal, .custom-modal * {
        transition: none !important;
        animation: none !important;
    }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views\admin\users\index.blade.php ENDPATH**/ ?>