<?php $__env->startSection('title', 'Kumbukumbu (Logs)'); ?>
<?php $__env->startSection('page-title', 'Kumbukumbu za Mfumo na Usalama'); ?>

<?php $__env->startSection('content'); ?>
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl">
        <div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body">
            <small class="text-muted text-uppercase">Kumbukumbu Ya Leo</small><h3 class="mb-0 text-primary"><?php echo e(number_format($securityTotals['today'])); ?></h3>
        </div></div>
    </div>
    <div class="col-sm-6 col-xl">
        <div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body">
            <small class="text-muted text-uppercase">Login zisizo salama</small><h3 class="mb-0 text-danger"><?php echo e(number_format($securityTotals['unsafe_logins'])); ?></h3>
        </div></div>
    </div>
    <div class="col-sm-6 col-xl">
        <div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body">
            <small class="text-muted text-uppercase">Mabadiliko ya nywila</small><h3 class="mb-0 text-warning"><?php echo e(number_format($securityTotals['password_events'])); ?></h3>
        </div></div>
    </div>
    <div class="col-sm-6 col-xl">
        <div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body">
            <small class="text-muted text-uppercase">Simu / uthibitishaji</small><h3 class="mb-0 text-success"><?php echo e(number_format($securityTotals['phone_events'])); ?></h3>
        </div></div>
    </div>
</div>


<?php if(!$hasSecurityAuditLogs): ?>
<div class="alert alert-warning border rounded-4 mb-4"><i class="bi bi-exclamation-triangle me-2"></i>Jedwali la kumbukumbu za usalama bado halijawekwa. Tumia <code>database/manual_production_payment_upgrade.sql</code> kwenye database iliyopo; script hiyo huongeza jedwali pekee na haifuti data yoyote.</div>
<?php endif; ?>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-4 pb-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="fw-bold mb-0"><i class="bi bi-activity me-2 text-primary"></i>Shughuli za Mfumo</h5>
        <div class="d-flex flex-wrap gap-2">
            <?php $__currentLoopData = ['all' => ['list-ul', 'Zote'], 'user' => ['person-plus', 'Watumiaji'], 'order' => ['cart', 'Maagizo'], 'business' => ['building', 'Biashara']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => [$icon, $label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('admin.logs.index', ['filter' => $key, 'security_filter' => $securityFilter, 'from_date' => $fromDate, 'to_date' => $toDate])); ?>" class="btn btn-sm <?php echo e($filter === $key ? 'btn-primary' : 'btn-outline-secondary'); ?>"><i class="bi bi-<?php echo e($icon); ?> me-1"></i><?php echo e($label); ?></a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <div class="card-body p-0">
        <?php $__empty_1 = true; $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="border-top px-4 py-3 d-flex gap-3 align-items-start">
                <div class="bg-<?php echo e($activity['color']); ?> bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:40px;height:40px"><i class="bi bi-<?php echo e($activity['icon']); ?> text-<?php echo e($activity['color']); ?>"></i></div>
                <div class="flex-grow-1"><p class="mb-1"><?php echo e($activity['description']); ?></p><small class="text-muted"><i class="bi bi-clock me-1"></i><?php echo e($activity['time']->format('d M Y H:i:s')); ?> (<?php echo e($activity['time_human']); ?>)</small><small class="text-muted ms-3"><i class="bi bi-person me-1"></i><?php echo e($activity['user']); ?></small></div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="text-center py-5 text-muted">Hakuna shughuli zilizopatikana.</div>
        <?php endif; ?>
    </div>
</div> <br>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form method="GET" action="<?php echo e(route('admin.logs.index')); ?>" class="row g-3 align-items-end">
            <div class="col-lg-3"><label class="form-label small text-muted">Aina ya kumbukumbu ya usalama</label>
                <select name="security_filter" class="form-select">
                    <option value="all" <?php if($securityFilter === 'all'): echo 'selected'; endif; ?>>Matukio yote</option>
                    <option value="login" <?php if($securityFilter === 'login'): echo 'selected'; endif; ?>>Kuingia / Login</option>
                    <option value="password" <?php if($securityFilter === 'password'): echo 'selected'; endif; ?>>Nywila / Reset</option>
                    <option value="phone" <?php if($securityFilter === 'phone'): echo 'selected'; endif; ?>>Namba ya simu / OTP</option>
                    <option value="account" <?php if($securityFilter === 'account'): echo 'selected'; endif; ?>>Akaunti</option>
                    <option value="warning" <?php if($securityFilter === 'warning'): echo 'selected'; endif; ?>>Tahadhari na hatari</option>
                </select>
            </div>
            <div class="col-md-3 col-lg-2"><label class="form-label small text-muted">Kuanzia</label><input type="date" name="from_date" value="<?php echo e($fromDate); ?>" class="form-control"></div>
            <div class="col-md-3 col-lg-2"><label class="form-label small text-muted">Hadi</label><input type="date" name="to_date" value="<?php echo e($toDate); ?>" class="form-control"></div>
            <div class="col-md-3 col-lg-2"><button class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i> Chuja</button></div>
            <div class="col-md-3 col-lg-2"><a href="<?php echo e(route('admin.logs.index')); ?>" class="btn btn-outline-secondary w-100">Reset</a></div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-0 pt-4 px-4">
        <h5 class="fw-bold mb-1"><i class="bi bi-shield-lock me-2 text-danger"></i>Ukaguzi wa Usalama</h5>
        <p class="text-muted small mb-0">Inaonyesha mhusika, akaunti iliyoathirika, IP, kifaa na muda kamili. Nywila na OTP hazihifadhiwi hapa.</p>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 security-table">
                <thead><tr><th>Muda</th><th>Tukio</th><th>Mhusika / Akaunti</th><th>Mtandao na Kifaa</th><th>Maelezo</th></tr></thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $securityLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="text-nowrap"><strong><?php echo e($log->occurred_at->format('d M Y')); ?></strong><br><small class="text-muted"><?php echo e($log->occurred_at->format('H:i:s')); ?></small></td>
                        <td><span class="badge bg-<?php echo e($log->severity === 'danger' ? 'danger' : ($log->severity === 'warning' ? 'warning text-dark' : 'info')); ?>"><?php echo e(ucfirst(str_replace('_', ' ', $log->event_type))); ?></span></td>
                        <td><strong><?php echo e($log->actor?->full_name ?? 'Mfumo / Mgeni'); ?></strong><br><small class="text-muted">Akaunti: <?php echo e($log->subject?->full_name ?? 'Haijatambuliwa'); ?></small></td>
                        <td><small class="d-block"><i class="bi bi-geo-alt me-1"></i><?php echo e($log->ip_address ?? 'IP haijapatikana'); ?></small><small class="text-muted device"><?php echo e($log->user_agent ?? 'Kifaa hakijapatikana'); ?></small></td>
                        <td><?php echo e($log->description); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" class="text-center text-muted py-5"><i class="bi bi-shield-check fs-1 d-block mb-2"></i>Hakuna kumbukumbu za usalama kwenye chujio hili.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.card { transition: transform .2s ease, box-shadow .2s ease; }
.card:hover { transform: translateY(-2px); box-shadow: 0 12px 25px rgba(0,0,0,.08)!important; }
.security-table th { white-space: nowrap; font-size: .74rem; text-transform: uppercase; letter-spacing: .04em; color: #6c757d; }
.device { display:block; max-width: 220px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views/admin/logs/index.blade.php ENDPATH**/ ?>