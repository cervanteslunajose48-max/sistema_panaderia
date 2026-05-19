<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Transformación de Productos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-modern-card variant="glass">

                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                        role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                <form action="{{ route('inventory.transformations.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 relative">

                        <!-- Arrow Icon in the middle -->
                        <div class="hidden md:flex absolute inset-0 items-center justify-center pointer-events-none">
                            <div
                                class="bg-white p-2 rounded-full shadow-lg border border-gray-200 z-10 text-bakery-gold">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </div>
                        </div>

                        <!-- Source Product -->
                        <div class="bg-red-50 p-6 rounded-xl border border-red-100">
                            <h3 class="text-lg font-bold text-red-800 mb-4 flex items-center">
                                <span class="bg-red-200 text-red-700 p-1 rounded mr-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 12H4" />
                                    </svg>
                                </span>
                                Origen (Sale del Inventario)
                            </h3>

                            <div class="mb-4">
                                <x-input-label for="source_variant_id" :value="__('Producto Origen')" />
                                <div class="mt-1">
                                    <select id="source_variant_id" name="source_variant_id" class="tom-select w-full"
                                        required>
                                        <option value="">Seleccione Producto...</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product['id'] }}" {{ old('source_variant_id') == $product['id'] ? 'selected' : '' }}>
                                                {{ $product['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <x-input-error :messages="$errors->get('source_variant_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="source_quantity" :value="__('Cantidad a Convertir')" />
                                <x-text-input id="source_quantity" class="block mt-1 w-full" type="number" step="0.01"
                                    name="source_quantity" :value="old('source_quantity')" required />
                                <x-input-error :messages="$errors->get('source_quantity')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Target Product -->
                        <div class="bg-green-50 p-6 rounded-xl border border-green-100">
                            <h3 class="text-lg font-bold text-green-800 mb-4 flex items-center">
                                <span class="bg-green-200 text-green-700 p-1 rounded mr-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                </span>
                                Destino (Entra al Inventario)
                            </h3>

                            <div class="mb-4">
                                <x-input-label for="target_variant_id" :value="__('Producto Destino')" />
                                <div class="mt-1">
                                    <select id="target_variant_id" name="target_variant_id" class="tom-select w-full"
                                        required>
                                        <option value="">Seleccione Producto...</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product['id'] }}" {{ old('target_variant_id') == $product['id'] ? 'selected' : '' }}>
                                                {{ $product['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <x-input-error :messages="$errors->get('target_variant_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="target_quantity" :value="__('Cantidad Resultante')" />
                                <x-text-input id="target_quantity" class="block mt-1 w-full" type="number" step="0.01"
                                    name="target_quantity" :value="old('target_quantity')" required />
                                <x-input-error :messages="$errors->get('target_quantity')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <!-- Notes & Actions -->
                    <div class="mt-6 border-t pt-6">
                        <div class="mb-4">
                            <x-input-label for="notes" :value="__('Notas / Razón')" />
                            <textarea id="notes" name="notes"
                                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                rows="2">{{ old('notes') }}</textarea>
                        </div>

                        <div class="flex justify-end">
                            <x-primary-button class="bg-bakery-gold hover:bg-bakery-dark">
                                {{ __('Procesar Transformación') }}
                            </x-primary-button>
                        </div>
                    </div>

                </form>
            </x-modern-card>
        </div>
    </div>

    <!-- Init TomSelect -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.tom-select').forEach((el) => {
                new TomSelect(el, {
                    create: false,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    }
                });
            });
        });
    </script>
</x-app-layout>