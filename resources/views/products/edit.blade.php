<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Producto') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Nombre del
                                    Producto</label>
                                <input type="text" name="name" id="name" value="{{ $product->name }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50"
                                    required>
                            </div>
                            <div>
                                <label for="category_id"
                                    class="block text-sm font-medium text-gray-700">Categoría</label>
                                <select name="category_id" id="category_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50"
                                    required>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label for="description"
                                    class="block text-sm font-medium text-gray-700">Descripción</label>
                                <textarea name="description" id="description" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50">{{ $product->description }}</textarea>
                            </div>
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">Tipo</label>
                                <select name="type" id="type"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50">
                                    <option value="finished" {{ $product->type == 'finished' ? 'selected' : '' }}>Producto
                                        Terminado (Venta)</option>
                                    <option value="raw_material" {{ $product->type == 'raw_material' ? 'selected' : '' }}>
                                        Insumo / Materia Prima</option>
                                    <option value="service" {{ $product->type == 'service' ? 'selected' : '' }}>Servicio
                                    </option>
                                </select>
                            </div>
                        </div>

                        {{-- Image Upload Section --}}
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Imagen del Producto</h3>
                            <div class="flex items-start gap-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Imagen Actual /
                                        Nueva Imagen</label>

                                    @if($product->primaryImage)
                                        <div class="mb-3">
                                            <p class="text-xs text-gray-500 mb-1">Imagen Actual:</p>
                                            <img src="{{ $product->primaryImage->url }}" alt="Imagen actual"
                                                class="w-32 h-32 object-cover rounded-lg border border-gray-200 shadow-sm mb-2">
                                        </div>
                                    @endif

                                    <input type="file" name="image" accept="image/*" class="block w-full text-sm text-gray-500
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-md file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-bakery-gold file:text-white
                                        hover:file:bg-bakery-dark transition" onchange="previewProductImage(event)">
                                    <p class="text-xs text-gray-500 mt-1">Sube una nueva imagen para reemplazar la
                                        actual. Tamaño máximo: 2MB.</p>
                                </div>
                                <div id="imagePreviewContainer" class="hidden">
                                    <p class="text-xs text-gray-500 mb-1">Nueva Vista Previa:</p>
                                    <img id="productStartPreview"
                                        class="w-32 h-32 object-cover rounded-lg border border-gray-200 shadow-sm" />
                                </div>
                            </div>
                        </div>

                        <hr class="my-6 border-gray-200">

                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        La edición de variantes y stock se gestionará en un módulo separado.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <a href="{{ route('products.index') }}"
                                class="bg-gray-200 text-gray-700 py-2 px-4 rounded mr-2 hover:bg-gray-300">Cancelar</a>
                            <button type="submit"
                                class="bg-bakery-gold hover:bg-bakery-dark text-white font-bold py-2 px-4 rounded">Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
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