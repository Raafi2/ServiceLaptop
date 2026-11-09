@props([
    'name',
    'size' => 16,
    'class' => '',
])

@php
$icons = [
    'search' => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
    'plus' => '<path d="M12 5v14M5 12h14"/>',
    'chevR' => '<path d="m9 18 6-6-6-6"/>',
    'chevL' => '<path d="m15 18-6-6 6-6"/>',
    'camera' => '<path d="M4 8h3l2-3h6l2 3h3v11H4z"/><circle cx="12" cy="13" r="3.5"/>',
    'list' => '<path d="M8 6h12M8 12h12M8 18h12M4 6h.01M4 12h.01M4 18h.01"/>',
    'users' => '<circle cx="9" cy="8" r="3.5"/><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6M16 5a3.5 3.5 0 0 1 0 6.5M18 14c2 .7 3 2.5 3 6"/>',
    'laptop' => '<rect x="5" y="5" width="14" height="10" rx="1.5"/><path d="M3 19h18"/>',
    'link' => '<path d="M10 14a4 4 0 0 0 5.7 0l3-3a4 4 0 0 0-5.7-5.7l-1 1M14 10a4 4 0 0 0-5.7 0l-3 3a4 4 0 0 0 5.7 5.7l1-1"/>',
    'logout' => '<path d="M10 5H6v14h4M14 8l4 4-4 4M18 12H9"/>',
    'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
    'checkc' => '<circle cx="12" cy="12" r="9"/><path d="m8.5 12.5 2.5 2.5 4.5-5"/>',
    'arrow' => '<circle cx="12" cy="12" r="9"/><path d="M8 12h8M13 9l3 3-3 3"/>',
    'xc' => '<circle cx="12" cy="12" r="9"/><path d="m9 9 6 6M15 9l-6 6"/>',
    'alert' => '<circle cx="12" cy="12" r="9"/><path d="M12 7.5v5M12 16h.01"/>',
    'x' => '<path d="M6 6l12 12M18 6 6 18"/>',
    'menu' => '<path d="M4 7h16M4 12h16M4 17h16"/>',
    'check' => '<path d="m5 12.5 4.5 4.5L19 7.5"/>',
    'screen' => '<rect x="4" y="5" width="16" height="11" rx="1.5"/><path d="M9 20h6M12 16v4"/>',
    'kb' => '<rect x="3" y="7" width="18" height="10" rx="1.5"/><path d="M7 11h.01M11 11h.01M15 11h.01M7 14h10"/>',
    'chip' => '<rect x="7" y="7" width="10" height="10" rx="1.5"/><path d="M10 3v4M14 3v4M10 17v4M14 17v4M3 10h4M3 14h4M17 10h4M17 14h4"/>',
    'fan' => '<circle cx="12" cy="12" r="2"/><path d="M12 10c0-3 1-5 3-5s2 3-1 5M14 12c3 0 5 1 5 3s-3 2-5-1M12 14c0 3-1 5-3 5s-2-3 1-5M10 12c-3 0-5-1-5-3s3-2 5 1"/>',
    'power' => '<path d="M12 3v8M7 6.5a7 7 0 1 0 10 0"/>',
    'disk' => '<rect x="4" y="6" width="16" height="12" rx="2"/><path d="M8 14h.01M12 14h6"/>',
    'printer' => '<path d="M7 9V4h10v5M7 17H5v-6h14v6h-2"/><rect x="7" y="14" width="10" height="6"/>',
    'copy' => '<rect x="9" y="9" width="11" height="11" rx="2"/><path d="M5 15V6a2 2 0 0 1 2-2h8"/>',
    'tool' => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>',
    'user' => '<circle cx="12" cy="8" r="4"/><path d="M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/>',
    'eye' => '<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>',
    'eyeOff' => '<path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/>',
];
$svg = $icons[$name] ?? '';
@endphp

@if($svg)
<svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="{{ $class }}">
    {!! $svg !!}
</svg>
@endif
