<?php $__env->startSection('title', 'Mipangilio ya Akaunti'); ?>
<?php $__env->startSection('page-title', 'Mipangilio ya Akaunti Yangu'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $user = Auth::user();
?>


<div id="toastNotification" class="toast-notification d-none">
    <div class="toast-content">
        <i class="bi bi-check-circle-fill text-success me-2"></i>
        <span id="toastMessage">Anwani imehifadhiwa kwa ufanisi!</span>
    </div>
    <button type="button" class="toast-close" onclick="window.hideToast()">
        <i class="bi bi-x"></i>
    </button>
</div>

<div class="row">
    <div class="col-lg-9 mx-auto">
        
        
        <div class="text-center mb-4">
            <div class="profile-avatar mx-auto">
                <span class="avatar-text"><?php echo e(substr($user->full_name, 0, 1)); ?></span>
            </div>
            <h4 class="fw-bold mt-3 mb-1"><?php echo e($user->full_name); ?></h4>
            <p class="text-muted">
                <i class="bi bi-telephone me-1"></i> <?php echo e($user->phone_number); ?>

                <?php if($user->email): ?>
                    <span class="mx-2">•</span>
                    <i class="bi bi-envelope me-1"></i> <?php echo e($user->email); ?>

                <?php endif; ?>
            </p>
            <span class="badge bg-primary"><?php echo e(ucfirst($user->user_type)); ?></span>
        </div>

        
        <ul class="nav nav-pills profile-tabs mb-4 justify-content-center" id="profileTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link <?php echo e(session('active_tab', 'profile') == 'profile' ? 'active' : ''); ?>" id="profile-tab" data-bs-toggle="pill" data-bs-target="#profile" type="button" role="tab">
                    <i class="bi bi-person me-1"></i> Maelezo Binafsi
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?php echo e(session('active_tab') == 'security' ? 'active' : ''); ?>" id="security-tab" data-bs-toggle="pill" data-bs-target="#security" type="button" role="tab">
                    <i class="bi bi-shield-lock me-1"></i> Usalama
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?php echo e(session('active_tab') == 'address' ? 'active' : ''); ?>" id="address-tab" data-bs-toggle="pill" data-bs-target="#address" type="button" role="tab">
                    <i class="bi bi-geo-alt me-1"></i> Anwani Zangu
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?php echo e(session('active_tab') == 'preferences' ? 'active' : ''); ?>" id="preferences-tab" data-bs-toggle="pill" data-bs-target="#preferences" type="button" role="tab">
                    <i class="bi bi-sliders me-1"></i> Mapendeleo
                </button>
            </li>
        </ul>

        
        <div class="tab-content" id="profileTabsContent">
            
            
            <div class="tab-pane fade <?php echo e(session('active_tab', 'profile') == 'profile' ? 'show active' : ''); ?>" id="profile" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 pt-4 pb-0">
                        <h5 class="fw-bold"><i class="bi bi-person-circle me-2"></i>Maelezo Binafsi</h5>
                        <p class="text-muted small">Sasisha maelezo yako ya msingi.</p>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?php echo e(route('consumer.profile.update')); ?>">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>
                            
                            <div class="mb-3">
                                <label for="full_name" class="form-label fw-semibold">Jina Kamili</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control" id="full_name" name="full_name" 
                                           value="<?php echo e(old('full_name', $user->full_name)); ?>" required>
                                </div>
                                <?php $__errorArgs = ['full_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <small class="text-danger"><?php echo e($message); ?></small>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone_number" class="form-label fw-semibold">Namba ya Simu</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">+255</span>
                                    <input type="tel" class="form-control" id="phone_number" name="phone_number" 
                                           value="<?php echo e(old('phone_number', ltrim($user->phone_number, '+255'))); ?>" 
                                           required
                                           oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                </div>
                                <small class="text-muted">Namba mpya itahitaji kuthibitishwa kwa OTP.</small>
                                <?php $__errorArgs = ['phone_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <small class="text-danger"><?php echo e($message); ?></small>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">Barua Pepe (Si Lazima)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo e(old('email', $user->email)); ?>">
                                </div>
                                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <small class="text-danger"><?php echo e($message); ?></small>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg"></i> Hifadhi Mabadiliko
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                
                <div class="card border-0 shadow-sm rounded-4 mt-4">
                    <div class="card-header bg-white border-0 pt-4 pb-0">
                        <h5 class="fw-bold"><i class="bi bi-shield-check me-2"></i>Uthibitishaji wa Namba</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="mb-1">Hali ya Uthibitishaji</h6>
                                <p class="mb-0">
                                    <?php if($user->is_phone_verified): ?>
                                        <span class="text-success"><i class="bi bi-check-circle-fill"></i> Namba imethibitishwa</span>
                                    <?php else: ?>
                                        <span class="text-warning"><i class="bi bi-exclamation-triangle-fill"></i> Haijathibitishwa</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <?php if(!$user->is_phone_verified): ?>
                            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#otpModal">
                                <i class="bi bi-shield"></i> Thibitisha Sasa
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            
            <div class="tab-pane fade <?php echo e(session('active_tab') == 'security' ? 'show active' : ''); ?>" id="security" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 pt-4 pb-0">
                        <h5 class="fw-bold"><i class="bi bi-lock-fill me-2"></i>Badilisha Nywila</h5>
                        <p class="text-muted small">Hakikisha unatumia nywila imara yenye herufi, namba, na alama.</p>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?php echo e(route('consumer.profile.password')); ?>">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            
                            <div class="mb-3">
                                <label for="current_password" class="form-label fw-semibold">Nywila ya Sasa</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-key"></i></span>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="current_password">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <small class="text-danger"><?php echo e($message); ?></small>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">Nywila Mpya</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Angalau herufi 8, jumuisha namba na alama.</small>
                                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <small class="text-danger"><?php echo e($message); ?></small>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label fw-semibold">Thibitisha Nywila Mpya</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password_confirmation">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-shield-lock"></i> Badilisha Nywila
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                
                <div class="card border-0 shadow-sm rounded-4 mt-4">
                    <div class="card-header bg-white border-0 pt-4 pb-0">
                        <h5 class="fw-bold"><i class="bi bi-shield-shaded me-2"></i>Usalama wa Ziada</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="mb-1">Uthibitishaji wa Hatua Mbili (2FA)</h6>
                                <p class="text-muted small mb-0">Ongeza safu ya ziada ya usalama kwa akaunti yako.</p>
                            </div>
                            <button class="btn btn-outline-secondary btn-sm" disabled>
                                Inakuja Hivi Karibuni
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            
            <div class="tab-pane fade <?php echo e(session('active_tab') == 'address' ? 'show active' : ''); ?>" id="address" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold"><i class="bi bi-geo-alt-fill me-2"></i>Anwani Zilizohifadhiwa</h5>
                            <p class="text-muted small">Hifadhi anwani zako za mara kwa mara kwa urahisi wa kuagiza.</p>
                        </div>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAddressModal" id="addAddressBtn">
                            <i class="bi bi-plus-lg"></i> Ongeza Anwani
                        </button>
                    </div>
                    <div class="card-body">
                        
                        <div id="addressesLoading" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Inapakia...</span>
                            </div>
                            <p class="mt-2 text-muted">Inapakia anwani zako...</p>
                        </div>

                        
                        <div id="addressesList" class="d-none"></div>

                        
                        <div id="addressesEmpty" class="text-center py-4 text-muted d-none">
                            <i class="bi bi-geo-alt display-4 opacity-50"></i>
                            <p class="mt-2">Bado huna anwani iliyohifadhiwa.</p>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                                <i class="bi bi-plus-lg"></i> Ongeza Anwani
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            
            <div class="tab-pane fade <?php echo e(session('active_tab') == 'preferences' ? 'show active' : ''); ?>" id="preferences" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 pt-4 pb-0">
                        <h5 class="fw-bold"><i class="bi bi-sliders me-2"></i>Mapendeleo ya Arifa</h5>
                        <p class="text-muted small">Chagua jinsi unavyotaka kupokea taarifa.</p>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?php echo e(route('consumer.preferences.update')); ?>" id="preferencesForm">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="sms_notifications" id="sms_notifications" value="1" checked>
                                <label class="form-check-label fw-semibold" for="sms_notifications">
                                    <i class="bi bi-chat-dots me-1"></i> Arifa za SMS
                                </label>
                                <p class="text-muted small ms-4">Pokea taarifa za agizo kwa SMS.</p>
                            </div>
                            
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="email_notifications" id="email_notifications" value="1" checked>
                                <label class="form-check-label fw-semibold" for="email_notifications">
                                    <i class="bi bi-envelope me-1"></i> Arifa za Barua Pepe
                                </label>
                                <p class="text-muted small ms-4">Pokea risiti na taarifa kwa barua pepe.</p>
                            </div>
                            
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="promotional_notifications" id="promotional_notifications" value="1">
                                    <label class="form-check-label fw-semibold" for="promotional_notifications">
                                        <i class="bi bi-megaphone me-1"></i> Matangazo na Ofa
                                    </label>
                                    <p class="text-muted small ms-4">Pokea taarifa za ofa maalum na punguzo.</p>
                                </div>
                                
                                <div id="preferencesError" class="text-danger small mb-3 d-none"></div>
                                
                                <div class="d-grid mt-4">
                                    <button type="submit" class="btn btn-primary" id="savePreferencesBtn">
                                        <i class="bi bi-check-lg"></i> Hifadhi Mapendeleo
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="otpModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form method="POST" action="<?php echo e(route('consumer.profile.verify-phone')); ?>" id="otpForm">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold">Thibitisha Namba ya Simu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted mb-2">Tumetuma namba ya siri (OTP) kwenye barua pepe yako. Ingiza hapa chini kuthibitisha.</p>
                        <div class="alert alert-light border py-2 px-3 small mb-3">
                            <i class="bi bi-hourglass-split text-warning me-1"></i>
                            OTP inaisha ndani ya <span id="otpExpiryTime">--:--</span>
                        </div>
                        
                        <div class="mb-3">
                            <label for="otp_code" class="form-label">Namba ya Uthibitisho (OTP)</label>
                            <input type="text" class="form-control form-control-lg text-center" id="otp_code" name="otp" 
                                   placeholder="000000" maxlength="6" required autocomplete="off"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                        <div id="otpError" class="text-danger small mb-2 d-none"></div>
                        <div id="otpSuccess" class="text-success small mb-2 d-none"></div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Ghairi</button>
                        <button type="submit" class="btn btn-primary" id="verifyOtpBtn">Thibitisha</button>
                    </div>
                    <div class="text-center pb-3">
                        <small class="text-muted">Hukupokea OTP? </small>
                        <button type="button" class="btn btn-link btn-sm p-0" id="resendOtpBtn" type="button">Tuma Tena</button>
                        <span id="resendTimer" class="text-muted small ms-2"></span>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="addAddressModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <form id="addressForm">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="address_id" id="address_id">
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold" id="addressModalTitle">Ongeza Anwani Mpya</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Lebo ya Anwani <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="label" id="address_label" 
                                   placeholder="Mf: Nyumbani, Kazini, Nyingine" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Anwani Kamili <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="address" id="address_text" rows="3" 
                                      placeholder="Mtaa, Nyumba, Wilaya..." required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Latitude</label>
                                <input type="text" class="form-control" name="latitude" id="address_latitude" 
                                       placeholder="-6.792354">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Longitude</label>
                                <input type="text" class="form-control" name="longitude" id="address_longitude" 
                                       placeholder="39.208328">
                            </div>
                        </div>
                        <div class="mb-3">
                            <button type="button" class="btn btn-outline-primary btn-sm w-100" id="getLocationForAddressBtn">
                                <i class="bi bi-geo-alt"></i> Tumia GPS Kupata Mahali
                            </button>
                            <div id="addressLocationStatus" class="small text-muted mt-1"></div>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_default" id="address_is_default" value="1">
                            <label class="form-check-label" for="address_is_default">Weka kama anwani chaguo-msingi</label>
                        </div>
                        <div id="addressError" class="text-danger small mt-2 d-none"></div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Ghairi</button>
                        <button type="submit" class="btn btn-primary" id="saveAddressBtn">Hifadhi Anwani</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// =====================================================
// ADDRESS FUNCTIONS - GLOBAL SCOPE
// =====================================================
let currentAddresses = [];

window.loadAddresses = function() {
    const addressesLoading = document.getElementById('addressesLoading');
    const addressesList = document.getElementById('addressesList');
    const addressesEmpty = document.getElementById('addressesEmpty');

    if (!addressesLoading || !addressesList || !addressesEmpty) return;

    addressesLoading.classList.remove('d-none');
    addressesList.classList.add('d-none');
    addressesEmpty.classList.add('d-none');

    fetch('/consumer/addresses', {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        addressesLoading.classList.add('d-none');
        if (data.success && data.addresses.length > 0) {
            currentAddresses = data.addresses;
            window.displayAddresses(data.addresses);
            addressesList.classList.remove('d-none');
        } else {
            addressesEmpty.classList.remove('d-none');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        addressesLoading.classList.add('d-none');
        addressesEmpty.classList.remove('d-none');
    });
};

window.displayAddresses = function(addresses) {
    const addressesList = document.getElementById('addressesList');
    if (!addressesList) return;
    
    let html = '';
    addresses.forEach(addr => {
        html += `
            <div class="address-card mb-3 ${addr.is_default ? 'border-primary' : ''}" data-address-id="${addr.id}">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="fw-bold mb-1">
                            ${addr.label}
                            ${addr.is_default ? '<span class="badge bg-primary ms-2">Chaguo-msingi</span>' : ''}
                        </h6>
                        <p class="text-muted mb-2">${addr.address}</p>
                        ${addr.latitude && addr.longitude ? 
                            `<small class="text-muted"><i class="bi bi-pin-map"></i> ${addr.latitude}, ${addr.longitude}</small>` : ''}
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown" data-bs-display="static">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#" onclick="window.editAddress(${addr.id}); return false;">
                                <i class="bi bi-pencil"></i> Hariri
                            </a></li>
                            
                            ${addr.is_default ? `
                            <li><a class="dropdown-item" href="#" onclick="window.removeDefaultAddress(${addr.id}); return false;">
                                <i class="bi bi-star"></i> Ondoa Chaguo-msingi
                            </a></li>
                            ` : `
                            <li><a class="dropdown-item" href="#" onclick="window.setDefaultAddress(${addr.id}); return false;">
                                <i class="bi bi-star-fill"></i> Weka kama Msingi
                            </a></li>
                            `}
                            
                            ${!addr.is_default ? `
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#" onclick="window.deleteAddress(${addr.id}); return false;">
                                <i class="bi bi-trash"></i> Futa
                            </a></li>
                            ` : `
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-muted" href="#" onclick="return false;" style="cursor: not-allowed;">
                                <i class="bi bi-trash"></i> Futa (Ondoa msingi kwanza)
                            </a></li>
                            `}
                        </ul>
                    </div>
                </div>
            </div>
        `;
    });
    addressesList.innerHTML = html;
};

window.setDefaultAddress = function(id) {
    fetch(`/consumer/addresses/${id}/default`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.loadAddresses();
            window.showToast('Anwani imewekwa kama chaguo-msingi!', 'success');
        } else {
            window.showToast(data.message || 'Imeshindikana kuweka anwani kama msingi.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        window.showToast('Imeshindikana kuweka anwani kama msingi.', 'error');
    });
};

window.removeDefaultAddress = function(id) {
    fetch(`/consumer/addresses/${id}/remove-default`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.loadAddresses();
            window.showToast('Chaguo-msingi kimeondolewa!', 'success');
        } else {
            window.showToast(data.message || 'Imeshindikana kuondoa chaguo-msingi.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        window.showToast('Imeshindikana kuondoa chaguo-msingi.', 'error');
    });
};

window.deleteAddress = function(id) {
    if (!confirm('Una uhakika unataka kufuta anwani hii?')) return;
    
    fetch(`/consumer/addresses/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.loadAddresses();
            window.showToast('Anwani imefutwa kwa ufanisi!', 'success');
        } else {
            window.showToast(data.message || 'Imeshindikana kufuta anwani.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        window.showToast('Imeshindikana kufuta anwani.', 'error');
    });
};

window.editAddress = function(id) {
    const addr = currentAddresses.find(a => a.id == id);
    if (!addr) {
        window.showToast('Anwani haikupatikana.', 'error');
        return;
    }

    document.getElementById('address_id').value = addr.id;
    document.getElementById('address_label').value = addr.label || '';
    document.getElementById('address_text').value = addr.address || '';
    document.getElementById('address_latitude').value = addr.latitude || '';
    document.getElementById('address_longitude').value = addr.longitude || '';
    document.getElementById('address_is_default').checked = addr.is_default || false;
    document.getElementById('addressModalTitle').textContent = 'Hariri Anwani';
    document.getElementById('saveAddressBtn').innerHTML = '<i class="bi bi-pencil"></i> Sasisha Anwani';

    const modal = new bootstrap.Modal(document.getElementById('addAddressModal'));
    modal.show();
};

window.showToast = function(message, type = 'success') {
    const toast = document.getElementById('toastNotification');
    const toastMessage = document.getElementById('toastMessage');
    const icon = toast?.querySelector('.toast-content i');
    
    if (!toast || !toastMessage) return;
    
    toastMessage.textContent = message;
    
    if (type === 'error') {
        toast.classList.add('error');
        if (icon) icon.className = 'bi bi-exclamation-circle-fill text-danger me-2';
    } else {
        toast.classList.remove('error');
        if (icon) icon.className = 'bi bi-check-circle-fill text-success me-2';
    }
    
    toast.classList.remove('d-none');
    
    if (window.toastTimeout) clearTimeout(window.toastTimeout);
    window.toastTimeout = setTimeout(() => {
        toast.classList.add('d-none');
    }, 4000);
};

window.hideToast = function() {
    const toast = document.getElementById('toastNotification');
    if (toast) toast.classList.add('d-none');
};

document.addEventListener('DOMContentLoaded', function() {
    // =====================================================
    // TOGGLE PASSWORD VISIBILITY
    // =====================================================
    document.querySelectorAll('.toggle-password').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
    });
    
    // =====================================================
    // OTP MODAL
    // =====================================================
    const otpModal = document.getElementById('otpModal');
    if (otpModal) {
        otpModal.addEventListener('shown.bs.modal', function() {
            sendOtpRequest();
        });
        
        otpModal.addEventListener('hidden.bs.modal', function() {
            if (otpExpiryInterval) clearInterval(otpExpiryInterval);
        });
    }
    
    const resendOtpBtn = document.getElementById('resendOtpBtn');
    if (resendOtpBtn) {
        resendOtpBtn.addEventListener('click', sendOtpRequest);
    }
    
    const otpForm = document.getElementById('otpForm');
    if (otpForm) {
        const otpInput = document.getElementById('otp_code');
        otpInput?.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length === 6) {
                otpForm.requestSubmit();
            }
        });

        otpForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const verifyBtn = document.getElementById('verifyOtpBtn');
            const errorDiv = document.getElementById('otpError');
            
            verifyBtn.disabled = true;
            verifyBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Inathibitisha...';
            errorDiv.classList.add('d-none');
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(otpModal).hide();
                    window.location.reload();
                } else {
                    errorDiv.textContent = data.message || 'OTP si sahihi au imeisha muda.';
                    errorDiv.classList.remove('d-none');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                errorDiv.textContent = 'Imeshindikana kuthibitisha. Tafadhali jaribu tena.';
                errorDiv.classList.remove('d-none');
            })
            .finally(() => {
                verifyBtn.disabled = false;
                verifyBtn.innerHTML = 'Thibitisha';
            });
        });
    }

    // =====================================================
    // ADDRESS FORM SUBMISSION
    // =====================================================
    const addressForm = document.getElementById('addressForm');
    const addressModal = document.getElementById('addAddressModal');
    const addressId = document.getElementById('address_id');
    const addressLabel = document.getElementById('address_label');
    const addressText = document.getElementById('address_text');
    const addressLatitude = document.getElementById('address_latitude');
    const addressLongitude = document.getElementById('address_longitude');
    const addressIsDefault = document.getElementById('address_is_default');
    const addressError = document.getElementById('addressError');
    const saveAddressBtn = document.getElementById('saveAddressBtn');
    const addressModalTitle = document.getElementById('addressModalTitle');
    const addressLocationStatus = document.getElementById('addressLocationStatus');

    const getLocationForAddressBtn = document.getElementById('getLocationForAddressBtn');
    if (getLocationForAddressBtn) {
        getLocationForAddressBtn.addEventListener('click', function() {
            if (navigator.geolocation) {
                addressLocationStatus.innerHTML = '<span class="text-info"><i class="bi bi-arrow-repeat spin"></i> Inatafuta location...</span>';
                navigator.geolocation.getCurrentPosition(function(position) {
                    addressLatitude.value = position.coords.latitude.toFixed(6);
                    addressLongitude.value = position.coords.longitude.toFixed(6);
                    addressLocationStatus.innerHTML = '<span class="text-success"><i class="bi bi-check-circle-fill"></i> Location imepatikana!</span>';
                    setTimeout(() => { addressLocationStatus.innerHTML = ''; }, 3000);
                }, function() {
                    addressLocationStatus.innerHTML = '<span class="text-danger"><i class="bi bi-exclamation-triangle-fill"></i> Imeshindikana kupata GPS.</span>';
                });
            } else {
                addressLocationStatus.innerHTML = '<span class="text-danger"><i class="bi bi-exclamation-triangle-fill"></i> Kifaa hakitumii GPS.</span>';
            }
        });
    }

    function resetAddressForm() {
        addressId.value = '';
        addressLabel.value = '';
        addressText.value = '';
        addressLatitude.value = '';
        addressLongitude.value = '';
        addressIsDefault.checked = false;
        addressError.classList.add('d-none');
        addressLocationStatus.innerHTML = '';
        addressModalTitle.textContent = 'Ongeza Anwani Mpya';
        saveAddressBtn.innerHTML = '<i class="bi bi-check-lg"></i> Hifadhi Anwani';
    }

    if (addressForm) {
        addressForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!addressLabel.value.trim()) {
                addressError.textContent = 'Tafadhali ingiza lebo ya anwani.';
                addressError.classList.remove('d-none');
                return;
            }
            if (!addressText.value.trim()) {
                addressError.textContent = 'Tafadhali ingiza anwani kamili.';
                addressError.classList.remove('d-none');
                return;
            }

            const id = addressId.value;
            const url = id ? `/consumer/addresses/${id}` : '/consumer/addresses';

            const formData = new FormData();
            formData.append('label', addressLabel.value.trim());
            formData.append('address', addressText.value.trim());
            if (addressLatitude.value) formData.append('latitude', addressLatitude.value);
            if (addressLongitude.value) formData.append('longitude', addressLongitude.value);
            formData.append('is_default', addressIsDefault.checked ? '1' : '0');
            if (id) formData.append('_method', 'PUT');

            saveAddressBtn.disabled = true;
            saveAddressBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Inahifadhi...';
            addressError.classList.add('d-none');

            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(addressModal).hide();
                    window.loadAddresses();
                    resetAddressForm();
                    window.showToast(data.message || 'Anwani imehifadhiwa!', 'success');
                } else {
                    addressError.textContent = data.message || 'Imeshindikana kuhifadhi.';
                    addressError.classList.remove('d-none');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                addressError.textContent = 'Imeshindikana kuhifadhi. Tafadhali jaribu tena.';
                addressError.classList.remove('d-none');
            })
            .finally(() => {
                saveAddressBtn.disabled = false;
                saveAddressBtn.innerHTML = id ? '<i class="bi bi-pencil"></i> Sasisha Anwani' : '<i class="bi bi-check-lg"></i> Hifadhi Anwani';
            });
        });
    }

    if (addressModal) {
        addressModal.addEventListener('hidden.bs.modal', resetAddressForm);
    }
    
    const addAddressBtn = document.getElementById('addAddressBtn');
    if (addAddressBtn) {
        addAddressBtn.addEventListener('click', resetAddressForm);
    }
    
    const addressTab = document.getElementById('address-tab');
    if (addressTab) {
        addressTab.addEventListener('shown.bs.tab', function() {
            window.loadAddresses();
        });
    }

    if (window.location.hash === '#address') {
        window.loadAddresses();
    }
    
    // =====================================================
    // PREFERENCES FORM SUBMISSION
    // =====================================================
    const preferencesForm = document.getElementById('preferencesForm');
    if (preferencesForm) {
        const savePreferencesBtn = document.getElementById('savePreferencesBtn');
        const preferencesError = document.getElementById('preferencesError');
        
        preferencesForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            if (savePreferencesBtn) {
                savePreferencesBtn.disabled = true;
                savePreferencesBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Inahifadhi...';
            }
            if (preferencesError) {
                preferencesError.classList.add('d-none');
            }
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.showToast(data.message || 'Mapendeleo yamehifadhiwa!', 'success');
                } else {
                    if (preferencesError) {
                        preferencesError.textContent = data.message || 'Imeshindikana kuhifadhi.';
                        preferencesError.classList.remove('d-none');
                    }
                    window.showToast(data.message || 'Imeshindikana kuhifadhi.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (preferencesError) {
                    preferencesError.textContent = 'Imeshindikana kuhifadhi. Tafadhali jaribu tena.';
                    preferencesError.classList.remove('d-none');
                }
                window.showToast('Imeshindikana kuhifadhi mapendeleo.', 'error');
            })
            .finally(() => {
                if (savePreferencesBtn) {
                    savePreferencesBtn.disabled = false;
                    savePreferencesBtn.innerHTML = '<i class="bi bi-check-lg"></i> Hifadhi Mapendeleo';
                }
            });
        });
    }
    
    // =====================================================
    // OTP FUNCTIONS (IMEBORESHWA)
    // =====================================================
    let resendCountdown = 0;
    let resendTimerInterval = null;
    let otpExpiryInterval = null;
    
    function sendOtpRequest() {
        if (resendCountdown > 0) {
            window.showToast('Tafadhali subiri sekunde ' + resendCountdown, 'error');
            return;
        }
        
        const resendBtn = document.getElementById('resendOtpBtn');
        const timerSpan = document.getElementById('resendTimer');
        const errorDiv = document.getElementById('otpError');
        const successDiv = document.getElementById('otpSuccess');
        
        if (resendBtn) resendBtn.disabled = true;
        if (errorDiv) errorDiv.classList.add('d-none');
        if (successDiv) successDiv.classList.add('d-none');
        
        fetch('/consumer/profile/resend-otp', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (errorDiv) errorDiv.classList.add('d-none');
                
                if (data.expires_at) updateExpiryTimer(data.expires_at);
                
                resendCountdown = 60;
                updateResendTimer();
                if (resendTimerInterval) clearInterval(resendTimerInterval);
                resendTimerInterval = setInterval(updateResendTimer, 1000);
                
                document.getElementById('otp_code').placeholder = 'Ingiza OTP';
                document.getElementById('otp_code').focus();
                
                if (successDiv) {
                    successDiv.textContent = 'OTP mpya imetumwa!';
                    successDiv.classList.remove('d-none');
                    setTimeout(() => successDiv.classList.add('d-none'), 3000);
                }
            } else {
                window.showToast(data.message || 'Imeshindikana kutuma OTP.', 'error');
                if (resendBtn) resendBtn.disabled = false;
            }
        })
        .catch(error => {
            window.showToast('Imeshindikana kutuma OTP.', 'error');
            if (resendBtn) resendBtn.disabled = false;
        });
    }
    
    function updateExpiryTimer(expiresAt) {
        if (otpExpiryInterval) clearInterval(otpExpiryInterval);
        
        otpExpiryInterval = setInterval(() => {
            const now = Math.floor(Date.now() / 1000);
            const remaining = expiresAt - now;
            const expirySpan = document.getElementById('otpExpiryTime');
            
            if (expirySpan) {
                if (remaining > 0) {
                    const minutes = Math.floor(remaining / 60);
                    const seconds = remaining % 60;
                    expirySpan.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                } else {
                    expirySpan.textContent = 'Imekwisha muda';
                    clearInterval(otpExpiryInterval);
                }
            } else {
                clearInterval(otpExpiryInterval);
            }
        }, 1000);
    }
    
    function updateResendTimer() {
        const resendBtn = document.getElementById('resendOtpBtn');
        const timerSpan = document.getElementById('resendTimer');
        if (resendCountdown > 0) {
            if (timerSpan) timerSpan.textContent = `(Subiri ${resendCountdown}s)`;
            resendCountdown--;
        } else {
            if (timerSpan) timerSpan.textContent = '';
            if (resendBtn) resendBtn.disabled = false;
            if (resendTimerInterval) clearInterval(resendTimerInterval);
            resendTimerInterval = null;
        }
    }
    
    window.addEventListener('beforeunload', function() {
        if (resendTimerInterval) clearInterval(resendTimerInterval);
        if (otpExpiryInterval) clearInterval(otpExpiryInterval);
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    /* Profile Avatar */
    .profile-avatar {
        width: 90px;
        height: 90px;
        background: linear-gradient(145deg, #FF6B35, #E85D2C);
        border-radius: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 25px rgba(255, 107, 53, 0.3);
    }
    
    .avatar-text {
        font-size: 2.5rem;
        font-weight: 700;
        color: white;
        text-transform: uppercase;
    }
    
    /* Profile Tabs */
    .profile-tabs {
        gap: 8px;
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 15px;
    }
    
    .profile-tabs .nav-link {
        border-radius: 30px;
        padding: 10px 20px;
        color: #6c757d;
        font-weight: 500;
        transition: all 0.2s;
    }
    
    .profile-tabs .nav-link:hover {
        background-color: #f8f9fa;
        color: #FF6B35;
    }
    
    .profile-tabs .nav-link.active {
        background: linear-gradient(145deg, #FF6B35, #E85D2C);
        color: white;
    }
    
    /* Address Card */
    .address-card {
        padding: 15px 18px;
        background: white;
        border: 1.5px solid #e9ecef;
        border-radius: 16px;
        transition: all 0.2s;
    }
    
    .address-card:hover {
        border-color: #FF6B35;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    .address-card.border-primary {
        border-color: #FF6B35;
        background: linear-gradient(145deg, #ffffff, #fff8f5);
    }
    
    /* Input Groups */
    .input-group-text {
        border-right: none;
    }
    
    .input-group .form-control {
        border-left: none;
    }
    
    .input-group .form-control:focus {
        border-color: #ced4da;
        box-shadow: none;
    }
    
    .input-group:focus-within {
        box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
        border-radius: 8px;
    }
    
    .input-group:focus-within .input-group-text,
    .input-group:focus-within .form-control {
        border-color: #FF6B35;
    }
    
    /* Form Switch */
    .form-switch .form-check-input {
        width: 3em;
        height: 1.5em;
        cursor: pointer;
    }
    
    .form-switch .form-check-input:checked {
        background-color: #FF6B35;
        border-color: #FF6B35;
    }

    .spin {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* Dropdown Menu */
    .dropdown-menu {
        border: none !important;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        padding: 8px 0;
        min-width: 180px;
        background: linear-gradient(145deg, #a3a3e4 0%, #b3b896 100%) !important;
        backdrop-filter: blur(10px);
        animation: dropdownFadeIn 0.2s ease;
    }
    
    @keyframes dropdownFadeIn {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .dropdown-item {
        padding: 10px 16px;
        font-size: 0.85rem;
        font-weight: 500;
        color: #1A1A2E;
        transition: all 0.15s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        background: transparent;
    }
    
    .dropdown-item i {
        width: 18px;
        color: #1A1A2E;
        font-size: 1rem;
        opacity: 0.8;
    }
    
    .dropdown-item:hover {
        background: rgba(255, 255, 255, 0.2) !important;
        color: #FF6B35;
    }
    
    .dropdown-item:hover i {
        color: #FF6B35;
        opacity: 1;
    }
    
    .dropdown-item.text-danger {
        color: #dc3545 !important;
    }
    
    .dropdown-item.text-danger i {
        color: #dc3545 !important;
    }
    
    .dropdown-item.text-danger:hover {
        background: rgba(220, 53, 69, 0.15) !important;
        color: #dc3545 !important;
    }
    
    .dropdown-item.text-muted {
        color: rgba(26, 26, 46, 0.5) !important;
        cursor: not-allowed;
    }
    
    .dropdown-divider {
        margin: 6px 0;
        border-color: rgba(255, 255, 255, 0.3);
        opacity: 0.6;
    }

    .dropdown-menu-end {
        right: 0;
        left: auto;
    }
    
    .dropdown-menu.show {
        display: block;
        max-height: 300px;
        overflow-y: auto;
    }
    
    .dropdown-menu::-webkit-scrollbar {
        width: 5px;
    }
    
    .dropdown-menu::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 10px;
    }
    
    .dropdown-menu::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.5);
        border-radius: 10px;
    }

    /* Toast Notification */
    .toast-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-width: 300px;
        max-width: 450px;
        background: white;
        border-radius: 12px;
        padding: 14px 18px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        border-left: 4px solid #198754;
        animation: slideInRight 0.3s ease;
    }
    
    .toast-notification.d-none {
        display: none !important;
    }
    
    .toast-content {
        display: flex;
        align-items: center;
        font-weight: 500;
        color: #1A1A2E;
    }
    
    .toast-close {
        background: none;
        border: none;
        color: #6c757d;
        cursor: pointer;
        padding: 0;
        margin-left: 10px;
        font-size: 1.2rem;
        transition: color 0.2s;
    }
    
    .toast-close:hover {
        color: #1A1A2E;
    }
    
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    .toast-notification.error {
        border-left-color: #dc3545;
    }
    
    .toast-notification.error .toast-content i {
        color: #dc3545 !important;
    }
    
    /* Badge */
    .badge.bg-primary {
        background: linear-gradient(145deg, #FF6B35, #E85D2C) !important;
        font-weight: 500;
        padding: 5px 10px;
        font-size: 0.7rem;
    }
    
    /* Address Card Small */
    .address-card small {
        display: block;
        margin-top: 8px;
        font-size: 0.7rem;
        color: #6c757d;
        letter-spacing: 0.3px;
    }
    
    .address-card small i {
        margin-right: 5px;
        color: #FF6B35;
    }
    
    /* Dropdown Button */
    .btn-outline-secondary {
        border-radius: 10px;
        padding: 6px 10px;
        border-color: #e9ecef;
        color: #6c757d;
        transition: all 0.2s;
    }
    
    .btn-outline-secondary:hover {
        background-color: #f8f9fa;
        border-color: #FF6B35;
        color: #FF6B35;
    }
    
    /* Address Card Overflow */
    .address-card {
        position: relative;
        overflow: visible !important;
    }
    
    .address-card .fw-bold {
        color: #1A1A2E;
        font-size: 1rem;
    }
    
    .address-card .text-muted {
        color: #6c757d !important;
        line-height: 1.5;
    }

    .address-card .dropdown {
        position: relative;
    }

    .address-card .dropdown-menu {
        position: absolute;
        z-index: 9999 !important;
        right: 0 !important;
        left: auto !important;
        top: 100% !important;
        margin-top: 8px !important;
    }

    .card-body, #addressesList, #addressesList > div {
        overflow: visible !important;
    }

    .tab-pane, .tab-content, .card, .row, .col-lg-9 {
        overflow: visible !important;
    }
    
    #otpExpiryTime {
        font-weight: 600;
        color: #dc3545;
    }
    
    #otpSuccess {
        background: rgba(25, 135, 84, 0.1);
        padding: 8px 12px;
        border-radius: 8px;
        border-left: 3px solid #198754;
    }
    
    #otpError {
        background: rgba(220, 53, 69, 0.1);
        padding: 8px 12px;
        border-radius: 8px;
        border-left: 3px solid #dc3545;
    }
    
    /* Loading spinner kwenye button */
    .btn .spinner-border {
        width: 1rem;
        height: 1rem;
        border-width: 0.15em;
    }
    
    /* Responsive */
    @media (max-width: 576px) {
        .profile-tabs .nav-link {
            padding: 8px 12px;
            font-size: 0.85rem;
        }
        .profile-avatar {
            width: 70px;
            height: 70px;
            border-radius: 20px;
        }
        .avatar-text {
            font-size: 2rem;
        }
        .dropdown-menu {
            min-width: 160px;
            max-width: 200px;
        }
        .dropdown-item {
            padding: 8px 14px;
            font-size: 0.8rem;
        }
        .address-card {
            padding: 12px 14px;
        }
        .address-card .fw-bold {
            font-size: 0.9rem;
        }
        .toast-notification {
            min-width: auto;
            max-width: calc(100vw - 40px);
            top: 10px;
            right: 10px;
            left: 10px;
        }
    }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views\consumer\profile\edit.blade.php ENDPATH**/ ?>