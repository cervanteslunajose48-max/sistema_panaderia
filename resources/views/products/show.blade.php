<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-display text-3xl font-bold text-bakery-dark-deep">
                    {{ $product->name }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">Detalles del producto</p>
            </div>
            <x-action-button href="{{ route('products.edit', $product) }}" variant="secondary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Editar Producto
            </x-action-button>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Image Gallery --}}
                <div class="lg:col-span-2">
                    <x-modern-card variant="glass">
                        <h3 class="text-lg font-bold text-bakery-dark mb-4">Galería de Imágenes</h3>

                        {{-- Main Image --}}
                        @if($product->primaryImage)
                            <div class="mb-6 rounded-xl overflow-hidden">
                                <img src="{{ $product->primaryImage->url }}" alt="{{ $product->name }}"
                                    class="w-full h-96 object-cover">
                            </div>
                        @else
                            <div
                                class="mb-6 h-96 bg-gradient-to-br from-bakery-cream to-bakery-vanilla rounded-xl flex items-center justify-center">
                                <div class="text-center">
                                    <svg class="w-24 h-24 text-bakery-gold/30 mx-auto mb-4" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-gray-500">Sin imagen principal</p>
                                </div>
                            </div>
                        @endif

                        {{-- Thumbnails Grid --}}
                        @if($product->images->count() > 0)
                            <div class="grid grid-cols-4 gap-4 mb-6">
                                @foreach($product->images as $image)
                                    <div class="relative group">
                                        <img src="{{ $image->url }}" alt="Imagen {{ $loop->iteration }}"
                                            class="w-full h-24 object-cover rounded-lg border-2 {{ $image->is_primary ? 'border-bakery-gold' : 'border-gray-200' }}">

                                        {{-- Primary Badge --}}
                                        @if($image->is_primary)
                                            <span
                                                class="absolute top-1 left-1 bg-bakery-gold text-bakery-dark text-xs px-2 py-1 rounded font-bold">
                                                Principal
                                            </span>
                                        @endif

                                        {{-- Actions --}}
                                        <div
                                            class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center gap-2">
                                            @if(!$image->is_primary)
                                                <form action="{{ route('product-images.set-primary', $image) }}" method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                        class="bg-bakery-gold text-bakery-dark px-3 py-1 rounded text-xs font-bold hover:bg-yellow-400">
                                                        Hacer Principal
                                                    </button>
                                                </form>
                                            @endif

                                            <form action="{{ route('product-images.delete', $image) }}" method="POST"
                                                onsubmit="return confirm('¿Eliminar esta imagen?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="bg-red-500 text-white px-3 py-1 rounded text-xs font-bold hover:bg-red-600">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Upload Form --}}
                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-sm font-bold text-gray-700 mb-3">Subir Nueva Imagen</h4>
                            <form action="{{ route('products.upload-image', $product) }}" method="POST"
                                enctype="multipart/form-data" class="flex gap-3">
                                @csrf
                                <input type="file" name="image" accept="image/*" required
                                    class="flex-1 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-bakery-gold file:text-bakery-dark hover:file:bg-yellow-400">
                                <button type="submit" class="btn-primary">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    Subir
                                </button>
                            </form>
                            <p class="text-xs text-gray-500 mt-2">Formatos: JPEG, PNG, JPG, WEBP. Tamaño máximo: 2MB</p>
                        </div>
                    </x-modern-card>
                </div>

                {{-- Product Info --}}
                <div class="space-y-6">
                    {{-- Basic Info --}}
                    <x-modern-card variant="bordered">
                        <h3 class="text-lg font-bold text-bakery-dark mb-4">Información General</h3>

                        <div class="space-y-3">
                            <div>
                                <span class="text-xs text-gray-500 uppercase">Estado</span>
                                <div>
                                    <span class="badge badge-{{ $product->status ? 'success' : 'danger' }}">
                                        {{ $product->status ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </div>
                            </div>

                            <div>
                                <span class="text-xs text-gray-500 uppercase">Categoría</span>
                                <p class="text-sm font-medium">{{ $product->category->name }}</p>
                            </div>

                            <div>
                                <span class="text-xs text-gray-500 uppercase">Tipo</span>
                                <p class="text-sm font-medium">{{ ucfirst($product->type) }}</p>
                            </div>

                            @if($product->description)
                                <div>
                                    <span class="text-xs text-gray-500 uppercase">Descripción</span>
                                    <p class="text-sm text-gray-700">{{ $product->description }}</p>
                                </div>
                            @endif
                        </div>
                    </x-modern-card>

                    {{-- Variants --}}
                    <x-modern-card variant="bordered">
                        <h3 class="text-lg font-bold text-bakery-dark mb-4">Variantes</h3>

                        <div class="space-y-3">
                            @foreach($product->variants as $variant)
                                <div class="p-3 bg-bakery-cream/30 rounded-lg">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <p class="font-bold text-bakery-dark">{{ $variant->name }}</p>
                                            <p class="text-xs text-gray-500">SKU: {{ $variant->sku }}</p>
                                        </div>
                                        <span class="text-lg font-bold text-bakery-gold">
                                            {{ $globalSettings['currency_symbol'] ?? '$' }}
                                            {{ number_format($variant->price, 2) }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-gray-600">Stock:</span>
                                        <span
                                            class="font-bold {{ $variant->current_stock < 10 ? 'text-red-600' : 'text-green-600' }}">
                                            {{ $variant->current_stock }} unidades
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </x-modern-card>

                    {{-- Quick Actions --}}
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('products.edit', $product) }}" class="btn-secondary text-center">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Editar Información
                        </a>
                        <form action="{{ route('products.toggle', $product) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full btn-{{ $product->status ? 'warning' : 'success' }}">
                                {{ $product->status ? 'Desactivar' : 'Activar' }} Producto
                            </button>
                        </form>
                        <a href="{{ route('products.index') }}" class="btn-secondary text-center">
                            Volver al Listado
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>