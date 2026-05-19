<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Pedidos Especiales') }}
            </h2>
            <a href="{{ route('special-orders.create') }}" class="btn-primary">
                {{ __('Nuevo Pedido') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-modern-card variant="glass">
                
                <!-- Filters -->
                <div class="mb-6 flex gap-4">
                    <a href="{{ route('special-orders.index') }}" class="px-3 py-1 rounded-full text-sm {{ !request('status') ? 'bg-bakery-gold text-white' : 'bg-gray-100 text-gray-600' }}">Todos</a>
                    <a href="{{ route('special-orders.index', ['status' => 'pending']) }}" class="px-3 py-1 rounded-full text-sm {{ request('status') == 'pending' ? 'bg-yellow-100 text-yellow-700 font-bold' : 'bg-gray-100 text-gray-600' }}">Pendientes</a>
                    <a href="{{ route('special-orders.index', ['status' => 'in_production']) }}" class="px-3 py-1 rounded-full text-sm {{ request('status') == 'in_production' ? 'bg-blue-100 text-blue-700 font-bold' : 'bg-gray-100 text-gray-600' }}">En Producción</a>
                    <a href="{{ route('special-orders.index', ['status' => 'ready']) }}" class="px-3 py-1 rounded-full text-sm {{ request('status') == 'ready' ? 'bg-green-100 text-green-700 font-bold' : 'bg-gray-100 text-gray-600' }}">Listos</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="table-modern w-full">
                        <thead>
                            <tr>
                                <th># Orden</th>
                                <th>Cliente</th>
                                <th>Fecha de Entrega</th>
                                <th>Total</th>
                                <th>Pendiente</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td class="font-bold text-bakery-dark">#{{ $order->id }}</td>
                                    <td>
                                        <div class="text-sm font-medium text-gray-900">{{ $order->customer->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $order->customer->phone }}</div>
                                    </td>
                                    <td>
                                        <div class="text-sm font-bold {{ $order->pickup_date->isToday() ? 'text-red-600' : 'text-gray-900' }}">
                                            {{ $order->pickup_date->format('d/m/Y h:i A') }}
                                        </div>
                                        <div class="text-xs text-gray-500">{{ $order->pickup_date->diffForHumans() }}</div>
                                    </td>
                                    <td>@currency($order->total_amount)</td>
                                    <td>
                                        @if($order->balance > 0)
                                            <span class="text-red-500 font-bold">@currency($order->balance)</span>
                                        @else
                                            <span class="text-green-500 font-bold">Pagado</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusClasses = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'confirmed' => 'bg-blue-100 text-blue-800',
                                                'in_production' => 'bg-purple-100 text-purple-800',
                                                'ready' => 'bg-green-100 text-green-800',
                                                'delivered' => 'bg-gray-100 text-gray-800',
                                                'cancelled' => 'bg-red-100 text-red-800',
                                            ];
                                            $statusLabels = [
                                                'pending' => 'Pendiente',
                                                'confirmed' => 'Confirmado',
                                                'in_production' => 'En Producción',
                                                'ready' => 'Listo',
                                                'delivered' => 'Entregado',
                                                'cancelled' => 'Cancelado',
                                            ];
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses[$order->status] }}">
                                            {{ $statusLabels[$order->status] }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('special-orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-900 font-bold text-sm">Ver Detalle</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-gray-500">No hay pedidos registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    {{ $orders->links() }}
                </div>
            </x-modern-card>
        </div>
    </div>
</x-app-layout>
