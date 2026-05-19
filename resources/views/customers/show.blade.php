<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $customer->name }} <span class="text-sm text-gray-500 font-normal">| Perfil del Cliente</span>
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('customers.edit', $customer) }}" class="btn-secondary">
                    Editar Datos
                </a>
                <a href="{{ route('customers.index') }}" class="btn-secondary">
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Top Section: Info & Stats -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- 1. Customer Card -->
                <x-modern-card class="flex flex-col h-full">
                    <div class="flex items-center space-x-4 mb-4">
                        <div
                            class="h-16 w-16 bg-bakery-gold text-white rounded-full flex items-center justify-center text-2xl font-bold">
                            {{ substr($customer->name, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-bakery-dark">{{ $customer->name }}</h3>
                            <p class="text-gray-500 text-sm">{{ $customer->email ?? 'Sin email' }}</p>
                            <p class="text-gray-500 text-sm">{{ $customer->phone ?? 'Sin teléfono' }}</p>
                        </div>
                    </div>

                    <div class="border-t pt-4 space-y-2 text-sm text-gray-600 flex-1">
                        @if($customer->birthday)
                            <div
                                class="flex items-center justify-between {{ $customer->birthday->isBirthday() ? 'bg-yellow-100 p-2 rounded text-yellow-800 font-bold' : '' }}">
                                <span>🎂 Cumpleaños:</span>
                                <span>{{ $customer->birthday->format('d M, Y') }} ({{ $customer->birthday->age }}
                                    años)</span>
                            </div>
                            @if($customer->birthday->isBirthday())
                                <div class="text-center text-xs text-bakery-orange font-bold animate-pulse mt-1">
                                    ¡Es su cumpleaños hoy! Ofrecer descuento.
                                </div>
                            @endif
                        @else
                            <div class="flex items-center justify-between text-gray-400 italic">
                                <span>🎂 Cumpleaños:</span>
                                <span>No registrado</span>
                            </div>
                        @endif

                        <div class="flex items-center justify-between mt-2">
                            <span>📍 Dirección:</span>
                            <span class="text-right truncate max-w-[150px]">{{ $customer->address ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <span>🆔 RUC/DNI:</span>
                            <span>{{ $customer->tax_id ?? '-' }}</span>
                        </div>
                    </div>
                </x-modern-card>

                <!-- 2. Loyalty & Stats -->
                <x-modern-card
                    class="bg-gradient-to-br from-bakery-dark to-gray-800 text-white flex flex-col justify-between">
                    <div>
                        <h4 class="text-bakery-gold uppercase tracking-widest text-xs font-bold mb-2">Puntos de
                            Fidelidad</h4>
                        <div class="text-5xl font-display font-bold text-white mb-2">
                            {{ $customer->loyalty_points }} <span
                                class="text-lg font-normal text-bakery-gold-light">pts</span>
                        </div>
                        <p class="text-xs text-gray-400">Acumulados en compras recientes.</p>
                    </div>

                    <div class="mt-8 grid grid-cols-2 gap-4 border-t border-gray-700 pt-4">
                        <div>
                            <div class="text-2xl font-bold text-bakery-peach">
                                @currency($totalSpent)
                            </div>
                            <div class="text-xs text-gray-400 uppercase">Gasto Total Histórico</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-bakery-peach">
                                {{ $orders->total() }}
                            </div>
                            <div class="text-xs text-gray-400 uppercase">Pedidos Realizados</div>
                        </div>
                    </div>
                </x-modern-card>

                <!-- 3. Notes (Sticky Note Style) -->
                <div
                    class="bg-yellow-50 p-6 rounded-2xl shadow-md border-l-4 border-yellow-400 relative rotate-1 hover:rotate-0 transition-transform duration-300">
                    <div class="absolute top-4 right-4 text-yellow-300">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z">
                            </path>
                        </svg>
                    </div>
                    <h4 class="text-yellow-800 font-bold uppercase text-xs mb-3">Notas & Preferencias</h4>
                    <p
                        class="text-gray-700 italic whitespace-pre-wrap font-handwriting text-sm leading-relaxed min-h-[100px]">
                        {{ $customer->notes ?? 'Sin notas registradas...' }}
                    </p>
                </div>

            </div>

            <!-- Bottom Section: History & Top Products -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left: Recent Orders -->
                <div class="lg:col-span-2">
                    <x-modern-card title="Historial de Compras">
                        <div class="overflow-x-auto">
                            <table class="table-modern w-full">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($orders as $order)
                                        <tr>
                                            <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                            <td class="font-bold">@currency($order->total)</td>
                                            <td>
                                                <span
                                                    class="badge {{ $order->status === 'completed' ? 'badge-success' : 'badge-warning' }}">
                                                    {{ $order->status }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="#" class="text-bakery-gold hover:underline text-xs">Ver detalle</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-gray-500 italic">No hay compras
                                                registradas.</td>
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

                <!-- Right: Top Products -->
                <div>
                    <x-modern-card title="Productos Favoritos">
                        <ul class="space-y-3">
                            @forelse($topProducts as $index => $product)
                                <li
                                    class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100">
                                    <div class="flex items-center">
                                        <span
                                            class="h-6 w-6 rounded-full bg-bakery-peach/20 text-bakery-orange flex items-center justify-center text-xs font-bold mr-3">
                                            {{ $index + 1 }}
                                        </span>
                                        <span
                                            class="text-gray-700 font-medium text-sm truncate max-w-[120px]">{{ $product->name }}</span>
                                    </div>
                                    <span
                                        class="text-xs font-bold text-gray-500 bg-white px-2 py-1 rounded shadow-sm border">
                                        {{ $product->total_qty }} u.
                                    </span>
                                </li>
                            @empty
                                <li class="text-gray-500 text-sm italic text-center py-4">Sin datos suficientes.</li>
                            @endforelse
                        </ul>
                    </x-modern-card>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>