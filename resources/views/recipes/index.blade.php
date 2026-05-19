<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-display font-bold text-2xl text-bakery-dark leading-tight">
                {{ __('Recetas') }}
            </h2>
            
            <x-action-button variant="primary" size="md" onclick="window.location.href='{{ route('recipes.create') }}'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span>Nueva Receta</span>
            </x-action-button>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                <x-stat-card-modern 
                    title="Total Recetas"
                    :value="$recipes->total()"
                    color="blue"
                    :icon="'<svg class=\'w-6 h-6 text-white\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z\'></path></svg>'"
                />
                
                <x-stat-card-modern 
                    title="Con Stock Suficiente"
                    :value="$recipes->filter(fn($r) => $r->hasEnoughStock())->count()"
                    color="green"
                    trend="up"
                    :trendValue="$recipes->count() > 0 ? round(($recipes->filter(fn($r) => $r->hasEnoughStock())->count() / $recipes->count()) * 100) . '%' : '0%'"
                    :icon="'<svg class=\'w-6 h-6 text-white\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'"
                />
                
                <x-stat-card-modern 
                    title="Productos Diferentes"
                    :value="$products->count()"
                    color="orange"
                    :icon="'<svg class=\'w-6 h-6 text-white\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10\'></path></svg>'"
                />
            </div>

            {{-- Search and Filters --}}
            <x-modern-card variant="glass">
                <form method="GET" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- Search --}}
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Buscar por nombre o producto..."
                                class="w-full pl-4 pr-4 py-2.5 rounded-xl border-gray-300 focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-30 transition-all duration-200">
                        </div>
                        
                        {{-- Filter by Category --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
                            <select name="category_id" 
                                class="w-full rounded-xl border-gray-300 focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-30">
                                <option value="">Todas las categorías</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- Filter by Product --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Producto</label>
                            <select name="product_id" 
                                class="w-full rounded-xl border-gray-300 focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-30">
                                <option value="">Todos los productos</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex justify-end gap-2">
                        <x-action-button variant="secondary" size="sm" type="submit">
                            Aplicar Filtros
                        </x-action-button>
                        <a href="{{ route('recipes.index') }}" class="btn-secondary px-4 py-2 text-sm">
                            Limpiar
                        </a>
                    </div>
                </form>
            </x-modern-card>

            {{-- Recipes Table --}}
            <x-modern-card variant="elevated">
                <div class="overflow-x-auto">
                    <table class="table-modern w-full">
                        <thead>
                            <tr>
                                <th>Receta</th>
                                <th>Producto Terminado</th>
                                <th>Ingredientes</th>
                                <th>Rendimiento</th>
                                <th>Costo Total</th>
                                <th>Costo/Unidad</th>
                                <th>Stock</th>
                                <th class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recipes as $recipe)
                                <tr class="group">
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-bakery-gold bg-opacity-20 flex items-center justify-center overflow-hidden">
                                                @if($recipe->productVariant?->product?->primaryImage)
                                                    <img src="{{ $recipe->productVariant->product->primaryImage->url }}" 
                                                         alt="{{ $recipe->productVariant->product->name }}"
                                                         class="w-full h-full object-cover">
                                                @else
                                                    <svg class="w-5 h-5 text-bakery-gold-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="text-sm font-semibold text-gray-900">{{ $recipe->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-sm font-medium text-gray-900">{{ $recipe->productVariant?->product?->name ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500">{{ $recipe->productVariant?->name ?? 'Sin variante' }}</div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $recipe->ingredients->count() }} insumos</span>
                                    </td>
                                    <td>
                                        <div class="text-sm text-gray-600">{{ $recipe->yield_quantity }} unidades</div>
                                    </td>
                                    <td>
                                        <div class="text-sm font-semibold text-gray-900">@currency($recipe->total_cost)</div>
                                    </td>
                                    <td>
                                        <div class="text-sm text-gray-900">@currency($recipe->unit_cost)</div>
                                    </td>
                                    <td>
                                        @if($recipe->hasEnoughStock())
                                            <span class="badge badge-success">
                                                <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                                Disponible
                                            </span>
                                        @else
                                            <span class="badge badge-danger">
                                                <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                                </svg>
                                                Faltante
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="flex justify-end items-center gap-2">
                                            <a href="{{ route('recipes.show', $recipe) }}"
                                                class="p-2 rounded-lg text-blue-600 hover:bg-blue-50 transition-colors"
                                                title="Ver detalles">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>

                                            <a href="{{ route('recipes.edit', $recipe) }}"
                                                class="p-2 rounded-lg text-indigo-600 hover:bg-indigo-50 transition-colors"
                                                title="Editar">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>

                                            <form action="{{ route('recipes.duplicate', $recipe) }}" method="POST" class="inline-block"
                                                onsubmit="return confirm('¿Duplicar esta receta?');">
                                                @csrf
                                                <button type="submit"
                                                    class="p-2 rounded-lg text-green-600 hover:bg-green-50 transition-colors"
                                                    title="Duplicar">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-500">
                                            <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <p class="text-lg font-medium">No hay recetas registradas</p>
                                            <p class="text-sm mt-1">Comienza creando tu primera receta</p>
                                            <x-action-button variant="primary" size="sm" onclick="window.location.href='{{ route('recipes.create') }}'" class="mt-4">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                                <span>Crear Receta</span>
                                            </x-action-button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-6">
                    {{ $recipes->links() }}
                </div>
            </x-modern-card>
        </div>
    </div>
</x-app-layout>