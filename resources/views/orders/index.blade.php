<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-xl font-bold text-gray-800">
            Historial de Ventas (Órdenes)
        </h2>
    </x-slot>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <!-- Dashboard Header / Actions -->
        <div class="p-6 border-b border-gray-100 bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex-1">
                <form action="{{ route('orders.index') }}" method="GET" class="flex flex-wrap gap-2 items-center">
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50 text-sm">
                    <span class="text-gray-500">hasta</span>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50 text-sm">
                    
                    <select name="customer_id" class="rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50 text-sm min-w-[200px]">
                        <option value="">Todos los Clientes</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="bg-bakery-dark hover:bg-opacity-90 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                        Filtrar
                    </button>
                    @if(request()->filled(['start_date', 'end_date', 'customer_id']))
                        <a href="{{ route('orders.index') }}" class="text-gray-500 hover:text-gray-700 text-sm underline">
                            Limpiar
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100/50 text-gray-500 text-xs uppercase tracking-wider">
                        <th class="p-4 font-medium">N° Ticket</th>
                        <th class="p-4 font-medium">Fecha</th>
                        <th class="p-4 font-medium">Cliente</th>
                        <th class="p-4 font-medium text-right">Total</th>
                        <th class="p-4 font-medium">Vendedor / Cajero</th>
                        <th class="p-4 font-medium text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @if($orders->count() > 0)
                        @foreach ($orders as $order)
                            <tr class="hover:bg-gray-50 transition-colors group">
                                <td class="p-4">
                                    <span class="font-bold text-gray-800">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="p-4">
                                    <span class="text-gray-600">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                                </td>
                                <td class="p-4">
                                    @if($order->customer)
                                        <span class="text-gray-800 font-medium">{{ $order->customer->name }}</span>
                                    @else
                                        <span class="text-gray-500 italic">Cliente General</span>
                                    @endif
                                </td>
                                <td class="p-4 text-right">
                                    <span class="font-bold text-bakery-dark tracking-wide">
                                        @currency($order->total)
                                    </span>
                                </td>
                                <td class="p-4">
                                    <span class="text-gray-600 bg-gray-100 px-2 py-1 rounded-md text-sm">
                                        {{ $order->user ? $order->user->name : 'N/A' }}
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <div class="flex justify-center items-center gap-2">
                                        <a href="{{ route('orders.show', $order) }}" 
                                           class="text-blue-500 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition-colors"
                                           title="Ver Detalles">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        
                                        <button onclick="window.open('{{ route('orders.ticket', $order) }}', '_blank')"
                                                class="text-bakery-dark hover:text-bakery-gold bg-bakery-cream hover:bg-yellow-100 p-2 rounded-lg transition-colors"
                                                title="Imprimir Ticket">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-lg font-medium text-gray-900 mb-1">No se encontraron ventas</p>
                                    <p class="text-sm">Ajusta los filtros o revisa más tarde.</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($orders->hasPages())
            <div class="p-4 border-t border-gray-100 bg-gray-50">
                {{ $orders->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
