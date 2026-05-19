<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nuevo Pedido Especial') }}
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
        .ts-dropdown {
            border-radius: 0.375rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            z-index: 50; /* Ensure dropdown is on top */
        }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-modern-card variant="glass">
                <form action="{{ route('special-orders.store') }}" method="POST" id="orderForm">
                    @csrf

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                        <!-- Left: Customer & Details -->
                        <div class="lg:col-span-1 space-y-6">
                            <div>
                                <h3 class="text-lg font-bold text-bakery-dark mb-4">Datos del Cliente</h3>
                                <div class="mb-4">
                                    <x-input-label for="customer_id" :value="__('Cliente')" />
                                    <select id="customer_id" name="customer_id" class="tom-select w-full" required>
                                        <option value="">Seleccione Cliente...</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->name }}
                                                ({{ $customer->phone }})</option>
                                        @endforeach
                                    </select>
                                    <div class="mt-2 text-xs text-right">
                                        <a href="{{ route('customers.create') }}"
                                            class="text-bakery-gold hover:underline">+ Crear Nuevo Cliente</a>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t pt-6">
                                <h3 class="text-lg font-bold text-bakery-dark mb-4">Entrega</h3>
                                <div class="mb-4">
                                    <x-input-label for="pickup_date" :value="__('Fecha y Hora de Entrega')" />
                                    <input type="datetime-local" id="pickup_date" name="pickup_date"
                                        class="border-gray-300 focus:border-bakery-gold focus:ring-bakery-gold rounded-md shadow-sm w-full"
                                        required min="{{ now()->format('Y-m-d\TH:i') }}">
                                </div>
                                <div class="mb-4">
                                    <x-input-label for="notes" :value="__('Notas Generales')" />
                                    <textarea id="notes" name="notes" rows="3"
                                        class="border-gray-300 focus:border-bakery-gold focus:ring-bakery-gold rounded-md shadow-sm w-full"
                                        placeholder="Ej: Entregar por la puerta trasera..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Items -->
                        <div class="lg:col-span-2">
                            <h3 class="text-lg font-bold text-bakery-dark mb-4">Productos</h3>

                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 mb-4">
                                <div id="items-container" class="space-y-4">
                                    <!-- Items will be appended here -->
                                </div>

                                <button type="button" id="add-item-btn"
                                    class="mt-4 flex items-center text-bakery-gold font-bold hover:text-bakery-dark">
                                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    Agregar Producto
                                </button>
                            </div>

                            <!-- Totals -->
                            <div class="flex justify-end">
                                <div
                                    class="w-full md:w-1/2 space-y-4 bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                                    <div class="flex justify-between items-center text-lg">
                                        <span class="font-bold text-gray-700">Total Estimado:</span>
                                        <span class="font-bold text-bakery-dark text-xl"
                                            id="total-display">{{ $globalSettings['currency_symbol'] ?? '$' }}
                                            0.00</span>
                                    </div>

                                    <div class="border-t pt-4">
                                        <x-input-label for="deposit_amount" :value="__('Anticipo / Seña Recibida')" />
                                        <div class="flex items-center mt-1">
                                            <span
                                                class="text-gray-500 mr-2 text-lg font-bold">{{ $globalSettings['currency_symbol'] ?? '$' }}</span>
                                            <input type="number" id="deposit_amount" name="deposit_amount"
                                                class="block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 font-bold text-green-600 text-lg"
                                                step="0.01" min="0" placeholder="0.00">
                                        </div>
                                    </div>

                                    <div
                                        class="flex justify-between items-center text-sm font-medium pt-2 text-gray-500">
                                        <span>Saldo Pendiente:</span>
                                        <span id="balance-display">{{ $globalSettings['currency_symbol'] ?? '$' }}
                                            0.00</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-4 border-t pt-6">
                        <a href="{{ route('special-orders.index') }}" class="btn-secondary">Cancelar</a>
                        <button type="submit" class="btn-primary">
                            Registrar Pedido
                        </button>
                    </div>

                </form>
            </x-modern-card>
        </div>
    </div>

    <!-- Script for Dynamic Items -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize TomSelect for Customer
            if(document.getElementById('customer_id')) {
                try {
                    new TomSelect('#customer_id', { create: false, sortField: { field: "text", direction: "asc" } });
                } catch(e) {
                    console.error('TomSelect initialization failed for customer:', e);
                }
            }

            const container = document.getElementById('items-container');
            const addItemBtn = document.getElementById('add-item-btn');
            const depositInput = document.getElementById('deposit_amount');
            const currencySymbol = @json($globalSettings['currency_symbol'] ?? '$');
            
            let itemCount = 0;

            function initTomSelect(element, row) {
                new TomSelect(element, {
                    create: false,
                    sortField: { field: "text", direction: "asc" },
                    placeholder: "Buscar Producto...",
                    onChange: function(value) {
                        if(!value) return;
                        
                        // Find price from option
                        const option = this.wrapper.querySelector(`option[value="${value}"]`) || element.querySelector(`option[value="${value}"]`);
                        
                        // Fallback: iterate raw options if previous methods fail (TomSelect can vary)
                        let price = 0;
                        if(option) {
                             price = option.getAttribute('data-price');
                        } else {
                            // Direct lookup on original select options
                            for(let i=0; i<element.options.length; i++) {
                                if(element.options[i].value == value) {
                                    price = element.options[i].getAttribute('data-price');
                                    break;
                                }
                            }
                        }

                        if (price) {
                            const priceInput = row.querySelector('.price-input');
                            if(priceInput) {
                                priceInput.value = price;
                                calculateTotals();
                            }
                        }
                    }
                });
            }

            window.addItem = function() {
                if(!container) return;

                const index = itemCount;
                // Construct HTML string
                const html = `
                    <div class="item-row grid grid-cols-12 gap-2 items-start bg-white p-3 rounded-lg shadow-sm border border-gray-100" id="item-${index}">
                        <div class="col-span-4">
                            <select name="items[${index}][product_variant_id]" class="product-select w-full rounded border-gray-300" required>
                                <option value="">Producto...</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                        {{ str_replace('"', '&quot;', $product->full_name) }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="text" name="items[${index}][specifications]"
                                placeholder="Espec. (Ej: Feliz Cumple Juan)"
                                class="mt-2 w-full text-xs border-gray-300 rounded text-gray-600 focus:ring-0">
                        </div>
                        <div class="col-span-2">
                            <input type="number" name="items[${index}][quantity]"
                                class="qty-input w-full rounded border-gray-300" placeholder="Cant."
                                min="1" step="1" value="1">
                        </div>
                        <div class="col-span-3">
                            <div class="flex items-center">
                                <span class="text-gray-500 mr-1">${currencySymbol}</span>
                                <input type="number" name="items[${index}][unit_price]"
                                    class="price-input w-full rounded border-gray-300"
                                    placeholder="Precio" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-span-2 pt-2 text-right font-bold text-gray-700 subtotal-display">
                            ${currencySymbol} 0.00
                        </div>
                        <div class="col-span-1 pt-1 text-center">
                            <button type="button" class="text-red-500 hover:text-red-700 remove-item">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                `;

                container.insertAdjacentHTML('beforeend', html);
                
                // Initialize logic for this new row
                const row = container.lastElementChild;
                const select = row.querySelector('.product-select');
                const priceInput = row.querySelector('.price-input');
                const qtyInput = row.querySelector('.qty-input');
                const removeBtn = row.querySelector('.remove-item');

                // Initialize TomSelect
                if(select) initTomSelect(select, row);

                if(priceInput) priceInput.addEventListener('input', calculateTotals);
                if(qtyInput) qtyInput.addEventListener('input', calculateTotals);
                
                if(removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        row.remove();
                        calculateTotals();
                    });
                }
                
                itemCount++;
            }

            window.calculateTotals = function() {
                let total = 0;
                const rows = container.querySelectorAll('.item-row');
                
                rows.forEach(row => {
                    const qtyInput = row.querySelector('.qty-input');
                    const priceInput = row.querySelector('.price-input');
                    
                    const qty = qtyInput ? (parseFloat(qtyInput.value) || 0) : 0;
                    const price = priceInput ? (parseFloat(priceInput.value) || 0) : 0;
                    
                    const subtotal = qty * price;
                    
                    const subtotalElement = row.querySelector('.subtotal-display');
                    if(subtotalElement) {
                        subtotalElement.textContent = currencySymbol + ' ' + subtotal.toFixed(2);
                    }
                    total += subtotal;
                });

                const totalDisplay = document.getElementById('total-display');
                if(totalDisplay) totalDisplay.textContent = currencySymbol + ' ' + total.toFixed(2);
                
                const deposit = depositInput ? (parseFloat(depositInput.value) || 0) : 0;
                const balance = Math.max(0, total - deposit);
                
                const balanceDisplay = document.getElementById('balance-display');
                if(balanceDisplay) balanceDisplay.textContent = currencySymbol + ' ' + balance.toFixed(2);
            }

            if(depositInput) {
                depositInput.addEventListener('input', calculateTotals);
            }

            if(addItemBtn) {
                addItemBtn.addEventListener('click', addItem);
            }

            // Start with one item
            addItem();
        });
    </script>
</x-app-layout>