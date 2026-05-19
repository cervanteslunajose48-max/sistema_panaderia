<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nuevo Ajuste de Inventario') }}
        </h2>
    </x-slot>

    <!-- TomSelect CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <style>
        .ts-control {
            border-radius: 0.375rem;
            border-color: #d1d5db;
            padding: 0.5rem 0.75rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .ts-control.focus {
            border-color: #d97706;
            box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.5);
        }
    </style>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <form action="{{ route('inventory.adjustments.store') }}" method="POST">
                        @csrf

                        <!-- Top Selector: Type -->
                        <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Ajuste</label>
                                <select name="adjustment_type"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50"
                                    required>
                                    <option value="waste" class="text-red-600">Merma / Desperdicio (-)</option>
                                    <option value="return" class="text-green-600">Devolución (+)</option>
                                    <option value="correction_in" class="text-blue-600">Corrección / Ingreso Manual (+)
                                    </option>
                                    <option value="correction_out" class="text-orange-600">Corrección / Salida Manual
                                        (-)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Item</label>
                                <div class="flex space-x-4 mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="item_type" value="supply" checked
                                            class="text-bakery-gold focus:ring-bakery-gold" onchange="toggleItemType()">
                                        <span class="ml-2">Insumo</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="item_type" value="product"
                                            class="text-bakery-gold focus:ring-bakery-gold" onchange="toggleItemType()">
                                        <span class="ml-2">Producto</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Supply Section -->
                        <div id="supply-section"
                            class="mb-6 border-l-4 border-bakery-gold pl-4 bg-yellow-50 p-4 rounded-r">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Seleccionar
                                        Insumo</label>
                                    <select id="supply-select" name="item_id_supply" class="tom-select w-full"
                                        placeholder="Buscar insumo...">
                                        <option value="">Seleccione...</option>
                                        @foreach($supplies as $supply)
                                            <option value="{{ $supply->id }}">{{ $supply->name }} ({{ $supply->base_unit }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Almacén Afectado</label>
                                    <select name="warehouse_id"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50">
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Product Section (Hidden by default) -->
                        <div id="product-section"
                            class="mb-6 border-l-4 border-bakery-dark pl-4 bg-gray-50 p-4 rounded-r hidden">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Seleccionar
                                        Producto</label>
                                    <select id="product-select" name="item_id_product" class="tom-select w-full"
                                        placeholder="Buscar producto...">
                                        <option value="">Seleccione...</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Quantity & Reason -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad</label>
                                <input type="number" name="quantity" step="0.01" min="0.01"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50"
                                    required placeholder="0.00">
                                <p class="text-xs text-gray-500 mt-1">Ingrese siempre en positivo. El sistema calculará
                                    la entrada/salida según el Tipo de Ajuste.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Motivo / Observación</label>
                                <input type="text" name="reason"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50"
                                    placeholder="Ej: Vencimiento, Rotura, Error de conteo">
                            </div>
                        </div>

                        <!-- Hidden Field for consolidated Item ID -->
                        <!-- Hidden Field for consolidated Item ID - Removed -->

                        <div class="flex justify-end pt-4">
                            <button type="submit"
                                class="bg-bakery-dark hover:bg-gray-800 text-white font-bold py-2 px-6 rounded shadow transition">
                                Guardar Ajuste
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
                // Init TomSelects
                const supplySelect = new TomSelect('#supply-select', {
                    create: false,
                    sortField: { field: "text", direction: "asc" },
                    plugins: ['dropdown_input'],
                });
                const productSelect = new TomSelect('#product-select', {
                    create: false,
                    sortField: { field: "text", direction: "asc" },
                    plugins: ['dropdown_input'],
                });

                window.toggleItemType = function () {
                    const type = document.querySelector('input[name="item_type"]:checked').value;
                    const supplySec = document.getElementById('supply-section');
                    const productSec = document.getElementById('product-section');

                    if (type === 'supply') {
                        supplySec.classList.remove('hidden');
                        productSec.classList.add('hidden');
                        supplySelect.clear(); // Reset to ensure user picks
                    } else {
                        supplySec.classList.add('hidden');
                        productSec.classList.remove('hidden');
                        productSelect.clear();
                    }
                }

                // Sync ID before submit
                document.querySelector('form').addEventListener('submit', function (e) {
                    const type = document.querySelector('input[name="item_type"]:checked').value;
                    const finalIdInput = document.getElementById('final-item-id');

                    if (type === 'supply') {
                        finalIdInput.value = document.getElementById('supply-select').value;
                    } else {
                        finalIdInput.value = document.getElementById('product-select').value;
                    }

                    if (!finalIdInput.value) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Debe seleccionar un item (Insumo o Producto).'
                        });
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>