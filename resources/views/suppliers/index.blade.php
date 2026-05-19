@php
    $totalSuppliers = $suppliers->total();
    $activeSuppliers = \App\Models\Supplier::where('status', 1)->count();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-display text-3xl font-bold text-bakery-dark-deep">
                    Proveedores
                </h2>
                <p class="text-sm text-gray-600 mt-1">Gestión de proveedores de insumos</p>
            </div>
            <x-action-button 
                href="{{ route('suppliers.create') }}"
                variant="primary"
                size="lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Nuevo Proveedor
            </x-action-button>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <x-stat-card-modern 
                    title="Total Proveedores"
                    :value="$totalSuppliers"
                    color="blue"
                    :icon="'<svg class=&quot;w-6 h-6&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; viewBox=&quot;0 0 24 24&quot;><path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;2&quot; d=&quot;M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4&quot;/></svg>'"
                />
                
                <x-stat-card-modern 
                    title="Proveedores Activos"
                    :value="$activeSuppliers"
                    color="green"
                    :icon="'<svg class=&quot;w-6 h-6&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; viewBox=&quot;0 0 24 24&quot;><path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;2&quot; d=&quot;M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z&quot;/></svg>'"
                />
            </div>

            {{-- Suppliers Grid --}}
            @if($suppliers->count() > 0)
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
                    @foreach($suppliers as $supplier)
                        <x-modern-card variant="bordered" class="group hover:shadow-2xl transition-all duration-300 hover-lift">
                            {{-- Supplier Header --}}
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-bakery-dark text-lg group-hover:text-bakery-gold transition-colors">
                                            {{ $supplier->name }}
                                        </h3>
                                        @if($supplier->contact_name)
                                            <p class="text-xs text-gray-500">
                                                Contacto: {{ $supplier->contact_name }}
                                            </p>
                                        @endif
                                        @if($supplier->document_number)
                                            <p class="text-xs text-gray-500 mt-1">
                                                Doc: <span class="font-medium text-gray-700">{{ $supplier->document_number }}</span>
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <span class="badge badge-{{ $supplier->status ? 'success' : 'danger' }}">
                                    {{ $supplier->status ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>

                            {{-- Contact Information --}}
                            <div class="space-y-2 mb-4">
                                @if($supplier->phone)
                                    <div class="flex items-center gap-2 text-sm text-gray-700">
                                        <svg class="w-4 h-4 text-bakery-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        <a href="tel:{{ $supplier->phone }}" class="hover:text-bakery-gold transition-colors">
                                            {{ $supplier->phone }}
                                        </a>
                                    </div>
                                @endif

                                @if($supplier->email)
                                    <div class="flex items-center gap-2 text-sm text-gray-700">
                                        <svg class="w-4 h-4 text-bakery-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        <a href="mailto:{{ $supplier->email }}" class="hover:text-bakery-gold transition-colors truncate">
                                            {{ $supplier->email }}
                                        </a>
                                    </div>
                                @endif

                                @if($supplier->address)
                                    <div class="flex items-start gap-2 text-sm text-gray-700">
                                        <svg class="w-4 h-4 text-bakery-gold mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span class="line-clamp-2">{{ $supplier->address }}</span>
                                    </div>
                                @endif

                                @if(!$supplier->phone && !$supplier->email && !$supplier->address)
                                    <p class="text-sm text-gray-400 italic">Sin información de contacto</p>
                                @endif
                            </div>

                            {{-- Stats Section --}}
                            <div class="p-3 bg-gradient-to-r from-bakery-cream/50 to-bakery-vanilla/30 rounded-lg mb-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-700 font-medium">Insumos Suministrados</span>
                                    <span class="text-xl font-bold text-bakery-gold">
                                        {{ $supplier->supplies->count() ?? 0 }}
                                    </span>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex gap-2">
                                <a href="{{ route('suppliers.edit', $supplier) }}" 
                                    class="flex-1 btn-secondary text-center">
                                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Editar
                                </a>
                                
                                <form action="{{ route('suppliers.toggle', $supplier) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" 
                                        class="w-full btn-{{ $supplier->status ? 'warning' : 'success' }}">
                                        @if($supplier->status)
                                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                            </svg>
                                            Desactivar
                                        @else
                                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Activar
                                        @endif
                                    </button>
                                </form>
                            </div>
                        </x-modern-card>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $suppliers->links() }}
                </div>
            @else
                {{-- Empty State --}}
                <x-modern-card variant="glass">
                    <div class="text-center py-12">
                        <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">No hay proveedores registrados</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Comienza agregando tu primer proveedor de insumos
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('suppliers.create') }}" class="btn-primary">
                                <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Crear Primer Proveedor
                            </a>
                        </div>
                    </div>
                </x-modern-card>
            @endif
        </div>
    </div>
</x-app-layout>