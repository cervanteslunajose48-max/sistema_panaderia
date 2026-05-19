<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center no-print">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalle de Pedido #') }}{{ $specialOrder->id }}
            </h2>
            <div class="flex gap-2">
                <button onclick="window.print()" class="btn-secondary">
                    Imprimir Ticket
                </button>
                <a href="{{ route('special-orders.index') }}" class="btn-secondary">
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <!-- Management Card (No Print) -->
            <x-modern-card variant="bordered" class="mb-6 no-print">
                <h3 class="font-bold text-lg mb-4">Gestionar Estado</h3>
                <form action="{{ route('special-orders.update-status', $specialOrder) }}" method="POST"
                    class="flex flex-wrap gap-2 items-center">
                    @csrf
                    @method('PATCH')

                    @php
                        $statuses = [
                            'pending' => 'Pendiente',
                            'confirmed' => 'Confirmado',
                            'in_production' => 'En Producción',
                            'ready' => 'Listo para Retirar',
                            'delivered' => 'Entregado',
                            'cancelled' => 'Cancelado',
                        ];
                    @endphp

                    @foreach($statuses as $key => $label)
                        <button type="submit" name="status" value="{{ $key }}"
                            class="px-4 py-2 rounded-lg text-sm font-semibold transition-colors
                                    {{ $specialOrder->status === $key ? 'bg-bakery-dark text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </form>
            </x-modern-card>

            <!-- Order Ticket (Printable) -->
            <div
                class="bg-white p-8 rounded-xl shadow-lg border border-gray-100 print:shadow-none print:border-none printable-area">

                <!-- Header -->
                <div class="text-center mb-8 border-b pb-4">
                    <h1 class="text-3xl font-display font-bold text-bakery-dark-deep mb-2">Panadería & Pastelería</h1>
                    <p class="text-sm text-gray-500 uppercase tracking-wide">Orden de Pedido Especial</p>
                    <h2 class="text-4xl font-bold text-bakery-gold mt-2">#{{ $specialOrder->id }}</h2>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-2 gap-8 mb-8">
                    <div>
                        <h4 class="text-xs uppercase text-gray-400 font-bold mb-1">Cliente</h4>
                        <p class="text-lg font-bold text-gray-900">{{ $specialOrder->customer->name }}</p>
                        <p class="text-gray-600">{{ $specialOrder->customer->phone }}</p>
                    </div>
                    <div class="text-right">
                        <h4 class="text-xs uppercase text-gray-400 font-bold mb-1">Fecha de Entrega</h4>
                        <p class="text-2xl font-bold text-bakery-dark">{{ $specialOrder->pickup_date->format('d/m/Y') }}
                        </p>
                        <p class="text-lg text-bakery-gold font-bold">{{ $specialOrder->pickup_date->format('h:i A') }}
                        </p>
                    </div>
                </div>

                <!-- Items Table -->
                <table class="w-full mb-8">
                    <thead class="border-b-2 border-gray-100">
                        <tr>
                            <th class="text-left py-2 text-gray-500 font-bold uppercase text-xs">Producto</th>
                            <th class="text-right py-2 text-gray-500 font-bold uppercase text-xs">Cant.</th>
                            <th class="text-right py-2 text-gray-500 font-bold uppercase text-xs">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($specialOrder->items as $item)
                            <tr>
                                <td class="py-4">
                                    <p class="font-bold text-gray-800">{{ $item->productVariant->product->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $item->productVariant->name }}</p>
                                    @if($item->specifications)
                                        <div
                                            class="mt-1 bg-yellow-50 p-2 rounded text-xs text-yellow-800 italic border border-yellow-100 inline-block">
                                            "{{ $item->specifications }}"
                                        </div>
                                    @endif
                                </td>
                                <td class="text-right py-4 align-top font-bold">{{ $item->quantity + 0 }}</td>
                                <td class="text-right py-4 align-top font-bold text-gray-800">@currency($item->subtotal)
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Totals -->
                <div class="flex justify-end border-t pt-4">
                    <div class="w-64 space-y-2">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span>@currency($specialOrder->total_amount)</span>
                        </div>
                        <div class="flex justify-between text-green-600 font-medium">
                            <span>Anticipo / Seña</span>
                            <span>- @currency($specialOrder->deposit_amount)</span>
                        </div>
                        <div
                            class="flex justify-between text-xl font-bold text-bakery-dark border-t border-dashed pt-2 mt-2">
                            <span>Saldo a Pagar</span>
                            <span>@currency($specialOrder->balance)</span>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                @if($specialOrder->notes)
                    <div class="mt-8 bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h4 class="text-xs uppercase text-gray-400 font-bold mb-1">Notas Generales</h4>
                        <p class="text-gray-700 italic">{{ $specialOrder->notes }}</p>
                    </div>
                @endif

                <div class="mt-12 text-center text-xs text-gray-400">
                    <p>Gracias por su preferencia</p>
                    <p class="mt-1">{{ now()->format('d/m/Y H:i') }}</p>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>