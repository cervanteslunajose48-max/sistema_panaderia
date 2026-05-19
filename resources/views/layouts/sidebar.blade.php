<div class="fixed inset-y-0 left-0 w-64 bg-gradient-to-b from-bakery-dark via-bakery-dark to-bakery-dark-deep text-bakery-cream transition-transform duration-300 transform -translate-x-full lg:translate-x-0 z-40 shadow-2xl flex flex-col"
    id="sidebar">
    <!-- Header with Logo -->
    <div
        class="flex flex-col items-center justify-center h-auto py-6 bg-bakery-dark/90 backdrop-blur-sm border-b border-gray-700 px-4">
        @if(isset($globalSettings['shop_logo']))
            <div class="relative mb-2">
                <div class="absolute inset-0 bg-bakery-gold/20 blur-xl rounded-full"></div>
                <img src="{{ asset('storage/' . $globalSettings['shop_logo']) }}" alt="Logo"
                    class="relative h-14 w-auto object-contain">
            </div>
        @endif
        <div
            class="inline-flex items-center gap-2 bg-bakery-gold/10 backdrop-blur-sm px-3 py-1 rounded-full border border-bakery-gold/30">
            <div class="w-2 h-2 bg-bakery-gold rounded-full animate-pulse"></div>
            <h1 class="text-sm font-bold tracking-wider text-bakery-gold">
                {{ strtoupper($globalSettings['shop_name'] ?? 'PANADERÍA') }}
            </h1>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="mt-5 px-2 space-y-1 overflow-y-auto flex-1 pb-4">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}"
            class="group flex items-center px-2 py-2 text-base font-medium rounded-md hover:bg-bakery-gold hover:text-bakery-dark transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-bakery-gold text-bakery-dark shadow-lg' : '' }}">
            <svg class="mr-4 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            Dashboard
        </a>

        <!-- POS (Cajeros) -->
        @can('execute pos')
            <a href="{{ route('pos.index') }}"
                class="group flex items-center px-2 py-2 text-base font-medium rounded-md hover:bg-bakery-gold hover:text-bakery-dark transition-all duration-200 {{ request()->routeIs('pos.*') ? 'bg-bakery-gold text-bakery-dark shadow-lg' : '' }}">
                <svg class="mr-4 h-6 w-6" fill="none" class="w-6 h-6" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                    </path>
                </svg>
                Punto de Venta
            </a>

            <!-- Cash Control -->
            <a href="{{ route('cash-registers.create') }}"
                class="group flex items-center px-2 py-2 text-base font-medium rounded-md hover:bg-bakery-gold hover:text-bakery-dark transition-all duration-200 {{ request()->routeIs('cash-registers.*') ? 'bg-bakery-gold text-bakery-dark shadow-lg' : '' }}">
                <svg class="mr-4 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a1 1 0 11-2 0 1 1 0 012 0z">
                    </path>
                </svg>
                Control de Caja
            </a>

            <!-- Historial de Ventas -->
            <a href="{{ route('orders.index') }}"
                class="group flex items-center px-2 py-2 text-base font-medium rounded-md hover:bg-bakery-gold hover:text-bakery-dark transition-all duration-200 {{ request()->routeIs('orders.*') ? 'bg-bakery-gold text-bakery-dark shadow-lg' : '' }}">
                <svg class="mr-4 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Historial Ventas
            </a>
        @endcan

        <!-- Productos -->
        <a href="{{ route('products.index') }}"
            class="group flex items-center px-2 py-2 text-base font-medium rounded-md hover:bg-bakery-gold hover:text-bakery-dark transition-all duration-200 {{ request()->routeIs('products.*') ? 'bg-bakery-gold text-bakery-dark shadow-lg' : '' }}">
            <svg class="mr-4 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
            </svg>
            Productos
        </a>

        <!-- Categorías -->
        <a href="{{ route('categories.index') }}"
            class="group flex items-center px-2 py-2 text-base font-medium rounded-md hover:bg-bakery-gold hover:text-bakery-dark transition-all duration-200 {{ request()->routeIs('categories.*') ? 'bg-bakery-gold text-bakery-dark shadow-lg' : '' }}">
            <svg class="mr-4 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                </path>
            </svg>
            Categorías
        </a>

        <!-- Insumos & Proveedores -->
        @can('manage inventory')
            <a href="{{ route('suppliers.index') }}"
                class="group flex items-center px-2 py-2 text-base font-medium rounded-md hover:bg-bakery-gold hover:text-bakery-dark transition-all duration-200 {{ request()->routeIs('suppliers.*') ? 'bg-bakery-gold text-bakery-dark shadow-lg' : '' }}">
                <svg class="mr-4 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                    </path>
                </svg>
                Proveedores
            </a>

            <a href="{{ route('supplies.index') }}"
                class="group flex items-center px-2 py-2 text-base font-medium rounded-md hover:bg-bakery-gold hover:text-bakery-dark transition-all duration-200 {{ request()->routeIs('supplies.*') ? 'bg-bakery-gold text-bakery-dark shadow-lg' : '' }}">
                <svg class="mr-4 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                    </path>
                </svg>
                Insumos
            </a>

            <!-- Special Orders -->
            <a href="{{ route('special-orders.index') }}"
                class="group flex items-center px-2 py-2 text-base font-medium rounded-md hover:bg-bakery-gold hover:text-bakery-dark transition-all duration-200 {{ request()->routeIs('special-orders.*') ? 'bg-bakery-gold text-bakery-dark shadow-lg' : '' }}">
                <svg class="mr-4 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                    </path>
                </svg>
                Pedidos Especiales
            </a>

            <a href="{{ route('purchases.index') }}"
                class="group flex items-center px-2 py-2 text-base font-medium rounded-md hover:bg-bakery-gold hover:text-bakery-dark transition-all duration-200 {{ request()->routeIs('purchases.*') ? 'bg-bakery-gold text-bakery-dark shadow-lg' : '' }}">
                <svg class="mr-4 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a1 1 0 11-2 0 1 1 0 012 0z">
                    </path>
                </svg>
                Compras
            </a>

            <a href="{{ route('warehouses.index') }}"
                class="group flex items-center px-2 py-2 text-base font-medium rounded-md hover:bg-bakery-gold hover:text-bakery-dark transition-all duration-200 {{ request()->routeIs('warehouses.*') ? 'bg-bakery-gold text-bakery-dark shadow-lg' : '' }}">
                <svg class="mr-4 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                    </path>
                </svg>
                Almacenes
            </a>

            <!-- Mermas y Ajustes -->
            <a href="{{ route('inventory.adjustments.create') }}"
                class="flex items-center space-x-2 py-2 px-4 rounded transition duration-200 {{ request()->routeIs('inventory.adjustments.create') ? 'bg-orange-700 text-white' : 'hover:bg-orange-700 hover:text-white' }}">
                <span class="text-xl">⚖️</span>
                <span>Mermas y Ajustes</span>
            </a>
            <a href="{{ route('inventory.transformations.create') }}"
                class="flex items-center space-x-2 py-2 px-4 rounded transition duration-200 {{ request()->routeIs('inventory.transformations.create') ? 'bg-orange-700 text-white' : 'hover:bg-orange-700 hover:text-white' }}">
                <span class="text-xl">🔄</span>
                <span>Transformaciones</span>
            </a>
        @endcan

        <!-- Recetas -->
        <a href="{{ route('recipes.index') }}"
            class="group flex items-center px-2 py-2 text-base font-medium rounded-md hover:bg-bakery-gold hover:text-bakery-dark transition-all duration-200 {{ request()->routeIs('recipes.*') ? 'bg-bakery-gold text-bakery-dark shadow-lg' : '' }}">
            <svg class="mr-4 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                </path>
            </svg>
            Recetas
        </a>

        <!-- Producción -->
        @can('manage production')
            <a href="{{ route('production.create') }}"
                class="group flex items-center px-2 py-2 text-base font-medium rounded-md hover:bg-bakery-gold hover:text-bakery-dark transition-all duration-200 {{ request()->routeIs('production.*') ? 'bg-bakery-gold text-bakery-dark shadow-lg' : '' }}">
                <svg class="mr-4 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
                Producción
            </a>
        @endcan

        <!-- Reportes -->
        @can('view reports')
            <a href="{{ route('reports.index') }}"
                class="group flex items-center px-2 py-2 text-base font-medium rounded-md hover:bg-bakery-gold hover:text-bakery-dark transition-all duration-200 {{ request()->routeIs('reports.*') ? 'bg-bakery-gold text-bakery-dark shadow-lg' : '' }}">
                <svg class="mr-4 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                    </path>
                </svg>
                Reportes
            </a>

            <a href="{{ route('inventory.index') }}"
                class="group flex items-center px-2 py-2 text-base font-medium rounded-md hover:bg-bakery-gold hover:text-bakery-dark transition-all duration-200 {{ request()->routeIs('inventory.index') ? 'bg-bakery-gold text-bakery-dark shadow-lg' : '' }}">
                <svg class="mr-4 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                    </path>
                </svg>
                Historial Inventario
            </a>
        @endcan

        <!-- Usuarios -->
        @role('admin')
        <a href="{{ route('users.index') }}"
            class="group flex items-center px-2 py-2 text-base font-medium rounded-md hover:bg-bakery-gold hover:text-bakery-dark transition-all duration-200 {{ request()->routeIs('users.*') ? 'bg-bakery-gold text-bakery-dark shadow-lg' : '' }}">
            <svg class="mr-4 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                </path>
            </svg>
            Usuarios
        </a>
        @endrole

        <!-- Clientes -->
        <a href="{{ route('customers.index') }}"
            class="group flex items-center px-2 py-2 text-base font-medium rounded-md hover:bg-bakery-gold hover:text-bakery-dark transition-all duration-200 {{ request()->routeIs('customers.*') ? 'bg-bakery-gold text-bakery-dark shadow-lg' : '' }}">
            <svg class="mr-4 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            Clientes
        </a>

        <!-- Configuración -->
        <a href="{{ route('settings.index') }}"
            class="group flex items-center px-2 py-2 text-base font-medium rounded-md hover:bg-bakery-gold hover:text-bakery-dark transition-all duration-200 {{ request()->routeIs('settings.*') ? 'bg-bakery-gold text-bakery-dark shadow-lg' : '' }}">
            <svg class="mr-4 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Configuración
        </a>
    </nav>

    <!-- (Clock Widget Removed) -->
</div>