@props(['active' => false])

<a {{ $attributes->class(['nav-link', 'active' => $active]) }}>{{ $slot }}</a>
