@props(['align' => 'right', 'width' => '48'])

<div {{ $attributes->merge(['class' => 'dropdown']) }}>
    <div class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        {{ $trigger }}
    </div>
    <div class="dropdown-menu dropdown-menu-end">
        {{ $content }}
    </div>
</div>
