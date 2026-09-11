
<header class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm px-3 px-md-4">
    <div class="container-fluid">
        
        <button class="btn btn-outline-secondary d-md-none me-2" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <i class="bi bi-list"></i>
        </button>

        
        <h5 class="mb-0 d-none d-md-block"><?php echo $__env->yieldContent('page-title', 'Dashboard'); ?></h5>

        
        <div class="ms-auto d-flex align-items-center">
            
            <div class="dropdown me-3">
                <a class="text-secondary position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        3
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end p-3" style="min-width: 280px;">
                    <li><h6 class="dropdown-header">Arifa</h6></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-box text-primary"></i> Agizo jipya #GPOA-0412-003</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-person-check text-success"></i> Duka jipya limesajiliwa</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-exclamation-triangle text-warning"></i> Hisa chache: Meko 15kg</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-center" href="#">Tazama Arifa Zote</a></li>
                </ul>
            </div>

            
            <div class="dropdown">
                <a class="d-flex align-items-center text-decoration-none text-dark" href="#" role="button" data-bs-toggle="dropdown">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                        <span class="fs-6 fw-bold"><?php echo e(strtoupper(substr(Auth::user()->full_name ?? 'A', 0, 1))); ?></span>
                    </div>
                    <div class="d-none d-md-block">
                        <span class="fw-medium"><?php echo e(Auth::user()->full_name ?? 'Admin'); ?></span>
                        <br>
                        <small class="text-muted"><?php echo e(ucfirst(Auth::user()->user_type ?? 'admin')); ?></small>
                    </div>
                    <i class="bi bi-chevron-down ms-2"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?php echo e(route('profile.edit')); ?>"><i class="bi bi-person"></i> Profaili</a></li>
                    <li><a class="dropdown-item" href="<?php echo e(route('admin.settings.profile')); ?>"><i class="bi bi-gear"></i> Mipangilio</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="<?php echo e(route('logout')); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right"></i> Toka</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views\partials\header.blade.php ENDPATH**/ ?>