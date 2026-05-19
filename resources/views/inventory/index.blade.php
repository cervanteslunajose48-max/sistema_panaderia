<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-display text-3xl font-bold text-bakery-dark-deep">
                Historial de Movimientos
            </h2>
            <p class="text-sm text-gray-600 mt-1">Registro completo de movimientos de inventario</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Filters Card --}}
            <x-modern-card variant="glass" class="mb-6">
                <form method="GET" action="{{ route('inventory.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        {{-- Type Filter --}}
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                            <select name="type" id="type"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold/50 text-sm">
                                <option value="">Todos</option>
                                @foreach($types as $type)
                                    <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $type)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Item Type Filter --}}
                        <div>
                            <label for="item_type"
                                class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                            <select name="item_type" id="item_type"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold/50 text-sm">
                                <option value="">Todos</option>
                                <option value="supply" {{ request('item_type') == 'supply' ? 'selected' : '' }}>Insumos
                                </option>
                                <option value="product" {{ request('item_type') == 'product' ? 'selected' : '' }}>
                                    Productos</option>
                            </select>
                        </div>

                        {{-- Date From --}}
                        <div>
                            <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
                            <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold/50 text-sm">
                        </div>

                        {{-- Date To --}}
                        <div>
                            <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
                            <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold/50 text-sm">
                        </div>

                        {{-- Search --}}
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                placeholder="Descripción..."
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold/50 text-sm">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('inventory.export', request()->all()) }}"
                            class="btn-secondary text-sm flex items-center text-green-700">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Exportar Excel
                        </a>
                        <a href="{{ route('inventory.print', request()->all()) }}" target="_blank"
                            class="btn-secondary text-sm flex items-center text-gray-700">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Imprimir / PDF
                        </a>
                        <a href="{{ route('inventory.index') }}" class="btn-secondary text-sm">
                            Limpiar Filtros
                        </a>
                        <button type="submit" class="btn-primary text-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filtrar
                        </button>
                    </div>
                </form>
            </x-modern-card>

            {{-- Movements Table --}}
            @if($movements->count() > 0)
                <x-modern-card variant="glass">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Fecha
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tipo
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Producto/Insumo
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Almacén
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Cantidad
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Usuario
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Descripción
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($movements as $movement)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $movement->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $typeColors = [
                                                    'production_in' => 'bg-green-100 text-green-800',
                                                    'production_out' => 'bg-red-100 text-red-800',
                                                    'restock' => 'bg-blue-100 text-blue-800',
                                                    'sale' => 'bg-yellow-100 text-yellow-800',
                                                    'adjustment' => 'bg-purple-100 text-purple-800',
                                                ];
                                                $colorClass = $typeColors[$movement->type] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $colorClass }}">
                                                {{ ucfirst(str_replace('_', ' ', $movement->type)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            @if($movement->supply)
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 text-blue-500 mr-2" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                                        </path>
                                                    </svg>
                                                    <a href="{{ route('supplies.index') }}"
                                                        class="text-blue-600 hover:text-blue-800 font-medium">
                                                        {{ $movement->supply->name }}
                                                    </a>
                                                </div>
                                            @elseif($movement->productVariant)
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4">
                                                        </path>
                                                    </svg>
                                                    <a href="{{ route('products.index') }}"
                                                        class="text-green-600 hover:text-green-800 font-medium">
                                                        {{ $movement->productVariant->product->name }} -
                                                        {{ $movement->productVariant->name }}
                                                    </a>
                                                </div>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $movement->warehouse->name ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold">
                                            @if(in_array($movement->type, ['production_in', 'restock']))
                                                <span class="text-green-600">+{{ $movement->quantity }}</span>
                                            @else
                                                <span class="text-red-600">-{{ $movement->quantity }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $movement->user->name ?? 'Sistema' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ Str::limit($movement->description, 50) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-4">
                        {{ $movements->links() }}
                    </div>
                </x-modern-card>
            @else
                <x-modern-card variant="glass">
                    <div class="text-center py-12">
                        <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">No hay movimientos</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            No se encontraron movimientos de inventario con los filtros seleccionados
                        </p>
                    </div>
                </x-modern-card>
            @endif
        </div>
    </div>
</x-app-layout>