<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-display font-bold text-2xl text-bakery-dark leading-tight">
                {{ __('Insumos y Materias Primas') }}
            </h2>
            
            <x-action-button variant="primary" size="md" onclick="openAddModal()">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span>Nuevo Insumo</span>
            </x-action-button>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <x-stat-card-modern 
                    title="Total Insumos"
                    :value="$supplies->total()"
                    color="blue"
                    :icon="'<svg class=\'w-6 h-6 text-white\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4\'></path></svg>'"
                />
                
                <x-stat-card-modern 
                    title="Stock Crítico"
                    :value="$supplies->filter(fn($s) => $s->stocks->sum('quantity') <= $s->min_stock)->count()"
                    color="red"
                    trend="down"
                    trendValue="Atención"
                    :icon="'<svg class=\'w-6 h-6 text-white\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z\'></path></svg>'"
                />
                
                <x-stat-card-modern 
                    title="Valor Total Stock"
                    :value="($globalSettings['currency_symbol'] ?? '$') . ' ' . number_format($supplies->sum(fn($s) => $s->cost * $s->stocks->sum('quantity')), 2)"
                    color="green"
                    :icon="'<svg class=\'w-6 h-6 text-white\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'"
                />
                
                <x-stat-card-modern 
                    title="Proveedores Activos"
                    :value="$suppliers->where('status', true)->count()"
                    color="orange"
                    :icon="'<svg class=\'w-6 h-6 text-white\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4\'></path></svg>'"
                />
            </div>

            {{-- Search and Filters --}}
            <x-modern-card variant="glass">
                <form method="GET" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- Search --}}
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                            <x-search-input name="search" :value="request('search')" placeholder="Buscar insumo..." />
                        </div>
                        
                        {{-- Filter by Supplier --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Proveedor</label>
                            <select name="supplier_id" 
                                class="w-full rounded-xl border-gray-300 focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-30">
                                <option value="">Todos los proveedores</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- Filter by Stock Status --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Estado de Stock</label>
                            <select name="stock_status" 
                                class="w-full rounded-xl border-gray-300 focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-30">
                                <option value="">Todos</option>
                                <option value="critical" {{ request('stock_status') == 'critical' ? 'selected' : '' }}>Stock Crítico</option>
                                <option value="sufficient" {{ request('stock_status') == 'sufficient' ? 'selected' : '' }}>Stock Suficiente</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex justify-end gap-2">
                        <x-action-button variant="secondary" size="sm" type="submit">
                            Aplicar Filtros
                        </x-action-button>
                        <a href="{{ route('supplies.index') }}" class="btn-secondary px-4 py-2 text-sm">
                            Limpiar
                        </a>
                    </div>
                </form>
            </x-modern-card>

            {{-- Supplies Table --}}
            <x-modern-card variant="elevated">
                <div class="overflow-x-auto">
                    <table class="table-modern w-full">
                        <thead>
                            <tr>
                                <th>Insumo</th>
                                <th>Unidad</th>
                                <th>Costo Prom.</th>
                                <th>Proveedor</th>
                                <th>Stock Total</th>
                                <th>Estado</th>
                                <th class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($supplies as $supply)
                                <tr class="group">
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-bakery-peach bg-opacity-20 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-bakery-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-sm font-semibold text-gray-900">{{ $supply->name }}</div>
                                                <div class="text-xs text-gray-500">Stock mín: {{ $supply->min_stock }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-bakery">{{ strtoupper($supply->base_unit) }}</span>
                                    </td>
                                    <td>
                                        <div class="text-sm font-semibold text-gray-900">@currency($supply->cost)</div>
                                    </td>
                                    <td>
                                        <div class="text-sm text-gray-600">
                                            {{ $supply->supplier?->name ?? '—' }}
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $totalStock = $supply->stocks->sum('quantity');
                                            $isLow = $totalStock <= $supply->min_stock;
                                        @endphp
                                        <div class="flex items-center gap-2">
                                            <span class="px-3 py-1 inline-flex text-xs font-bold rounded-full {{ $isLow ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                {{ number_format($totalStock, 2) }}
                                            </span>
                                            @if($isLow)
                                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                </svg>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $supply->status ? 'badge-success' : 'badge-danger' }}">
                                            {{ $supply->status ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="flex justify-end items-center gap-2">
                                            <a href="{{ route('supplies.show', $supply) }}" 
                                                class="p-2 rounded-lg text-emerald-600 hover:bg-emerald-50 transition-colors"
                                                title="Ver Detalle">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>

                                            <button
                                                onclick="openRestockModal({{ $supply->id }}, '{{ $supply->name }}', '{{ $supply->base_unit }}')"
                                                class="p-2 rounded-lg text-indigo-600 hover:bg-indigo-50 transition-colors"
                                                title="Reponer Stock">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                            </button>

                                            <a href="{{ route('supplies.edit', $supply) }}"
                                                class="p-2 rounded-lg text-blue-600 hover:bg-blue-50 transition-colors"
                                                title="Editar">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>

                                            <form action="{{ route('supplies.toggle', $supply) }}" method="POST" class="inline-block">
                                                @csrf
                                                <button type="submit"
                                                    class="p-2 rounded-lg transition-colors {{ $supply->status ? 'text-green-600 hover:bg-green-50' : 'text-gray-400 hover:bg-gray-50' }}"
                                                    title="{{ $supply->status ? 'Desactivar' : 'Activar' }}">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        @if($supply->status)
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        @else
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                        @endif
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-500">
                                            <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                            </svg>
                                            <p class="text-lg font-medium">No hay insumos registrados</p>
                                            <p class="text-sm mt-1">Comienza agregando tu primer insumo</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-6">
                    {{ $supplies->links() }}
                </div>
            </x-modern-card>
        </div>
    </div>

    {{-- Add/Edit Modal --}}
    <div id="addModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 rounded-t-2xl">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-900">Nuevo Insumo</h3>
                    <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <form action="{{ route('supplies.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Insumo*</label>
                        <input type="text" name="name"
                            class="input-modern"
                            placeholder="Ej: Harina de Trigo" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Unidad de Medida*</label>
                        <select name="base_unit" class="input-modern">
                            <option value="kg">Kilogramos (kg)</option>
                            <option value="g">Gramos (g)</option>
                            <option value="l">Litros (l)</option>
                            <option value="ml">Mililitros (ml)</option>
                            <option value="unit">Unidad</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stock Mínimo*</label>
                        <input type="number" step="0.01" name="min_stock"
                            class="input-modern"
                            placeholder="0.00" value="0" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Proveedor</label>
                        <select name="supplier_id" class="input-modern">
                            <option value="">-- Sin proveedor --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Costo Base ({{ $globalSettings['currency_symbol'] ?? '$' }})*
                        </label>
                        <input type="number" step="0.01" name="cost"
                            class="input-modern"
                            placeholder="0.00" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Almacén</label>
                        <select name="warehouse_id" class="input-modern">
                            <option value="">-- Seleccionar almacén --</option>
                            @foreach(\App\Models\Warehouse::where('status', true)->get() as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Opcional: Selecciona un almacén si deseas agregar stock inicial</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stock Inicial</label>
                        <input type="number" step="0.01" name="initial_stock"
                            class="input-modern"
                            placeholder="0.00" value="0">
                        <p class="mt-1 text-xs text-gray-500">Se agregará al almacén seleccionado</p>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <x-action-button variant="secondary" type="button" onclick="closeAddModal()">
                        Cancelar
                    </x-action-button>
                    <x-action-button variant="primary" type="submit">
                        Guardar Insumo
                    </x-action-button>
                </div>
            </form>
        </div>
    </div>

    {{-- Restock Modal --}}
    <div id="restockModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white p-6 rounded-2xl shadow-2xl w-full max-w-md">
            <h3 class="text-lg font-bold mb-4">Reponer Stock: <span id="modalSupplyName"></span></h3>
            <form id="restockForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Cantidad a Agregar (<span id="modalSupplyUnit"></span>)
                    </label>
                    <input type="number" step="0.01" name="quantity"
                        class="input-modern"
                        placeholder="0.00" required>
                </div>
                <input type="hidden" name="cost" value="">

                <div class="flex justify-end gap-3">
                    <x-action-button variant="secondary" type="button" onclick="closeRestockModal()">
                        Cancelar
                    </x-action-button>
                    <x-action-button variant="success" type="submit">
                        Confirmar
                    </x-action-button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
        }
        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
        }
        function openRestockModal(id, name, unit) {
            document.getElementById('restockModal').classList.remove('hidden');
            document.getElementById('modalSupplyName').textContent = name;
            document.getElementById('modalSupplyUnit').textContent = unit;
            document.getElementById('restockForm').action = `/supplies/${id}/restock`;
        }
        function closeRestockModal() {
            document.getElementById('restockModal').classList.add('hidden');
        }
    </script>
</x-app-layout>