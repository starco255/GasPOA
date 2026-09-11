<?php
    $interfaceUser = auth()->user();
    $savedLanguage = auth()->check() && in_array($interfaceUser?->interface_language, ['sw', 'en'], true)
        ? $interfaceUser->interface_language
        : '';
    $savedTheme = auth()->check() && in_array($interfaceUser?->interface_theme, ['light', 'dark'], true)
        ? $interfaceUser->interface_theme
        : '';
?>
<div class="dropdown interface-preferences"
     data-interface-preferences
     data-authenticated="<?php echo e(auth()->check() ? 'true' : 'false'); ?>"
     data-language="<?php echo e($savedLanguage); ?>"
     data-theme="<?php echo e($savedTheme); ?>"
     <?php if(auth()->guard()->check()): ?> data-preferences-url="<?php echo e(route('interface-preferences.update')); ?>" <?php endif; ?>>
    <button class="btn quick-settings-toggle dropdown-toggle" type="button"
            data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
        <i class="bi bi-sliders2-vertical" aria-hidden="true"></i>
        <span data-i18n="Mipangilio ya haraka">Mipangilio ya haraka</span>
    </button>
    <div class="dropdown-menu dropdown-menu-end interface-settings-menu p-0">
        <div class="interface-settings-heading">
            <i class="bi bi-gear-wide-connected" aria-hidden="true"></i>
            <span data-i18n="Mipangilio ya haraka">Mipangilio ya haraka</span>
        </div>
        <div class="interface-settings-body">
            <label class="interface-setting-row" for="interface-language-select">
                <span class="interface-setting-copy">
                    <i class="bi bi-translate" aria-hidden="true"></i>
                    <span data-i18n="Lugha">Lugha</span>
                </span>
                <select id="interface-language-select" class="form-select form-select-sm" data-language-select aria-label="Language">
                    <option value="sw">Kiswahili</option>
                    <option value="en">English</option>
                </select>
            </label>
            <div class="interface-setting-row">
                <span class="interface-setting-copy">
                    <i class="bi bi-moon-stars-fill" data-theme-icon aria-hidden="true"></i>
                    <span data-theme-label>Mwonekano wa giza</span>
                </span>
                <div class="form-check form-switch m-0">
                    <input class="form-check-input interface-theme-switch" type="checkbox" role="switch"
                           data-theme-toggle aria-label="Badili kwenda mwonekano wa giza">
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\GasPOA\resources\views\components\interface-preferences.blade.php ENDPATH**/ ?>