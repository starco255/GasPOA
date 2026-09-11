@php
    $interfaceUser = auth()->user();
    $initialLanguage = in_array($interfaceUser?->interface_language, ['sw', 'en'], true)
        ? $interfaceUser->interface_language
        : null;
    $initialTheme = in_array($interfaceUser?->interface_theme, ['light', 'dark'], true)
        ? $interfaceUser->interface_theme
        : null;
@endphp
<script>
    (function () {
        var storedLanguage = null;
        var storedTheme = null;

        try {
            storedLanguage = localStorage.getItem('gaspoa_language');
            storedTheme = localStorage.getItem('gaspoa_theme');
        } catch (error) {
            // The controls continue to work when browser storage is unavailable.
        }

        var language = @json($initialLanguage) || storedLanguage || 'sw';
        var theme = @json($initialTheme) || storedTheme || 'light';
        var root = document.documentElement;

        root.lang = language === 'en' ? 'en' : 'sw';
        root.dataset.theme = theme === 'dark' ? 'dark' : 'light';
    })();
</script>
