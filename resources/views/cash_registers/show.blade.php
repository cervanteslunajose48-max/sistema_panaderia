<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Control de Caja') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Header Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="text-sm font-medium text-gray-500">Monto Inicial</div>
                    <div class="text-2xl font-bold text-gray-800">@currency($cashRegister->opening_amount)
                    </div>
                    <div class="text-xs text-gray-400">{{ $cashRegister->opening_time->format('d M H:i') }}</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="text-sm font-medium text-gray-500">Ventas (Sistema)</div>
                    <div class="text-2xl font-bold text-green-700">@currency($salesTotal)</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-bakery-gold">
                    <div class="text-sm font-medium text-gray-500">Saldo Actual Estimado</div>
                    <div class="text-3xl font-bold text-bakery-dark">@currency($currentBalance)</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Left: Movements & Actions -->
                <div class="lg:col-span-2 space-y-8">

                    <!-- Add Movement Form -->
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h3 class="font-bold text-lg mb-4 text-gray-700">Registrar Movimiento (Entrada/Salida)</h3>
                        <form action="{{ route('cash-registers.movement', $cashRegister) }}" method="POST"
                            class="flex gap-4 items-end">
                            @csrf
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">Descripción</label>
                                <input type="text" name="description" placeholder="Ej: Pago de agua, Cambio..."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold"
                                    required>
                            </div>
                            <div class="w-32">
                                <label class="block text-sm font-medium text-gray-700">Monto</label>
                                <input type="number" step="0.01" name="amount" placeholder="0.00"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold"
                                    required>
                            </div>
                            <div class="w-32">
                                <label class="block text-sm font-medium text-gray-700">Tipo</label>
                                <select name="type"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold">
                                    <option value="out">Salida (-)</option>
                                    <option value="in">Entrada (+)</option>
                                </select>
                            </div>
                            <button type="submit"
                                class="bg-gray-800 text-white px-4 py-2 rounded shadow hover:bg-gray-700 font-bold">Registrar</button>
                        </form>
                    </div>

                    <!-- History Table -->
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h3 class="font-bold text-lg mb-4 text-gray-700">Historial de Movimientos</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Hora
                                        </th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                            Descripción</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tipo
                                        </th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">
                                            Monto</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($cashRegister->movements as $movement)
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-gray-500">
                                                {{ $movement->created_at->format('H:i') }}
                                            </td>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $movement->description }}</td>
                                            <td class="px-4 py-2 text-sm">
                                                @if($movement->type == 'in')
                                                    <span class="text-green-600 font-bold">Entrada</span>
                                                @else
                                                    <span class="text-red-600 font-bold">Salida</span>
                                                @endif
                                            </td>
                                            <td
                                                class="px-4 py-2 text-sm text-right font-medium {{ $movement->type == 'out' ? 'text-red-600' : 'text-green-600'}}">
                                                @currency($movement->amount)
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <!-- Right: Close Register -->
                <div class="space-y-8">
                    <div class="bg-red-50 border border-red-200 shadow-sm sm:rounded-lg p-6">
                        <h3 class="font-bold text-lg mb-4 text-red-800">Cerrar Caja (Arqueo)</h3>
                        <p class="text-sm text-gray-600 mb-4">Al cerrar caja, el sistema calculará las diferencias.
                            Ingrese el dinero efectivo real que tiene en mano.</p>

                        <form action="{{ route('cash-registers.close', $cashRegister) }}" method="POST"
                            onsubmit="return confirm('¿Seguro que desea cerrar la caja? Esta acción no se puede deshacer.');">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-red-800">Efectivo Real en Caja</label>
                                <input type="number" step="0.01" name="actual_cash"
                                    class="mt-1 block w-full rounded-md border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500 font-bold text-xl"
                                    placeholder="0.00" required>
                            </div>
                            <button type="submit"
                                class="w-full bg-red-600 text-white font-bold py-3 rounded shadow hover:bg-red-700 transition">
                                CERRAR TURNO
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>