<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nueva Compra') }}
        </h2>
    </x-slot>

    <!-- TomSelect CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <style>
        /* Custom styles to match bakery theme */
        .ts-control {
            border-radius: 0.375rem;
            border-color: #d1d5db;
            padding: 0.5rem 0.75rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .ts-control.focus {
            border-color: #d97706;
            /* bakery-gold */
            box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.5);
        }

        .ts-dropdown {
            border-radius: 0.375rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <form action="{{ route('purchases.store') }}" method="POST" id="purchaseForm">
                        @csrf

                        <!-- Header Data -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Proveedor</label>
                                <select name="supplier_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50"
                                    required>
                                    <option value="">Seleccione...</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Almacén Destino</label>
                                <select name="warehouse_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50"
                                    required>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Fecha de Compra</label>
                                <input type="date" name="purchase_date" value="{{ date('Y-m-d') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50"
                                    required>
                            </div>
                        </div>

                        <!-- Items Table -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Detalle de Insumos</h3>
                            <table class="min-w-full divide-y divide-gray-200 border" id="itemsTable">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                            Insumo</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase"
                                            width="150">Cantidad</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase"
                                            width="150">Costo Unit.</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase"
                                            width="150">Total</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase"
                                            width="50"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="itemsBody">
                                    <tr class="item-row">
                                        <td class="px-4 py-2">
                                            <select name="items[0][supply_id]"
                                                class="block w-full rounded-md border-gray-300 shadow-sm text-sm"
                                                required>
                                                <option value="">Seleccione Insumo...</option>
                                                @foreach($supplies as $supply)
                                                    <option value="{{ $supply->id }}">{{ $supply->name }}
                                                        ({{ $supply->base_unit }})</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-4 py-2">
                                            <input type="number" name="items[0][quantity]" step="0.01" min="0"
                                                class="block w-full rounded-md border-gray-300 shadow-sm text-sm text-right qty-input"
                                                placeholder="0.00" required>
                                        </td>
                                        <td class="px-4 py-2">
                                            <input type="number" name="items[0][unit_cost]" step="0.01" min="0"
                                                class="block w-full rounded-md border-gray-300 shadow-sm text-sm text-right cost-input"
                                                placeholder="0.00" required>
                                        </td>
                                        <td class="px-4 py-2 text-right font-bold text-gray-700 total-cell">
                                            {{ $globalSettings['currency_symbol'] ?? '$' }}0.00
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <button type="button" class="text-red-600 hover:text-red-900 remove-row">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="bg-gray-50 font-bold">
                                        <td colspan="3" class="px-4 py-2 text-right">Total Compra:</td>
                                        <td class="px-4 py-2 text-right text-lg" id="grandTotal">
                                            {{ $globalSettings['currency_symbol'] ?? '$' }}0.00</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                            <button type="button" id="addRow"
                                class="mt-2 text-sm text-indigo-600 hover:text-indigo-900 font-bold flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Agregar Insumo
                            </button>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700">Notas / Observaciones</label>
                            <textarea name="notes" rows="2"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50"></textarea>
                        </div>

                        <div class="mb-6">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="receive_immediately" value="1"
                                    class="rounded border-gray-300 text-bakery-gold shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50"
                                    checked>
                                <span class="ml-2 text-gray-700 font-bold">Recibir mercadería inmediatamente e ingresar
                                    stock</span>
                            </label>
                            <p class="text-sm text-gray-500 ml-6">Si se desmarca, la compra quedará como "Pendiente" y
                                el stock no se actualizará hasta confirmar la recepción.</p>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('purchases.index') }}"
                                class="text-gray-600 hover:text-gray-900">Cancelar</a>
                            <button type="submit"
                                class="bg-bakery-gold hover:bg-yellow-600 text-white font-bold py-2 px-6 rounded shadow transition">
                                Guardar Compra
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let rowCount = 1;
                const tableBody = document.getElementById('itemsBody');
                const grandTotalEl = document.getElementById('grandTotal');

                // Helper to init TomSelect on a specific select element
                function initTomSelect(selectElement) {
                    new TomSelect(selectElement, {
                        create: false,
                        sortField: {
                            field: "text",
                            direction: "asc"
                        },
                        placeholder: "Buscar insumo...",
                        plugins: ['dropdown_input'],
                    });
                }

                // Init existing selects (first row)
                document.querySelectorAll('.item-row select').forEach(select => {
                    initTomSelect(select);
                });

                function calculateRowTotal(row) {
                    const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
                    const cost = parseFloat(row.querySelector('.cost-input').value) || 0;
                    const total = qty * cost;
                    row.querySelector('.total-cell').textContent = '{{ $globalSettings['currency_symbol'] ?? '$' }}' + total.toFixed(2);
                    return total;
                }

                function updateGrandTotal() {
                    let grandTotal = 0;
                    document.querySelectorAll('.item-row').forEach(row => {
                        grandTotal += calculateRowTotal(row);
                    });
                    grandTotalEl.textContent = '{{ $globalSettings['currency_symbol'] ?? '$' }}' + grandTotal.toFixed(2);
                }

                // Event Delegation for Inputs
                tableBody.addEventListener('input', function (e) {
                    if (e.target.classList.contains('qty-input') || e.target.classList.contains('cost-input')) {
                        updateGrandTotal();
                    }
                });

                // Remove Row
                tableBody.addEventListener('click', function (e) {
                    if (e.target.closest('.remove-row')) {
                        const row = e.target.closest('tr');
                        if (document.querySelectorAll('.item-row').length > 1) {
                            // If it has a TomSelect instance, it might need cleanup, but simply removing the DOM element is usually enough for simple pages.
                            row.remove();
                            updateGrandTotal();
                        }
                    }
                });

                // Add Row
                document.getElementById('addRow').addEventListener('click', function () {
                    // Clone the first row. 
                    // Problem: Clustering with initialized TomSelect is messy because the DOM structure changes (wrapper divs).
                    // Solution: We will create a fresh structure or strip the TomSelect wrapper from the clone.

                    // Better approach: Keep a hidden template or manually reconstruct the row HTML string. 
                    // Manual reconstruction is safer here to avoid cloning initialized widgets.

                    const newRowHtml = `
                            <tr class="item-row">
                                <td class="px-4 py-2">
                                    <select name="items[${rowCount}][supply_id]" class="block w-full rounded-md border-gray-300 shadow-sm text-sm" required>
                                        <option value="">Seleccione Insumo...</option>
                                        @foreach($supplies as $supply)
                                            <option value="{{ $supply->id }}">{{ $supply->name }} ({{ $supply->base_unit }})</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" name="items[${rowCount}][quantity]" step="0.01" min="0" class="block w-full rounded-md border-gray-300 shadow-sm text-sm text-right qty-input" placeholder="0.00" required>
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" name="items[${rowCount}][unit_cost]" step="0.01" min="0" class="block w-full rounded-md border-gray-300 shadow-sm text-sm text-right cost-input" placeholder="0.00" required>
                                </td>
                                <td class="px-4 py-2 text-right font-bold text-gray-700 total-cell">
                                    {{ $globalSettings['currency_symbol'] ?? '$' }}0.00
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <button type="button" class="text-red-600 hover:text-red-900 remove-row">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </td>
                            </tr>
                        `;

                    tableBody.insertAdjacentHTML('beforeend', newRowHtml);

                    // Get the newly added select
                    const newSelect = tableBody.lastElementChild.querySelector('select');
                    initTomSelect(newSelect);

                    rowCount++;
                });
            });
        </script>
    @endpush
</x-app-layout>