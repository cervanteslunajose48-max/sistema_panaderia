@props([
    'label' => '',
    'value' => '',
    'icon' => null,
    'color' => 'primary',
    'badge' => null
])

@php
    $colorClasses = [
        'primary' => 'border-bakery-gold/30 bg-gradient-to-br from-white to-bakery-cream/20',
        'success' => 'border-green-300 bg-gradient-to-br from-white to-green-50/50',
        'warning' => 'border-orange-300 bg-gradient-to-br from-white to-orange-50/50',
        'info' => 'border-blue-300 bg-gradient-to-br from-white to-blue-50/50',
        'purple' => 'border-purple-300 bg-gradient-to-br from-white to-purple-50/50'
    ];
    
    $badgeColors = [
        'primary' => 'bg-bakery-gold text-white',
        'success' => 'bg-green-500 text-white',
        'warning' => 'bg-orange-500 text-white',
        'info' => 'bg-blue-500 text-white',
        'purple' => 'bg-purple-500 text-white'
    ];
@endphp

<div class="relative rounded-2xl border-2 {{ $colorClasses[$color] ?? $colorClasses['primary'] }} p-6 shadow-lg hover:shadow-xl transition-all duration-300">
    <!-- Badge if provided -->
    @if($badge)
        <div class="absolute top-3 right-3">
            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $badgeColors[$color] ?? $badgeColors['primary'] }}">
                {{ $badge }}
            </span>
        </div>
    @endif
    
    <!-- Icon if provided -->
    @if($icon)
        <div class="mb-3">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-bakery-gold to-bakery-secondary flex items-center justify-center">
                {!! $icon !!}
            </div>
        </div>
    @endif
    
    <!-- Label -->
    <p class="text-sm font-medium text-gray-600 mb-2">{{ $label }}</p>
    
    <!-- Value -->
    <p class="text-3xl font-bold text-gray-900">{{ $value }}</p>
</div>
