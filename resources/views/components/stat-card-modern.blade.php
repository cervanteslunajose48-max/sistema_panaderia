@props([
    'title',
    'value',
    'icon' => null,
    'trend' => null, // 'up', 'down', or null
    'trendValue' => null,
    'color' => 'gold' // gold, orange, peach, green, red
])

@php
$colorClasses = [
    'gold' => 'from-amber-400 to-amber-500',
    'orange' => 'from-orange-400 to-orange-500',
    'peach' => 'from-orange-300 to-orange-400',
    'green' => 'from-green-400 to-green-500',
    'red' => 'from-red-400 to-red-500',
    'blue' => 'from-blue-400 to-blue-500',
];
$bgClass = $colorClasses[$color] ?? $colorClasses['gold'];
@endphp

<div class="glass-card-white p-6 rounded-3xl shadow-lg hover:shadow-xl transition-all duration-300 animate-fade-in-up">
    <div class="flex items-center justify-between mb-3">
        <div class="bg-gradient-to-br {{ $bgClass }} p-3 rounded-2xl">
            @if($icon)
                {!! $icon !!}
            @else
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
            @endif
        </div>
        
        @if($trend)
            <span class="badge {{ $trend === 'up' ? 'badge-success' : 'badge-danger' }}">
                @if($trend === 'up')
                    ↑
                @else
                    ↓
                @endif
                {{ $trendValue }}
            </span>
        @endif
    </div>
    
    <p class="text-gray-600 text-sm font-medium mb-1">{{ $title }}</p>
    <h4 class="text-3xl font-bold text-gray-800">{{ $value }}</h4>
    
    @isset($footer)
        <div class="mt-3 pt-3 border-t border-gray-100">
            {{ $footer }}
        </div>
    @endisset
</div>
