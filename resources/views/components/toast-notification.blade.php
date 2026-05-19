@props(['type' => 'success', 'message', 'id' => null])

@php
    $id = $id ?? 'toast-' . uniqid();
    $iconClasses = [
        'success' => 'text-green-500',
        'error' => 'text-red-500',
        'warning' => 'text-yellow-500',
        'info' => 'text-blue-500',
    ];
    $bgClasses = [
        'success' => 'border-l-4 border-green-500 bg-green-50',
        'error' => 'border-l-4 border-red-500 bg-red-50',
        'warning' => 'border-l-4 border-yellow-500 bg-yellow-50',
        'info' => 'border-l-4 border-blue-500 bg-blue-50',
    ];
@endphp

<div id="{{ $id }}"
    class="fixed top-4 right-4 z-50 max-w-sm w-full {{ $bgClasses[$type] ?? $bgClasses['info'] }} rounded-xl shadow-lg p-4 animate-slide-in"
    x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition>

    <div class="flex items-start">
        <div class="flex-shrink-0">
            @if($type === 'success')
                <svg class="h-6 w-6 {{ $iconClasses[$type] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            @elseif($type === 'error')
                <svg class="h-6 w-6 {{ $iconClasses[$type] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            @elseif($type === 'warning')
                <svg class="h-6 w-6 {{ $iconClasses[$type] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
            @else
                <svg class="h-6 w-6 {{ $iconClasses[$type] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            @endif
        </div>

        <div class="ml-3 flex-1">
            <p class="text-sm font-medium text-gray-800">
                {{ $message ?? $slot }}
            </p>
        </div>

        <button @click="show = false" class="ml-4 flex-shrink-0">
            <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>