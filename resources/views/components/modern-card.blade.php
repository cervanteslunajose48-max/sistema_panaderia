@props(['variant' => 'default', 'elevated' => false])

@php
    $baseClasses = 'bg-white rounded-2xl p-6 transition-all duration-300';
    $variantClasses = [
        'default' => 'shadow-soft hover:shadow-lg',
        'elevated' => 'shadow-lg hover:shadow-xl hover:-translate-y-1',
        'bordered' => 'border-2 border-bakery-gold shadow-softer hover:shadow-soft',
        'glass' => 'glass-card-white',
    ];
    $classes = $baseClasses . ' ' . ($variantClasses[$variant] ?? $variantClasses['default']);
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>