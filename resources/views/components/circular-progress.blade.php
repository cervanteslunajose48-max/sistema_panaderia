@props([
    'percentage' => 0,
    'color' => 'primary',
    'label' => '',
    'size' => 'md'
])

@php
    $sizeClasses = [
        'sm' => 'w-20 h-20',
        'md' => 'w-24 h-24',
        'lg' => 'w-32 h-32'
    ];
    
    $colorMap = [
        'primary' => '#D4965A',
        'secondary' => '#E9C46A',
        'success' => '#06D6A0',
        'warning' => '#F4A261',
        'danger' => '#EF476F',
        'info' => '#118AB2',
        'purple' => '#9333ea',
        'yellow' => '#eab308',
        'green' => '#22c55e'
    ];
    
    $strokeColor = $colorMap[$color] ?? $colorMap['primary'];
    $radius = 45;
    $circumference = 2 * pi() * $radius;
    $offset = $circumference - ($percentage / 100) * $circumference;
@endphp

<div class="flex flex-col items-center">
    <div class="relative {{ $sizeClasses[$size] ?? $sizeClasses['md'] }}">
        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
            <!-- Background circle -->
            <circle
                cx="50"
                cy="50"
                r="{{ $radius }}"
                fill="none"
                stroke="#e5e7eb"
                stroke-width="8"
            />
            
            <!-- Progress circle -->
            <circle
                cx="50"
                cy="50"
                r="{{ $radius }}"
                fill="none"
                stroke="{{ $strokeColor }}"
                stroke-width="8"
                stroke-linecap="round"
                stroke-dasharray="{{ $circumference }}"
                stroke-dashoffset="{{ $offset }}"
                class="transition-all duration-1000 ease-out"
            />
        </svg>
        
        <!-- Percentage text -->
        <div class="absolute inset-0 flex items-center justify-center">
            <span class="text-2xl font-bold text-gray-800">{{ $percentage }}%</span>
        </div>
    </div>
    
    @if($label)
        <p class="mt-2 text-sm text-gray-600 text-center">{{ $label }}</p>
    @endif
</div>
