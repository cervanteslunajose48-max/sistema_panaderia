<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Insumo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form action="{{ route('supplies.update', $supply) }}" method="POST" class="p-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-6">
                        <!-- Nombre -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nombre del Insumo</label>
                            <input type="text" name="name" value="{{ old('name', $supply->name) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50"
                                required>
                            @error('name')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Proveedor -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Proveedor</label>
                            <select name="supplier_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50">
                                <option value="">Sin proveedor</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id', $supply->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Costo Base -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Costo Base
                                ({{ $globalSettings['currency_symbol'] ?? '$' }})</label>
                            <input type="number" step="0.01" name="cost" value="{{ old('cost', $supply->cost) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50"
                                required>
                            @error('cost')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Stock Mínimo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Stock Mínimo</label>
                            <input type="number" step="0.01" name="min_stock"
                                value="{{ old('min_stock', $supply->min_stock) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50">
                            @error('min_stock')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Stock por Almacén (Editable) -->
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Gestión de Stock por
                                Almacén</label>

                            @php
                                $warehouses = \App\Models\Warehouse::where('status', true)->get();
                                $stockByWarehouse = $supply->stocks->keyBy('warehouse_id');
                            @endphp

                            <div class="space-y-3">
                                @foreach($warehouses as $warehouse)
                                    @php
                                        $currentStock = $stockByWarehouse->get($warehouse->id);
                                    @endphp
                                    <div
                                        class="p-3 bg-gray-50 rounded-lg border {{ $currentStock && $currentStock->quantity > 0 ? 'border-green-200 bg-green-50' : 'border-gray-200' }}">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex-1">
                                                <p class="font-medium text-gray-900">{{ $warehouse->name }}</p>
                                                <p class="text-xs text-gray-500">
                                                    {{ $warehouse->location ?? 'Sin ubicación' }}</p>
                                            </div>
                                            <div class="w-32">
                                                <input type="number" step="0.01"
                                                    name="warehouse_stocks[{{ $warehouse->id }}]"
                                                    value="{{ $currentStock ? $currentStock->quantity : 0 }}"
                                                    placeholder="0.00"
                                                    class="w-full text-right rounded border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50 text-sm">
                                            </div>
                                            <span class="ml-2 text-xs text-gray-500">{{ $supply->base_unit }}</span>
                                        </div>
                                        @if($currentStock && $currentStock->quantity > 0)
                                            <p class="text-xs text-green-600">
                                                <svg class="w-3 h-3 inline-block" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                                Stock actual: {{ number_format($currentStock->quantity, 2) }}
                                                {{ $supply->base_unit }}
                                            </p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <p class="mt-3 text-xs text-gray-500">
                                <svg class="w-3 h-3 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Actualiza las cantidades para cada almacén. Los valores se sincronizarán al guardar.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6 space-x-3">
                        <a href="{{ route('supplies.index') }}"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded">
                            Cancelar
                        </a>
                        <button type="submit"
                            class="bg-bakery-gold hover:bg-bakery-dark text-white font-bold py-2 px-4 rounded shadow transition duration-300">
                            Actualizar Insumo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>