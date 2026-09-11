@props(['name', 'show' => false, 'maxWidth' => 'modal-lg', 'focusable' => false])

<div x-data="{ shown: @js($show) }" x-show="shown" x-on:close.stop="shown = false" x-on:keydown.escape.window="shown = false" class="modal d-block" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog {{ $maxWidth }}" role="document">
        <div class="modal-content p-3">
            {{ $slot }}
        </div>
    </div>
</div>
