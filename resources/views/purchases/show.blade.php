<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalle de Compra') }} #{{ $purchase->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Proveedor: {{ $purchase->supplier->name }}</h3>
                            <p class="text-sm text-gray-600">Almacén: {{ $purchase->warehouse->name }}</p>
                            <p class="text-sm text-gray-600">Fecha: {{ $purchase->purchase_date->format('d/m/Y') }}</p>
                            <p class="text-sm text-gray-600 mt-2">Registrado por:
                                {{ $purchase->user->name ?? 'Sistema' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-gray-900">
                                {{ $globalSettings['currency_symbol'] ?? '$' }}{{ number_format($purchase->total_amount, 2) }}
                            </div>
                            <div class="mt-2">
                                @if($purchase->status === 'pending')
                                    <span
                                        class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pendiente de Recepción
                                    </span>
                                @elseif($purchase->status === 'received')
                                    <span
                                        class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Recibido
                                    </span>
                                @else
                                    <span
                                        class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ ucfirst($purchase->status) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($purchase->notes)
                        <div class="mt-4 p-4 bg-gray-50 rounded border">
                            <h4 class="text-xs font-bold text-gray-500 uppercase">Notas</h4>
                            <p class="text-sm text-gray-700">{{ $purchase->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Action Bar -->
            @if($purchase->status === 'pending')
                <div class="flex justify-end gap-4 mb-6">
                    <form action="{{ route('purchases.destroy', $purchase) }}" method="POST"
                        id="deleteForm-{{ $purchase->id }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="confirmDelete({{ $purchase->id }})"
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded shadow transition">
                            Eliminar Compra
                        </button>
                    </form>

                    <form action="{{ route('purchases.receive', $purchase) }}" method="POST"
                        id="receiveForm-{{ $purchase->id }}">
                        @csrf
                        <button type="button" onclick="confirmReceive({{ $purchase->id }})"
                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow transition flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Recibir Mercadería e Ingresar Stock
                        </button>
                    </form>
                </div>

                <script>
                    function confirmDelete(id) {
                        Swal.fire({
                            title: '¿Eliminar Compra?',
                            text: "Esta acción no se puede deshacer.",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Sí, eliminar',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                document.getElementById('deleteForm-' + id).submit();
                            }
                        })
                    }

                    function confirmReceive(id) {
                        Swal.fire({
                            title: '¿Confirmar Recepción?',
                            text: "Esto aumentará el stock en el almacén seleccionado.",
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#10b981', // green-500
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Sí, recibir insumos',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                document.getElementById('receiveForm-' + id).submit();
                            }
                        })
                    }
                </script>
            @endif

            <!-- Items Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Detalle de Insumos</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Insumo</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Cantidad
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Costo Unit.
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($purchase->items as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $item->supply->name }}
                                        <span class="text-gray-500 text-xs">({{ $item->supply->base_unit }})</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                        {{ number_format($item->quantity, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                        {{ $globalSettings['currency_symbol'] ?? '$' }}{{ number_format($item->unit_cost, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-right">
                                        {{ $globalSettings['currency_symbol'] ?? '$' }}{{ number_format($item->total_cost, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right font-bold text-gray-900">Total:</td>
                                <td class="px-6 py-4 text-right font-bold text-gray-900 text-lg">
                                    {{ $globalSettings['currency_symbol'] ?? '$' }}{{ number_format($purchase->total_amount, 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('purchases.index') }}" class="text-indigo-600 hover:text-indigo-900 font-medium">←
                    Volver al listado</a>
            </div>
        </div>
    </div>
</x-app-layout>