<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-display font-bold text-2xl text-bakery-dark leading-tight">
                {{ $recipe->name }}
            </h2>
            <a href="{{ route('recipes.index') }}" class="btn-secondary px-4 py-2 text-sm hover-lift">
                ← Volver a Recetas
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Image and Basic Info Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Recipe Image --}}
                <div class="lg:col-span-1">
                    <x-modern-card variant="glass" class="h-full flex items-center justify-center p-2">
                        @if($recipe->productVariant->product->primaryImage)
                            <img src="{{ $recipe->productVariant->product->primaryImage->url }}" 
                                 alt="{{ $recipe->productVariant->product->name }}" 
                                 class="w-full h-auto rounded-lg shadow-md object-cover max-h-96">
                        @else
                            <div class="text-center py-12 text-gray-400">
                                <svg class="w-24 h-24 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p>Sin imagen asignada al producto</p>
                                <p class="text-xs mt-1">Gestionar en Módulo de Productos</p>
                            </div>
                        @endif
                    </x-modern-card>
                </div>

                {{-- Basic Info --}}
                <div class="lg:col-span-2 space-y-6">
                    <x-modern-card variant="elevated">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-2">Producto Terminado</h3>
                                <p class="text-lg font-bold text-gray-900">
                                    {{ $recipe->productVariant->product->name }}
                                </p>
                                <p class="text-sm text-gray-600 mt-1">{{ $recipe->productVariant->name }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-2">Rendimiento</h3>
                                <p class="text-lg font-bold text-gray-900">
                                    {{ $recipe->yield_quantity }} unidades
                                </p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-2">Estado de Stock</h3>
                                <p class="mt-1">
                                    @if($recipe->hasEnoughStock())
                                        <span class="badge badge-success text-sm py-2 px-4">
                                            <svg class="w-5 h-5 inline mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Stock Disponible
                                        </span>
                                    @else
                                        <span class="badge badge-danger text-sm py-2 px-4">
                                            <svg class="w-5 h-5 inline mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                            </svg>
                                            Stock Insuficiente
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </x-modern-card>

                    {{-- Instructions Section --}}
                    @if($recipe->instructions)
                    <x-modern-card variant="elevated">
                        <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-bakery-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                            Instrucciones de Preparación
                        </h3>
                        <div class="prose max-w-none text-gray-700 whitespace-pre-line">
                            {{ $recipe->instructions }}
                        </div>
                    </x-modern-card>
                    @endif
                </div>
            </div>

            {{-- Cost Analysis --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                <x-stat-card-modern 
                    title="Costo Total de Producción"
                    :value="'$' . number_format($recipe->total_cost, 2)"
                    color="blue"
                    :icon="'<svg class=\'w-6 h-6 text-white\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z\'></path></svg>'"
                >
                    <x-slot:footer>
                        <p class="text-xs text-gray-600">Para {{ $recipe->yield_quantity }} unidades</p>
                    </x-slot:footer>
                </x-stat-card-modern>
                
                <x-stat-card-modern 
                    title="Costo por Unidad"
                    :value="'$' . number_format($recipe->unit_cost, 2)"
                    color="green"
                    :icon="'<svg class=\'w-6 h-6 text-white\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'"
                >
                    <x-slot:footer>
                        <p class="text-xs text-gray-600">Por cada producto terminado</p>
                    </x-slot:footer>
                </x-stat-card-modern>
            </div>

            {{-- Ingredients Detail --}}
            <x-modern-card variant="elevated">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Ingredientes</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ $recipe->ingredients->count() }} insumos necesarios</p>
                    </div>
                    <x-action-button variant="secondary" size="sm" onclick="window.location.href='{{ route('recipes.edit', $recipe) }}'">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span>Editar Receta</span>
                    </x-action-button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="table-modern w-full">
                        <thead>
                            <tr>
                                <th>Insumo</th>
                                <th>Cantidad Requerida</th>
                                <th>Costo Unitario</th>
                                <th>Costo Total</th>
                                <th>Stock Disponible</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recipe->ingredients as $ingredient)
                                @php
                                    $availableStock = $ingredient->supply->stocks->sum('quantity');
                                    $hasEnough = $availableStock >= $ingredient->quantity;
                                    $ingredientCost = $ingredient->quantity * ($ingredient->supply->cost ?? 0);
                                @endphp
                                <tr class="{{ !$hasEnough ? 'bg-red-50' : '' }}">
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-bakery-peach bg-opacity-20 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-bakery-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                </svg>
                                            </div>
                                            <div class="text-sm font-semibold text-gray-900">{{ $ingredient->supply->name }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ number_format($ingredient->quantity, 2) }} {{ $ingredient->supply->base_unit }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-sm text-gray-900">
                                            @currency($ingredient->supply->cost ?? 0)
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-sm font-semibold text-gray-900">
                                            @currency($ingredientCost)
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-sm {{ $hasEnough ? 'text-green-600' : 'text-red-600' }} font-medium">
                                            {{ number_format($availableStock, 2) }} {{ $ingredient->supply->base_unit }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($hasEnough)
                                            <span class="badge badge-success">✓ OK</span>
                                        @else
                                            <span class="badge badge-danger">✗ Faltante</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                            <tr>
                                <td colspan="3" class="px-6 py-3 text-right">
                                    <span class="text-sm font-bold text-gray-900">TOTAL:</span>
                                </td>
                                <td class="px-6 py-3">
                                    <span class="text-sm font-bold text-bakery-gold-dark">
                                        @currency($recipe->total_cost)
                                    </span>
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </x-modern-card>

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row justify-between gap-4">
                <x-action-button variant="secondary" size="md" onclick="window.location.href='{{ route('recipes.index') }}'">
                    ← Volver al Listado
                </x-action-button>
                
                <div class="flex flex-col sm:flex-row gap-3">
                    <form action="{{ route('recipes.duplicate', $recipe) }}" method="POST" class="inline"
                        onsubmit="return confirm('¿Duplicar esta receta?');">
                        @csrf
                        <x-action-button variant="success" size="md" type="submit">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            <span>Duplicar Receta</span>
                        </x-action-button>
                    </form>
                    
                    <x-action-button variant="primary" size="md" onclick="window.location.href='{{ route('recipes.edit', $recipe) }}'">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span>Editar Receta</span>
                    </x-action-button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
