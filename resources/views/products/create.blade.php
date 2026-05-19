<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nuevo Producto') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('products.store') }}" method="POST" id="productForm"
                        enctype="multipart/form-data">
                        @csrf

                        <!-- Basic Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Nombre del
                                    Producto</label>
                                <input type="text" name="name" id="name"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50"
                                    required>
                            </div>
                            <div>
                                <label for="category_id"
                                    class="block text-sm font-medium text-gray-700">Categoría</label>
                                <select name="category_id" id="category_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50"
                                    required>
                                    <option value="">Seleccionar Categoría</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label for="description"
                                    class="block text-sm font-medium text-gray-700">Descripción</label>
                                <textarea name="description" id="description" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50"></textarea>
                            </div>
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">Tipo</label>
                                <select name="type" id="type"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50">
                                    <option value="finished">Producto Terminado (Venta)</option>
                                    <option value="raw_material">Insumo / Materia Prima</option>
                                    <option value="service">Servicio</option>
                                </select>
                            </div>
                        </div>

                        {{-- Image Upload Section --}}
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Imagen del Producto</h3>
                            <div class="flex items-start gap-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Seleccionar
                                        Imagen</label>
                                    <input type="file" name="image" accept="image/*" class="block w-full text-sm text-gray-500
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-md file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-bakery-gold file:text-white
                                        hover:file:bg-bakery-dark transition" onchange="previewProductImage(event)">
                                    <p class="text-xs text-gray-500 mt-1">Tamaño máximo: 2MB. Formatos: JPG, PNG, WEBP
                                    </p>
                                </div>
                                <div id="imagePreviewContainer" class="hidden">
                                    <p class="text-xs text-gray-500 mb-1">Vista Previa:</p>
                                    <img id="productStartPreview"
                                        class="w-32 h-32 object-cover rounded-lg border border-gray-200 shadow-sm" />
                                </div>
                            </div>
                        </div>

                        <hr class="my-6 border-gray-200">

                        <!-- Variants Section -->
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Variantes y Precios</h3>
                                <button type="button" onclick="addVariant()"
                                    class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-2 px-4 rounded border border-gray-300 shadow-sm text-sm">
                                    + Agregar Variante
                                </button>
                            </div>

                            <div id="variantsContainer" class="space-y-4">
                                <!-- Initial Variant Row -->
                                <div
                                    class="variant-row grid grid-cols-12 gap-4 items-end bg-gray-50 p-4 rounded-md relative">
                                    <div class="col-span-5">
                                        <label class="block text-xs font-medium text-gray-500 uppercase">Nombre /
                                            Medida</label>
                                        <input type="text" name="variants[0][name]" placeholder="Ej: Unidad, 1kg, Caja"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50 text-sm"
                                            required>
                                    </div>
                                    <div class="col-span-3">
                                        <label class="block text-xs font-medium text-gray-500 uppercase">Precio
                                            ({{ $globalSettings['currency_symbol'] ?? '$' }})</label>
                                        <input type="number" step="0.01" name="variants[0][price]"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50 text-sm"
                                            required>
                                    </div>
                                    <div class="col-span-3">
                                        <label class="block text-xs font-medium text-gray-500 uppercase">Stock
                                            Inicial</label>
                                        <input type="number" name="variants[0][current_stock]" value="0"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50 text-sm"
                                            required>
                                    </div>
                                    <div class="col-span-1 text-center">
                                        <!-- Remove button hidden for first item usually, or managed by JS -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-6">
                            <a href="{{ route('products.index') }}"
                                class="bg-gray-200 text-gray-700 py-2 px-4 rounded mr-2 hover:bg-gray-300">Cancelar</a>
                            <button type="submit"
                                class="bg-bakery-gold hover:bg-bakery-dark text-white font-bold py-2 px-4 rounded shadow">Guardar
                                Producto</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let variantIndex = 1;
        const currencySymbol = "{{ $globalSettings['currency_symbol'] ?? '$' }}";

        function addVariant() {
            const container = document.getElementById('variantsContainer');
            const row = document.createElement('div');
            row.className = 'variant-row grid grid-cols-12 gap-4 items-end bg-gray-50 p-4 rounded-md relative';
            row.innerHTML = `
                <div class="col-span-5">
                    <label class="block text-xs font-medium text-gray-500 uppercase">Nombre / Medida</label>
                    <input type="text" name="variants[${variantIndex}][name]" placeholder="Ej: Unidad, 1kg, Caja" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50 text-sm" required>
                </div>
                <div class="col-span-3">
                    <label class="block text-xs font-medium text-gray-500 uppercase">Precio (${currencySymbol})</label>
                    <input type="number" step="0.01" name="variants[${variantIndex}][price]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50 text-sm" required>
                </div>
                <div class="col-span-3">
                    <label class="block text-xs font-medium text-gray-500 uppercase">Stock Inicial</label>
                    <input type="number" name="variants[${variantIndex}][current_stock]" value="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50 text-sm" required>
                </div>
                <div class="col-span-1 text-center pb-2">
                   <button type="button" onclick="this.closest('.variant-row').remove()" class="text-red-600 hover:text-red-900 font-bold text-xl">&times;</button>
                </div>
            `;
            container.appendChild(row);
            variantIndex++;
        }

        function previewProductImage(event) {
            const input = event.target;
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const preview = document.getElementById('productStartPreview');
                    const container = document.getElementById('imagePreviewContainer');
                    preview.src = e.target.result;
                    container.classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-app-layout>