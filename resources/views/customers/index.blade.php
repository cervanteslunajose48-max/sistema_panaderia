@php
    $totalCustomers = $customers->total();
    $activeCustomers = \App\Models\Customer::where('status', 1)->count();
    $totalOrders = \App\Models\Order::whereHas('customer')->count();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-display text-3xl font-bold text-bakery-dark-deep">
                    Clientes
                </h2>
                <p class="text-sm text-gray-600 mt-1">Gestión de clientes y historial de compras</p>
            </div>
            <x-action-button 
                href="{{ route('customers.create') }}"
                variant="primary"
                size="lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Nuevo Cliente
            </x-action-button>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <x-stat-card-modern 
                    title="Total Clientes"
                    :value="$totalCustomers"
                    color="blue"
                    :icon="'<svg class=&quot;w-6 h-6&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; viewBox=&quot;0 0 24 24&quot;><path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;2&quot; d=&quot;M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z&quot;/></svg>'"
                />
                
                <x-stat-card-modern 
                    title="Clientes Activos"
                    :value="$activeCustomers"
                    color="green"
                    :icon="'<svg class=&quot;w-6 h-6&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; viewBox=&quot;0 0 24 24&quot;><path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;2&quot; d=&quot;M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z&quot;/></svg>'"
                />
                
                <x-stat-card-modern 
                    title="Total Órdenes"
                    :value="$totalOrders"
                    color="orange"
                    :icon="'<svg class=&quot;w-6 h-6&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; viewBox=&quot;0 0 24 24&quot;><path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;2&quot; d=&quot;M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z&quot;/></svg>'"
                />
            </div>

            {{-- Customers Grid --}}
            @if($customers->count() > 0)
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
                    @foreach($customers as $customer)
                        <x-modern-card variant="bordered" class="group hover:shadow-2xl transition-all duration-300 hover-lift">
                            {{-- Customer Header --}}
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-14 h-14 rounded-full bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white font-bold text-xl">
                                        {{ strtoupper(substr($customer->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-bakery-dark text-lg group-hover:text-bakery-gold transition-colors">
                                            {{ $customer->name }}
                                        </h3>
                                        <p class="text-xs text-gray-500">
                                            Cliente desde {{ $customer->created_at->format('M Y') }}
                                        </p>
                                    </div>
                                </div>
                                <span class="badge badge-{{ $customer->status ? 'success' : 'danger' }}">
                                    {{ $customer->status ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>

                            {{-- Contact Information --}}
                            <div class="space-y-2 mb-4">
                                @if($customer->phone)
                                    <div class="flex items-center gap-2 text-sm text-gray-700">
                                        <svg class="w-4 h-4 text-bakery-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        <a href="tel:{{ $customer->phone }}" class="hover:text-bakery-gold transition-colors">
                                            {{ $customer->phone }}
                                        </a>
                                    </div>
                                @endif

                                @if($customer->email)
                                    <div class="flex items-center gap-2 text-sm text-gray-700">
                                        <svg class="w-4 h-4 text-bakery-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        <a href="mailto:{{ $customer->email }}" class="hover:text-bakery-gold transition-colors truncate">
                                            {{ $customer->email }}
                                        </a>
                                    </div>
                                @endif

                                @if($customer->address)
                                    <div class="flex items-start gap-2 text-sm text-gray-700">
                                        <svg class="w-4 h-4 text-bakery-gold mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span class="line-clamp-2">{{ $customer->address }}</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Purchase History Stats --}}
                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <div class="p-3 bg-blue-50 rounded-lg text-center">
                                    <p class="text-2xl font-bold text-blue-600">{{ $customer->orders->count() ?? 0 }}</p>
                                    <p class="text-xs text-gray-600">Órdenes</p>
                                </div>
                                <div class="p-3 bg-green-50 rounded-lg text-center">
                                    <p class="text-lg font-bold text-green-600">
                                        {{ $globalSettings['currency_symbol'] ?? '$' }}{{ number_format($customer->orders->sum('total') ?? 0, 2) }}
                                    </p>
                                    <p class="text-xs text-gray-600">Total Gastado</p>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex gap-2">
                                <a href="{{ route('customers.show', $customer) }}" 
                                    class="flex-1 btn-primary text-center bg-bakery-dark hover:bg-bakery-dark-deep border-none text-white px-3 py-2 rounded-lg shadow-sm hover:shadow-md transition-all text-sm flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Ver Perfil
                                </a>
                                <a href="{{ route('customers.edit', $customer) }}" 
                                    class="flex-1 btn-secondary text-center">
                                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Editar
                                </a>
                                
                                <form action="{{ route('customers.toggle', $customer) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" 
                                        class="w-full btn-{{ $customer->status ? 'warning' : 'success' }}">
                                        @if($customer->status)
                                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                            </svg>
                                            Desactivar
                                        @else
                                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Activar
                                        @endif
                                    </button>
                                </form>
                            </div>
                        </x-modern-card>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $customers->links() }}
                </div>
            @else
                {{-- Empty State --}}
                <x-modern-card variant="glass">
                    <div class="text-center py-12">
                        <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">No hay clientes registrados</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Comienza agregando tu primer cliente
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('customers.create') }}" class="btn-primary">
                                <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Crear Primer Cliente
                            </a>
                        </div>
                    </div>
                </x-modern-card>
            @endif
        </div>
    </div>
</x-app-layout>