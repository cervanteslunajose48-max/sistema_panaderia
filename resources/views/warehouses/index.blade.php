<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-display text-3xl font-bold text-bakery-dark-deep">
                    Almacenes
                </h2>
                <p class="text-sm text-gray-600 mt-1">Gestión de ubicaciones de almacenamiento</p>
            </div>
            <x-action-button href="{{ route('warehouses.create') }}" variant="primary" size="lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Nuevo Almacén
            </x-action-button>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Search Bar --}}
            <x-modern-card variant="glass" class="mb-6">
                <form method="GET" action="{{ route('warehouses.index') }}">
                    <x-search-input name="search" placeholder="Buscar por nombre o ubicación..."
                        :value="request('search')" />
                </form>
            </x-modern-card>

            {{-- Warehouses Grid --}}
            @if($warehouses->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                    @foreach($warehouses as $warehouse)
                        <x-modern-card variant="glass"
                            class="flex flex-col hover:shadow-2xl transition-all duration-300 hover-lift">
                            {{-- Warehouse Icon/Visual --}}
                            <div
                                class="h-32 bg-gradient-to-br from-purple-500/20 to-purple-700/10 rounded-xl mb-4 flex items-center justify-center relative overflow-hidden">
                                {{-- Background Pattern --}}
                                <div class="absolute inset-0 opacity-10">
                                    <svg class="w-full h-full" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                        <pattern id="grid-{{ $warehouse->id }}" x="0" y="0" width="20" height="20"
                                            patternUnits="userSpaceOnUse">
                                            <rect x="0" y="0" width="10" height="10" fill="currentColor"
                                                class="text-purple-600" />
                                        </pattern>
                                        <rect width="100" height="100" fill="url(#grid-{{ $warehouse->id }})" />
                                    </svg>
                                </div>

                                {{-- Warehouse Icon --}}
                                <svg class="w-16 h-16 text-purple-600 relative z-10" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>

                            {{-- Warehouse Info --}}
                            <div class="flex-grow flex flex-col justify-between">
                                <div class="mb-3">
                                    <h3
                                        class="text-lg font-bold text-bakery-dark hover:text-purple-600 transition-colors truncate">
                                        {{ $warehouse->name }}
                                    </h3>
                                    @if($warehouse->location)
                                        <p class="text-xs text-gray-500 mt-1 flex items-center gap-1 truncate">
                                            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <span class="truncate">{{ $warehouse->location }}</span>
                                        </p>
                                    @endif
                                </div>

                                {{-- Stats --}}
                                <div class="mb-4">
                                    <div class="p-3 bg-purple-50 rounded-lg flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                            <span class="text-sm text-gray-700 font-medium">Insumos</span>
                                        </div>
                                        <span class="text-2xl font-bold text-purple-600">
                                            {{ $warehouse->stock_count ?? 0 }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="space-y-2">
                                    <a href="{{ route('warehouses.show', $warehouse) }}"
                                        class="w-full btn-primary text-center text-sm flex items-center justify-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                        Ver Insumos
                                    </a>

                                    <div class="flex gap-2">
                                        <a href="{{ route('warehouses.edit', $warehouse) }}"
                                            class="flex-1 btn-secondary text-center text-sm">
                                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Editar
                                        </a>

                                        <form action="{{ route('warehouses.toggle', $warehouse) }}" method="POST"
                                            class="flex-1">
                                            @csrf
                                            <button type="submit"
                                                class="w-full text-sm {{ $warehouse->status ? 'btn-danger' : 'btn-success' }}">
                                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    @if($warehouse->status)
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                    @else
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    @endif
                                                </svg>
                                                {{ $warehouse->status ? 'Desactivar' : 'Activar' }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </x-modern-card>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $warehouses->links() }}
                </div>
            @else
                {{-- Empty State --}}
                <x-modern-card variant="glass">
                    <div class="text-center py-12">
                        <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">No hay almacenes</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Comienza creando el primer almacén para gestionar el stock de insumos
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('warehouses.create') }}" class="btn-primary">
                                <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Crear Primer Almacén
                            </a>
                        </div>
                    </div>
                </x-modern-card>
            @endif
        </div>
    </div>
</x-app-layout>