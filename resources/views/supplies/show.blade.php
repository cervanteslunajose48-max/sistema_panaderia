<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalle de Insumo') }}: {{ $supply->name }}
            </h2>
            <a href="{{ route('supplies.index') }}" class="text-indigo-600 hover:text-indigo-900">
                &larr; Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- General Info Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Información General</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <span class="block text-sm font-medium text-gray-500">Nombre</span>
                        <span class="block text-lg font-semibold text-gray-900">{{ $supply->name }}</span>
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-500">Unidad Base</span>
                        <span class="badge badge-bakery">{{ strtoupper($supply->base_unit) }}</span>
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-500">Costo Actual</span>
                        <span class="block text-lg font-semibold text-gray-900">@currency($supply->cost)</span>
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-500">Proveedor Principal</span>
                        <span class="block text-lg text-gray-900">{{ $supply->supplier?->name ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-500">Min. Stock</span>
                        <span class="block text-lg text-gray-900">{{ $supply->min_stock }}</span>
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-500">Estado</span>
                        <span class="badge {{ $supply->status ? 'badge-success' : 'badge-danger' }}">
                            {{ $supply->status ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Stock by Warehouse -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Stock por Almacén</h3>
                <div class="overflow-x-auto">
                    <table class="table-modern w-full">
                        <thead>
                            <tr>
                                <th>Almacén</th>
                                <th class="text-right">Cantidad</th>
                                <th class="text-right">Valorizado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($supply->stocks as $stock)
                                <tr>
                                    <td>{{ $stock->warehouse->name }}</td>
                                    <td class="text-right font-bold">{{ number_format($stock->quantity, 2) }}
                                        {{ $supply->base_unit }}</td>
                                    <td class="text-right">@currency($stock->quantity * $supply->cost)</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-gray-500 py-4">No hay stock registrado en
                                        almacenes.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50 font-bold">
                                <td>TOTAL</td>
                                <td class="text-right">{{ number_format($supply->stocks->sum('quantity'), 2) }}</td>
                                <td class="text-right">@currency($supply->stocks->sum('quantity') * $supply->cost)</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Movement History -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Historial de Movimientos</h3>
                <div class="overflow-x-auto">
                    <table class="table-modern w-full text-sm">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Descripción</th>
                                <th>Usuario</th>
                                <th class="text-right">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($movements as $movement)
                                <tr>
                                    <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if(in_array($movement->type, ['purchase', 'return', 'correction_in']))
                                            <span class="text-green-600 font-bold">Entrada</span>
                                        @else
                                            <span class="text-red-600 font-bold">Salida</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="block text-gray-900">{{ $movement->type }}</span>
                                        <span class="text-xs text-gray-500">{{ $movement->description }}</span>
                                    </td>
                                    <td>{{ $movement->user->name ?? 'Sistema' }}</td>
                                    <td
                                        class="text-right font-bold {{ $movement->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $movement->quantity > 0 ? '+' : '' }}{{ number_format($movement->quantity, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-gray-500 py-4">No hay movimientos registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">
                        {{ $movements->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>