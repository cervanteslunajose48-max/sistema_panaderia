<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-xl font-bold text-gray-800 flex items-center gap-2">
            <a href="{{ route('orders.index') }}" class="text-gray-400 hover:text-bakery-dark transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            Detalle de Venta #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
        </h2>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Order info -->
        <div class="md:col-span-2 space-y-6">
            <!-- Items Detail -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-bold text-gray-800">Productos Vendidos</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50 text-gray-500 text-xs uppercase tracking-wider">
                                <th class="p-4 font-medium">Producto</th>
                                <th class="p-4 font-medium text-center">Cant.</th>
                                <th class="p-4 font-medium text-right">P. Unit.</th>
                                <th class="p-4 font-medium text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($order->items as $item)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-4">
                                        <p class="font-bold text-gray-800">{{ $item->variant->product->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $item->variant->name }} ({{ $item->variant->sku }})</p>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="bg-gray-100 px-2 py-1 rounded font-medium text-gray-700">
                                            {{ $item->quantity }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-right">@currency($item->unit_price)</td>
                                    <td class="p-4 text-right font-bold text-bakery-dark">
                                        @currency($item->subtotal)
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 border-t border-gray-200">
                            <tr>
                                <td colspan="3" class="p-4 text-right font-bold text-gray-600 uppercase text-sm">Total Venta</td>
                                <td class="p-4 text-right font-black text-xl text-bakery-dark tracking-wider">
                                    @currency($order->total)
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <!-- General Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Información del Ticket</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Fecha:</dt>
                        <dd class="font-medium text-gray-900">{{ $order->created_at->format('d/m/Y H:i:A') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Atendido por:</dt>
                        <dd class="font-medium text-gray-900">
                            <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded-md">
                                {{ $order->user ? $order->user->name : 'N/A' }}
                            </span>
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Estado Pago:</dt>
                        <dd class="font-medium text-green-700 uppercase tracking-widest text-xs font-bold bg-green-50 px-2 py-1 rounded">
                            {{ $order->payment_status }}
                        </dd>
                    </div>
                </dl>

                <div class="mt-6 pt-4 border-t">
                    <button onclick="window.open('{{ route('orders.ticket', $order) }}', '_blank')"
                            class="w-full flex justify-center items-center gap-2 bg-bakery-dark hover:bg-opacity-90 text-white font-bold py-3 px-4 rounded-lg transition-transform hover:-translate-y-0.5 shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Reimprimir Ticket
                    </button>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Cliente</h3>
                @if($order->customer)
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-bakery-cream flex items-center justify-center text-bakery-dark font-bold">
                            {{ substr($order->customer->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $order->customer->name }}</p>
                            <p class="text-xs text-gray-500">{{ $order->customer->email ?? 'Sin correo' }}</p>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-3 text-gray-500">
                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <p class="font-medium italic">Venta al Público General</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
