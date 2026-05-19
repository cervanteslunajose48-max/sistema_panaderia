@props([
    'variant' => 'primary', // primary, secondary, success, danger, warning
    'size' => 'md', // sm, md, lg
    'icon' => null,
    'loading' => false,
    'href' => null
])

@php
$baseClasses = 'inline-flex items-center justify-center font-semibold rounded-xl transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2';

$variantClasses = [
    'primary' => 'bg-bakery-gold hover:bg-bakery-gold-dark text-white shadow-soft hover:shadow-lg hover:-translate-y-0.5 focus:ring-bakery-gold',
    'secondary' => 'bg-white hover:bg-gray-50 text-bakery-dark border-2 border-bakery-gold hover:shadow-soft focus:ring-bakery-gold',
    'success' => 'bg-bakery-green hover:bg-bakery-green-light text-white shadow-soft hover:shadow-lg focus:ring-bakery-green',
    'danger' => 'bg-bakery-red hover:bg-bakery-red-light text-white shadow-soft hover:shadow-lg focus:ring-bakery-red',
    'warning' => 'bg-yellow-500 hover:bg-yellow-600 text-white shadow-soft hover:shadow-lg focus:ring-yellow-500',
];

$sizeClasses = [
    'sm' => 'px-4 py-2 text-sm',
    'md' => 'px-6 py-2.5 text-base',
    'lg' => 'px-8 py-3 text-lg',
];

$classes = $baseClasses . ' ' . ($variantClasses[$variant] ?? $variantClasses['primary']) . ' ' . ($sizeClasses[$size] ?? $sizeClasses['md']);
@endphp

@if($href || $attributes->has('href'))
    <a href="{{ $href ?? $attributes->get('href') }}" {{ $attributes->except('href')->merge(['class' => $classes]) }}>
        @if($loading)
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        @elseif($icon)
            <span class="-ml-1 mr-2">
                {!! $icon !!}
            </span>
        @endif
        
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes, 'disabled' => $loading]) }}>
        @if($loading)
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        @elseif($icon)
            <span class="-ml-1 mr-2">
                {!! $icon !!}
            </span>
        @endif
        
        {{ $slot }}
    </button>
@endif
