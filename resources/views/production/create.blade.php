@php
    // Stats calculations
    $todayProductions = \App\Models\InventoryMovement::where('type', 'production_in')
        ->whereDate('created_at', today())
        ->count();

    $productsWithoutStock = \App\Models\ProductVariant::where('stock_track', true)
        ->where('current_stock', '<=', 0)
        ->count();

    $todayProductionValue = \App\Models\InventoryMovement::where('type', 'production_in')
        ->whereDate('created_at', today())
        ->with('productVariant')
        ->get()
        ->sum(function ($item) {
            return $item->quantity * ($item->productVariant->price ?? 0);
        });

    // Get Categories for filter
    $categories = \App\Models\Category::orderBy('name')->where('status', true)->get();

    // Get all products for component
    $productsQuery = \App\Models\Product::where('type', 'finished')
        ->where('status', 'active')
        ->with(['category', 'variants.recipe.ingredients.supply.stocks'])
        ->get();

    $availableProducts = [];
    foreach ($productsQuery as $product) {
        foreach ($product->variants as $variant) {
            // Calculate max production
            $maxProduction = 999999;
            if ($variant->recipe) {
                foreach ($variant->recipe->ingredients as $ingredient) {
                    $totalStock = $ingredient->supply->stocks->sum('quantity');
                    if ($ingredient->quantity > 0) {
                        $possible = floor($totalStock / $ingredient->quantity);
                        if ($possible < $maxProduction) {
                            $maxProduction = $possible;
                        }
                    }
                }
            } else {
                $maxProduction = 0;
            }

            // Only add if it has a recipe (optional, but logical for production)
            if ($variant->recipe) {
                $availableProducts[] = [
                    'id' => $variant->id,
                    'category_id' => $product->category_id,
                    'name' => $product->name,
                    'variant' => $variant->name,
                    'full_name' => $product->name . ' - ' . $variant->name,
                    'current_stock' => $variant->current_stock ?? 0,
                    'max_production' => $maxProduction == 999999 ? 0 : $maxProduction,
                    'price' => $variant->price,
                    'image' => $product->primaryImage ? $product->primaryImage->url : null
                ];
            }
        }
    }
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-display text-3xl font-bold text-bakery-dark-deep">
                Producción en Lote
            </h2>
        </div>
    </x-slot>

    <div class="py-8"
        x-data="productionBatch(@js($availableProducts), @js($categories), @js($globalSettings['currency_symbol'] ?? '$'))">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <x-stat-card-modern title="Producciones Hoy" :value="$todayProductions" color="blue" />

                <x-stat-card-modern title="Productos Sin Stock" :value="$productsWithoutStock" color="red" trend="down"
                    :trendValue="$productsWithoutStock > 0 ? 'Producir ahora' : 'Todo bien'" />

                <x-stat-card-modern title="Valor Producido Hoy" :value="($globalSettings['currency_symbol'] ?? '$') . ' ' . number_format($todayProductionValue, 2)" color="green" trend="up" />
            </div>

            {{-- Alert Messages --}}
            @if(session('success'))
                <div
                    class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm animate-fade-in">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm animate-fade-in">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- Left Column: Search & Add Products --}}
                <div class="lg:col-span-1 space-y-6">
                    <x-modern-card variant="glass" class="sticky top-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-bakery-dark">Productos</h3>
                            <button @click="resetFilters()"
                                class="text-xs text-blue-600 hover:text-blue-800 font-medium transition cursor-pointer"
                                x-show="selectedCategory || searchQuery">
                                Limpiar filtros
                            </button>
                        </div>

                        {{-- Category Filter --}}
                        <div class="mb-4">
                            <label
                                class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Categorías</label>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" @click="selectedCategory = null"
                                    class="px-3 py-1.5 rounded-full text-xs font-bold transition border"
                                    :class="selectedCategory === null 
                                        ? 'bg-bakery-gold text-white border-bakery-gold shadow-sm' 
                                        : 'bg-white text-gray-600 border-gray-200 hover:border-bakery-gold hover:text-bakery-gold'">
                                    Todos
                                </button>
                                <template x-for="category in categories" :key="category.id">
                                    <button type="button" @click="selectedCategory = category.id"
                                        class="px-3 py-1.5 rounded-full text-xs font-bold transition border"
                                        :class="selectedCategory === category.id
                                            ? 'bg-bakery-gold text-white border-bakery-gold shadow-sm' 
                                            : 'bg-white text-gray-600 border-gray-200 hover:border-bakery-gold hover:text-bakery-gold'" x-text="category.name">
                                    </button>
                                </template>
                            </div>
                        </div>

                        <div class="relative mb-6">
                            <input type="text" x-model="searchQuery" placeholder="Escribe para buscar..."
                                class="w-full pl-10 pr-4 py-3 rounded-lg border-gray-300 focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-30 transition shadow-sm">
                            <svg class="w-6 h-6 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>

                        <div class="space-y-3 max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                            <template x-for="product in filteredProducts" :key="product.id">
                                <div class="p-3 bg-white rounded-lg border border-gray-200 hover:border-bakery-gold hover:shadow-md transition cursor-pointer group"
                                    @click="addToQueue(product)">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden flex-shrink-0">
                                            <template x-if="product.image">
                                                <img :src="product.image" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!product.image">
                                                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </template>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-bold text-gray-800 text-sm truncate"
                                                x-text="product.full_name"></p>
                                            <div class="flex justify-between items-center mt-1">
                                                <span class="text-xs text-gray-500">Stock: <span
                                                        x-text="product.current_stock"></span></span>
                                                <span class="text-xs font-semibold"
                                                    :class="product.max_production > 0 ? 'text-green-600' : 'text-red-500'">
                                                    Máx: <span x-text="product.max_production"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <button
                                            class="w-8 h-8 rounded-full bg-bakery-cream text-bakery-gold hover:bg-bakery-gold hover:text-white flex items-center justify-center transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </template>

                            <template x-if="filteredProducts.length === 0">
                                <div class="text-center py-8 text-gray-400">
                                    <p>No se encontraron productos</p>
                                </div>
                            </template>
                        </div>
                    </x-modern-card>
                </div>

                {{-- Center/Right Column: Production Queue --}}
                <div class="lg:col-span-2">
                    <x-modern-card variant="elevated">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-bakery-dark flex items-center">
                                <svg class="w-6 h-6 mr-2 text-bakery-gold" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                Lista de Producción
                            </h3>
                            <span class="bg-bakery-cream text-bakery-dark font-bold px-3 py-1 rounded-full text-sm">
                                <span x-text="queue.length"></span> ítems
                            </span>
                        </div>

                        <template x-if="queue.length === 0">
                            <div class="text-center py-16 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                <p class="text-gray-500 text-lg font-medium">La lista está vacía</p>
                                <p class="text-gray-400 text-sm mt-2">Selecciona productos del panel izquierdo para
                                    comenzar</p>
                            </div>
                        </template>

                        <template x-if="queue.length > 0">
                            <div class="space-y-4">
                                <div class="overflow-hidden rounded-lg border border-gray-200">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Producto</th>
                                                <th
                                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Stock Actual</th>
                                                <th
                                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Cantidad a Producir</th>
                                                <th
                                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <template x-for="(item, index) in queue" :key="item.id">
                                                <tr class="hover:bg-gray-50 transition">
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <div class="text-sm font-bold text-gray-900"
                                                                x-text="item.full_name"></div>
                                                        </div>
                                                        <div class="text-xs text-red-500 mt-1"
                                                            x-show="item.quantity > item.max_production">
                                                            ⚠️ Supera el máximo posible (<span
                                                                x-text="item.max_production"></span>)
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500"
                                                        x-text="item.current_stock"></td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="flex justify-center items-center gap-2">
                                                            <button @click="if(item.quantity > 1) item.quantity--"
                                                                class="w-8 h-8 rounded bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold">−</button>
                                                            <input type="number" x-model.number="item.quantity"
                                                                class="w-20 text-center border-gray-300 rounded focus:ring-bakery-gold focus:border-bakery-gold font-bold">
                                                            <button @click="item.quantity++"
                                                                class="w-8 h-8 rounded bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold">+</button>
                                                        </div>
                                                    </td>
                                                    <td
                                                        class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                        <button @click="queue.splice(index, 1)"
                                                            class="text-red-500 hover:text-red-700 transition"
                                                            title="Eliminar">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                        <tfoot class="bg-gray-50">
                                            <tr>
                                                <td colspan="4" class="px-6 py-4 text-right">
                                                    <span class="text-sm text-gray-500">Valor Estimado
                                                        Producción:</span>
                                                    <span class="ml-2 text-lg font-bold text-bakery-dark"
                                                        x-text="formatMoney(paramQueueTotal)"></span>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <div class="mt-6 flex justify-end">
                                    <form action="{{ route('production.batch.store') }}" method="POST"
                                        @submit.prevent="submitForm" x-ref="productionForm">
                                        @csrf
                                        <input type="hidden" name="batch_items" :value="JSON.stringify(queue)">
                                        <button type="submit"
                                            class="bg-gradient-to-r from-bakery-gold to-yellow-500 text-bakery-dark font-bold py-3 px-8 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2"
                                            :disabled="queue.length === 0 || hasErrors">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            Confirmar Producción
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </template>

                    </x-modern-card>
                </div>
            </div>
        </div>

        {{-- Confirmation Modal --}}
        <x-modal name="confirm-production" focusable>
            <div class="p-6">
                <h2 class="text-lg font-bold text-bakery-dark mb-4">
                    Confirmar Producción
                </h2>

                <p class="text-gray-600 mb-6">
                    Estás a punto de registrar la producción de <span class="font-bold text-bakery-dark"
                        x-text="queue.length"></span> productos.
                    Verifica los detalles antes de continuar.
                </p>

                <div
                    class="bg-gray-50 rounded-lg p-4 mb-6 max-h-60 overflow-y-auto custom-scrollbar border border-gray-200">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 uppercase border-b border-gray-200">
                            <tr>
                                <th class="py-2">Producto</th>
                                <th class="py-2 text-center">Cant.</th>
                                <th class="py-2 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="item in queue" :key="item.id">
                                <tr>
                                    <td class="py-2 font-medium text-gray-800" x-text="item.full_name"></td>
                                    <td class="py-2 text-center text-gray-600" x-text="item.quantity"></td>
                                    <td class="py-2 text-right text-gray-600"
                                        x-text="formatMoney(item.quantity * item.price)"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-between items-center border-t border-gray-200 pt-4 mb-6">
                    <span class="text-gray-600 font-bold">Total Estimado</span>
                    <span class="text-2xl font-bold text-bakery-dark" x-text="formatMoney(paramQueueTotal)"></span>
                </div>

                <div class="flex justify-end gap-3">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        Cancelar
                    </x-secondary-button>

                    <button @click="$refs.productionForm.submit()"
                        class="bg-gradient-to-r from-bakery-gold to-yellow-500 text-bakery-dark font-bold py-2 px-6 rounded-lg shadow hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                        Confirmar y Guardar
                    </button>
                </div>
            </div>
        </x-modal>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('productionBatch', (products, categories, currencySymbol) => ({
                products: products,
                categories: categories,
                currencySymbol: currencySymbol,
                searchQuery: '',
                selectedCategory: null,
                queue: [],



                get filteredProducts() {
                    let result = this.products;

                    // Filter by Category
                    if (this.selectedCategory !== null) {
                        result = result.filter(p => p.category_id === this.selectedCategory);
                    }

                    // Filter by Search Query
                    if (this.searchQuery !== '') {
                        result = result.filter(p =>
                            p.full_name.toLowerCase().includes(this.searchQuery.toLowerCase())
                        );
                    }

                    return result;
                },

                resetFilters() {
                    this.selectedCategory = null;
                    this.searchQuery = '';
                },

                addToQueue(product) {
                    if (product.max_production <= 0) {
                        alert('No hay insumos suficientes para producir este ítem.');
                        return;
                    }

                    let existing = this.queue.find(item => item.id === product.id);
                    if (existing) {
                        existing.quantity++;
                    } else {
                        this.queue.push({
                            id: product.id,
                            full_name: product.full_name,
                            current_stock: product.current_stock,
                            max_production: product.max_production,
                            quantity: 1,
                            price: product.price
                        });
                    }
                },

                get paramQueueTotal() {
                    return this.queue.reduce((total, item) => total + (item.quantity * item.price), 0);
                },

                get hasErrors() {
                    return this.queue.some(item => item.quantity > item.max_production);
                },

                formatMoney(amount) {
                    return this.currencySymbol + ' ' + parseFloat(amount).toFixed(2);
                },

                submitForm(e) {
                    if (this.queue.length === 0) return;
                    if (this.hasErrors) {
                        alert('Por favor corrige las cantidades que exceden el máximo posible.');
                        return;
                    }
                    // Open Modal
                    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'confirm-production' }));
                }
            }));
        });
    </script>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #d4a017;
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #b8860b;
        }
    </style>
</x-app-layout>