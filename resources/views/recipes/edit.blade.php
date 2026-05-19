<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Receta') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('recipes.update', $recipe) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Recipe Header -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Producto Terminado</label>
                                <select name="product_variant_id" disabled
                                    class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm">
                                    <option value="{{ $recipe->productVariant->id }}" selected>
                                        {{ $recipe->productVariant->product->name }} -
                                        {{ $recipe->productVariant->name }}
                                    </option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">El producto terminado no se puede cambiar.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nombre de Receta</label>
                                <input type="text" name="name" value="{{ old('name', $recipe->name) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50"
                                    placeholder="Ej: Receta Estándar" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Cantidad Rendimiento
                                    (Unidades)</label>
                                <input type="number" name="yield_quantity"
                                    value="{{ old('yield_quantity', $recipe->yield_quantity) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50"
                                    required>
                                <p class="text-xs text-gray-500 mt-1">Cuántas unidades de producto genera esta receta.
                                </p>
                            </div>
                        </div>

                        {{-- Image Upload --}}
                        {{-- Product Image Display --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Imagen del Producto</label>
                            @if($recipe->productVariant->product->primaryImage)
                                <div class="mb-3">
                                    <img src="{{ $recipe->productVariant->product->primaryImage->url }}"
                                        alt="Imagen del producto" class="max-w-xs rounded-lg shadow-md">
                                    <p class="text-xs text-gray-500 mt-1">Imagen referencial del producto (Administrar en
                                        Módulo de Productos)</p>
                                </div>
                            @else
                                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 text-center">
                                    <p class="text-sm text-gray-500">Este producto no tiene imagen asignada.</p>
                                    <p class="text-xs text-gray-400 mt-1">Puedes agregar una imagen en la edición del
                                        producto.</p>
                                </div>
                            @endif
                        </div>

                        {{-- Instructions --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Instrucciones de
                                Preparación</label>
                            <textarea name="instructions" rows="6"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50"
                                placeholder="Describe paso a paso cómo preparar este producto...">{{ old('instructions', $recipe->instructions) }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">Opcional: Agrega las instrucciones detalladas de
                                preparación</p>
                        </div>

                        <hr class="mb-6">

                        <!-- Ingredients Dynamic List -->
                        <!-- Ingredients Dynamic List -->
                        <div x-data="recipeIngredients({{ $recipe->ingredients->map(function ($i) {
    return [
        'id' => $i->supply_id,
        'name' => $i->supply->name,
        'base_unit' => $i->supply->base_unit,
        'quantity' => $i->quantity
    ];
}) }})" class="mb-6">
                            <h3 class="font-bold text-lg mb-4">Ingredientes</h3>

                            <!-- Search Box -->
                            <div class="mb-4 relative">
                                <label class="block text-sm font-medium text-gray-700">Buscar Insumo</label>
                                <div class="flex gap-2">
                                    <input type="text" x-model="searchQuery" @input.debounce.300ms="searchSupplies()"
                                        @keydown.escape="showResults = false"
                                        placeholder="Escribe para buscar insumos..."
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50">
                                </div>

                                <!-- Search Results Dropdown -->
                                <div x-show="showResults && searchResults.length > 0" @click.away="showResults = false"
                                    class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg mt-1 max-h-60 overflow-y-auto">
                                    <template x-for="supply in searchResults" :key="supply.id">
                                        <div @click="addIngredient(supply)"
                                            class="p-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-0">
                                            <span x-text="supply.name" class="font-semibold"></span>
                                            <span class="text-xs text-gray-500"
                                                x-text="'(' + supply.base_unit + ')'"></span>
                                        </div>
                                    </template>
                                </div>
                                <div x-show="showResults && searchResults.length === 0 && searchQuery.length > 2"
                                    class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg mt-1 p-2 text-gray-500">
                                    No se encontraron insumos.
                                </div>
                            </div>

                            <!-- Selected Ingredients List -->
                            <div class="space-y-4 mb-6">
                                <template x-for="(ing, index) in ingredients" :key="index">
                                    <div class="flex gap-4 items-end p-3 bg-gray-50 rounded-lg border border-gray-200">
                                        <div class="flex-1">
                                            <label class="block text-sm font-medium text-gray-700">Insumo</label>
                                            <div class="text-gray-900 font-bold mt-1" x-text="ing.name"></div>
                                            <div class="text-xs text-gray-500" x-text="ing.base_unit"></div>
                                            <input type="hidden" :name="'ingredients['+index+'][supply_id]'"
                                                :value="ing.id">
                                        </div>
                                        <div class="w-32">
                                            <label class="block text-sm font-medium text-gray-700">Cantidad</label>
                                            <input type="number" step="0.0001"
                                                :name="'ingredients['+index+'][quantity]'" x-model="ing.quantity"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50"
                                                required>
                                        </div>
                                        <button type="button" @click="removeIngredient(index)"
                                            class="text-red-500 font-bold px-2 hover:text-red-700">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                </template>

                                <div x-show="ingredients.length === 0"
                                    class="text-center py-4 text-gray-400 border-2 border-dashed border-gray-300 rounded-lg">
                                    No hay ingredientes agregados. Busca y selecciona insumos arriba.
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3">
                            <a href="{{ route('recipes.index') }}"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 px-6 rounded">
                                Cancelar
                            </a>
                            <button type="submit"
                                class="bg-bakery-gold hover:bg-bakery-dark text-white font-bold py-3 px-6 rounded shadow transition">
                                Guardar Receta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function recipeIngredients(initialIngredients = []) {
            return {
                searchQuery: '',
                searchResults: [],
                showResults: false,
                ingredients: initialIngredients,

                searchSupplies() {
                    if (this.searchQuery.length < 2) {
                        this.searchResults = [];
                        this.showResults = false;
                        return;
                    }

                    fetch(`{{ route('supplies.search') }}?q=${this.searchQuery}`)
                        .then(response => response.json())
                        .then(data => {
                            this.searchResults = data;
                            this.showResults = true;
                        });
                },

                addIngredient(supply) {
                    // Check if already exists
                    if (this.ingredients.some(i => i.id === supply.id)) {
                        alert('Este insumo ya está en la lista.');
                        return;
                    }

                    this.ingredients.push({
                        id: supply.id,
                        name: supply.name,
                        base_unit: supply.base_unit,
                        quantity: 1
                    });

                    this.searchQuery = '';
                    this.showResults = false;
                },

                removeIngredient(index) {
                    this.ingredients.splice(index, 1);
                }
            }
        }
    </script>
</x-app-layout>