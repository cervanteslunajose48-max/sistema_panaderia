@php
    // Calculate stats for products
    $totalProducts = $products->total();
    $activeProducts = \App\Models\Product::where('status', 'active')->count();
    $lowStockProducts = \App\Models\ProductVariant::where('current_stock', '<', 10)->count();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-display text-3xl font-bold text-bakery-dark-deep">
                    Productos
                </h2>
                <p class="text-sm text-gray-600 mt-1">Gestión del catálogo de productos</p>
            </div>
            <x-action-button 
                href="{{ route('products.create') }}"
                variant="primary"
                size="lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Nuevo Producto
            </x-action-button>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <x-stat-card-modern 
                    title="Total Productos"
                    :value="$totalProducts"
                    color="blue"
                    :icon="'<svg class=&quot;w-6 h-6&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; viewBox=&quot;0 0 24 24&quot;><path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;2&quot; d=&quot;M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4&quot;/></svg>'"
                />
                
                <x-stat-card-modern 
                    title="Productos Activos"
                    :value="$activeProducts"
                    color="green"
                    :icon="'<svg class=&quot;w-6 h-6&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; viewBox=&quot;0 0 24 24&quot;><path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;2&quot; d=&quot;M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z&quot;/></svg>'"
                />
                
                <x-stat-card-modern 
                    title="Stock Bajo"
                    :value="$lowStockProducts"
                    color="red"
                    :icon="'<svg class=&quot;w-6 h-6&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; viewBox=&quot;0 0 24 24&quot;><path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;2&quot; d=&quot;M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z&quot;/></svg>'"
                />
            </div>

            {{-- Filters and Search --}}
            <x-modern-card variant="glass" class="mb-6">
                <form method="GET" action="{{ route('products.index') }}" class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <x-search-input 
                            name="search"
                            placeholder="Buscar producto por nombre..."
                            :value="request('search')"
                        />
                    </div>
                    
                    <div class="w-full md:w-64">
                        <select name="category_id" class="input-modern w-full">
                            <option value="">Todas las Categorías</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="w-full md:w-48">
                        <select name="status" class="input-modern w-full">
                            <option value="">Todos los Estados</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activos</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivos</option>
                        </select>
                    </div>
                    
                    <div class="flex gap-2">
                        <button type="submit" class="btn-primary whitespace-nowrap">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Filtrar
                        </button>
                        @if(request()->hasAny(['search', 'category_id', 'status']))
                            <a href="{{ route('products.index') }}" class="btn-secondary whitespace-nowrap">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                </form>
            </x-modern-card>

            {{-- Products Grid --}}
            @if($products->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
                    @foreach($products as $product)
                        <x-modern-card variant="bordered" class="group hover:shadow-2xl transition-all duration-300">
                            {{-- Product Image --}}
                            <div class="h-48 bg-gradient-to-br from-bakery-cream to-bakery-vanilla rounded-xl mb-4 flex items-center justify-center overflow-hidden relative">
                                @if($product->primaryImage)
                                    <img src="{{ $product->primaryImage->url }}" 
                                         alt="{{ $product->name }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <svg class="w-24 h-24 text-bakery-gold/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                @endif
                                
                                {{-- Image count badge --}}
                                @if($product->images()->count() > 0)
                                    <span class="absolute top-2 right-2 bg-black/70 text-white text-xs px-2 py-1 rounded-full">
                                        <svg class="w-3 h-3 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $product->images()->count() }}
                                    </span>
                                @endif
                            </div>

                            {{-- Product Info --}}
                            <div class="mb-4">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-lg font-bold text-bakery-dark group-hover:text-bakery-gold transition-colors">
                                        {{ $product->name }}
                                    </h3>
                                    <span class="badge badge-{{ $product->status ? 'success' : 'danger' }}">
                                        {{ $product->status ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </div>
                                
                                @if($product->description)
                                    <p class="text-sm text-gray-600 mb-3">
                                        {{ Str::limit($product->description, 80) }}
                                    </p>
                                @endif

                                <div class="flex items-center gap-2 mb-3">
                                    <span class="badge badge-info">
                                        {{ $product->category->name }}
                                    </span>
                                    <span class="badge badge-secondary">
                                        {{ ucfirst($product->type) }}
                                    </span>
                                </div>
                            </div>

                            {{-- Variants Info --}}
                            <div class="mb-4 p-3 bg-bakery-cream/50 rounded-lg">
                                @if($product->variants->count() > 1)
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-700 font-medium">
                                            {{ $product->variants->count() }} Variantes
                                        </span>
                                        <span class="text-sm font-bold text-bakery-gold">
                                            {{ $globalSettings['currency_symbol'] ?? '$' }} {{ number_format($product->variants->min('price'), 2) }} - 
                                            {{ $globalSettings['currency_symbol'] ?? '$' }} {{ number_format($product->variants->max('price'), 2) }}
                                        </span>
                                    </div>
                                @elseif($product->variants->first())
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-700 font-medium">
                                            {{ $product->variants->first()->name }}
                                        </span>
                                        <span class="text-lg font-bold text-bakery-gold">
                                            {{ $globalSettings['currency_symbol'] ?? '$' }} {{ number_format($product->variants->first()->price, 2) }}
                                        </span>
                                    </div>
                                @endif

                                <div class="mt-2 flex justify-between items-center">
                                    <span class="text-xs text-gray-600">Stock Total:</span>
                                    <span class="font-bold {{ $product->variants->sum('current_stock') < 10 ? 'text-red-600' : 'text-green-600' }}">
                                        {{ $product->variants->sum('current_stock') }} unidades
                                    </span>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex gap-2">
                                <a href="{{ route('products.show', $product) }}" 
                                    class="flex-1 btn-primary text-center hover-lift">
                                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Ver
                                </a>
                                
                                <a href="{{ route('products.edit', $product) }}" 
                                    class="flex-1 btn-secondary text-center hover-lift">
                                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Editar
                                </a>
                                
                                <form action="{{ route('products.toggle', $product) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" 
                                        class="w-full btn-{{ $product->status ? 'warning' : 'success' }} hover-lift">
                                        @if($product->status)
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
                    {{ $products->links() }}
                </div>
            @else
                {{-- Empty State --}}
                <x-modern-card variant="glass">
                    <div class="text-center py-12">
                        <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">No se encontraron productos</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            @if(request()->hasAny(['search', 'category_id', 'status']))
                                Intenta ajustar los filtros de búsqueda
                            @else
                                Comienza creando tu primer producto
                            @endif
                        </p>
                        <div class="mt-6">
                            @if(request()->hasAny(['search', 'category_id', 'status']))
                                <a href="{{ route('products.index') }}" class="btn-secondary">
                                    Limpiar Filtros
                                </a>
                            @else
                                <a href="{{ route('products.create') }}" class="btn-primary">
                                    <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    Crear Primer Producto
                                </a>
                            @endif
                        </div>
                    </div>
                </x-modern-card>
            @endif
        </div>
    </div>
</x-app-layout>