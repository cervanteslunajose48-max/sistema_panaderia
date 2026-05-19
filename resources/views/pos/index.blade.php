<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-xl font-bold text-gray-800">
            Punto de Venta
        </h2>
    </x-slot>

    <div class="h-[calc(100vh-110px)] min-h-[500px] grid grid-cols-1 lg:grid-cols-12 gap-0 shadow-sm rounded-lg overflow-hidden bg-gray-100 border border-gray-200 w-full min-w-0">

        <!-- LEFT COLUMN: Products -->
        <div class="col-span-1 lg:col-span-8 flex flex-col h-full border-r border-gray-200 overflow-hidden">
            <!-- Search & Filter Bar -->
            <div class="p-4 bg-white shadow-sm z-10">
                <div class="flex gap-2 mb-2">
                    <input type="text" id="searchInput" placeholder="Buscar producto (Código o Nombre)..."
                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50"
                        autofocus>
                </div>
                <!-- Categories Chips -->
                <div class="flex gap-2 overflow-x-auto pb-2 no-scrollbar" id="categoryFilters">
                    <button onclick="filterCategory('all')"
                        class="px-4 py-1 rounded-full bg-bakery-dark text-white text-sm font-bold whitespace-nowrap hover:bg-opacity-90 transition">
                        Todos
                    </button>
                    @foreach($categories as $category)
                        <button onclick="filterCategory('{{ $category->id }}')"
                            class="px-4 py-1 rounded-full bg-bakery-cream text-bakery-dark text-sm font-medium whitespace-nowrap hover:bg-bakery-gold hover:text-white transition">
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Product Grid -->
            <div class="flex-1 overflow-y-auto p-4 bg-gray-100">
                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4" id="productGrid">
                    @foreach($products as $product)
                        @foreach($product->variants as $variant)
                            <div class="bg-white rounded-lg shadow hover:shadow-md cursor-pointer transition transform hover:-translate-y-1 product-card group"
                                onclick="addToCart({{ $variant->id }}, '{{ addslashes($product->name) }}', '{{ addslashes($variant->name) }}', {{ $variant->price }}, {{ $variant->current_stock }}, {{ $variant->stock_track ? 'true' : 'false' }})"
                                data-name="{{ strtolower($product->name) }} {{ strtolower($variant->name) }} {{ $variant->sku }}"
                                data-category="{{ $product->category_id }}">

                                <div class="h-24 bg-bakery-cream flex items-center justify-center rounded-t-lg relative overflow-hidden">
                                    @if($variant->stock_track)
                                        <span
                                            class="absolute top-1 right-1 text-xs font-bold px-2 py-0.5 rounded-full {{ $variant->current_stock > 10 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} z-10 shadow-sm">
                                            Stock: {{ $variant->current_stock }}
                                        </span>
                                    @endif
                                    
                                    @if($product->primaryImage)
                                        <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" 
                                             alt="{{ $product->name }}" 
                                             class="w-full h-full object-cover transition duration-300 group-hover:scale-110">
                                    @else
                                        <span class="text-3xl">🍞</span> <!-- Placeholder Icon -->
                                    @endif
                                </div>

                                <div class="p-3">
                                    <h4 class="font-bold text-gray-800 text-sm leading-tight mb-1">{{ $product->name }}</h4>
                                    <p class="text-xs text-gray-500 mb-2">{{ $variant->name }}</p>
                                    <div class="flex justify-between items-center">
                                        <span class="font-bold text-bakery-dark">@currency($variant->price)</span>
                                        <span
                                            class="bg-bakery-gold text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition">
                                            + Agregar
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Cart -->
        <div class="col-span-1 lg:col-span-4 bg-white flex flex-col h-full shadow-xl relative z-20 overflow-hidden">

            <!-- Cart Header -->
            <div class="p-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h2 class="font-bold text-lg text-gray-800 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                    Carrito de Compras
                </h2>
                <button onclick="clearCart()" class="text-red-500 text-sm hover:text-red-700">Vaciar</button>
            </div>

            <!-- Customer Selector -->
            <div class="p-4 border-b border-gray-200">
                <select id="customerSelect"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50 text-sm">
                    <option value="">Cliente General (Público)</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Cart Items -->
            <div class="flex-1 overflow-y-auto p-4 space-y-3" id="cartItems">
                <!-- Items will be injected here by JS -->
                <div id="emptyCartMessage" class="text-center text-gray-400 mt-10">
                    <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    <p>El carrito está vacío</p>
                </div>
            </div>

            <!-- Cart Footer (Totals & Pay) -->
            <div class="p-4 bg-gray-50 border-t border-gray-200">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-600">Subtotal</span>
                    <span class="font-bold text-gray-800"
                        id="cartSubtotal">{{ $globalSettings['currency_symbol'] ?? '$' }}0.00</span>
                </div>
                <div class="flex justify-between items-center mb-4 text-xl">
                    <span class="font-bold text-gray-900">Total</span>
                    <span class="font-bold text-bakery-dark"
                        id="cartTotal">{{ $globalSettings['currency_symbol'] ?? '$' }}0.00</span>
                </div>
                <button id="payButton" onclick="processPayment()" disabled
                    class="w-full bg-bakery-green hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-bold py-4 px-4 rounded shadow-lg transition transform hover:-translate-y-0.5 flex justify-center items-center text-lg">
                    <span>COBRAR</span>
                    <svg class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Payment Processing Modal (Loading) -->
    <div id="processingModal"
        class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 hidden flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-xl flex flex-col items-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-bakery-gold mb-4"></div>
            <p class="text-lg font-semibold text-gray-700">Procesando Venta...</p>
        </div>
    </div>

    @push('scripts')
        <!-- SweetAlert2 CDN -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            // State
            let cart = [];
            // Inject currency symbol from global settings (or default to $)
            const currencySymbol = "{{ $globalSettings['currency_symbol'] ?? '$' }}";

            // DOM Elements
            const cartItemsContainer = document.getElementById('cartItems');
            const emptyCartMessage = document.getElementById('emptyCartMessage');
            const cartSubtotalEl = document.getElementById('cartSubtotal');
            const cartTotalEl = document.getElementById('cartTotal');
            const payButton = document.getElementById('payButton');
            const searchInput = document.getElementById('searchInput');
            const productGrid = document.getElementById('productGrid');
            const processingModal = document.getElementById('processingModal');

            // Search Logic
            searchInput.addEventListener('keyup', (e) => {
                const term = e.target.value.toLowerCase();
                const cards = productGrid.querySelectorAll('.product-card');
                cards.forEach(card => {
                    const name = card.getAttribute('data-name');
                    if (name.includes(term)) {
                        card.classList.remove('hidden');
                    } else {
                        card.classList.add('hidden');
                    }
                });
            });

            // Category Filter Logic
            window.filterCategory = function (catId) {
                const cards = productGrid.querySelectorAll('.product-card');
                cards.forEach(card => {
                    const cardCat = card.getAttribute('data-category');
                    if (catId === 'all' || cardCat === catId) {
                        card.classList.remove('hidden');
                    } else {
                        card.classList.add('hidden');
                    }
                });
            };

            // Add to Cart Logic
            window.addToCart = function (variantId, productName, variantName, price, stock, stockTrack) {
                const existingItem = cart.find(item => item.variantId === variantId);

                if (stockTrack && stock <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Producto agotado',
                        text: 'No hay stock disponible para este producto.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    return;
                }

                if (existingItem) {
                    if (stockTrack && existingItem.quantity >= stock) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Stock insuficiente',
                            text: 'No hay suficiente stock disponible para agregar más.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        return;
                    }
                    existingItem.quantity++;
                } else {
                    cart.push({
                        variantId,
                        productName,
                        variantName,
                        price,
                        quantity: 1,
                        maxStock: stock,
                        stockTrack: stockTrack
                    });
                }
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                })

                Toast.fire({
                    icon: 'success',
                    title: 'Producto agregado'
                })
                renderCart();
            };

            // Render Cart
            function renderCart() {
                cartItemsContainer.innerHTML = '';

                if (cart.length === 0) {
                    emptyCartMessage.style.display = 'block';
                    payButton.disabled = true;
                    cartSubtotalEl.textContent = `${currencySymbol}0.00`;
                    cartTotalEl.textContent = `${currencySymbol}0.00`;
                    return;
                }

                emptyCartMessage.style.display = 'none';
                payButton.disabled = false;

                let total = 0;

                cart.forEach((item, index) => {
                    const subtotal = item.price * item.quantity;
                    total += subtotal;

                    const cartRow = document.createElement('div');
                    cartRow.className = 'bg-gray-50 p-3 rounded-lg';
                    cartRow.innerHTML = `
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="flex-1">
                                                <p class="font-bold text-sm text-gray-800">${item.productName}</p>
                                                <p class="text-xs text-gray-500">${item.variantName}</p>
                                                <p class="text-sm text-bakery-dark font-bold mt-1">${currencySymbol}${item.price.toFixed(2)}</p>
                                            </div>
                                            <button onclick="removeFromCart(${index})" class="text-red-500 hover:text-red-700 ml-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-2 bg-white rounded-md border border-gray-200">
                                                <button onclick="updateQuantity(${index}, -1)" class="px-3 py-1 text-bakery-dark hover:bg-gray-100 font-bold">-</button>
                                                <span class="px-2 font-bold text-gray-700 min-w-[30px] text-center">${item.quantity}</span>
                                                <button onclick="updateQuantity(${index}, 1)" class="px-3 py-1 text-bakery-dark hover:bg-gray-100 font-bold">+</button>
                                            </div>
                                            <span class="font-bold text-gray-800">${currencySymbol}${subtotal.toFixed(2)}</span>
                                        </div>
                                    `;
                    cartItemsContainer.appendChild(cartRow);
                });

                cartSubtotalEl.textContent = `${currencySymbol}${total.toFixed(2)}`;
                cartTotalEl.textContent = `${currencySymbol}${total.toFixed(2)}`;
            }

            // Update Quantity
            window.updateQuantity = function (index, change) {
                const item = cart[index];
                const newQty = item.quantity + change;

                if (newQty <= 0) {
                    removeFromCart(index);
                    return;
                }

                if (item.stockTrack && change > 0 && newQty > item.maxStock) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Stock máximo alcanzado',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    return;
                }

                item.quantity = newQty;
                renderCart();
            };

            // Remove from Cart
            window.removeFromCart = function (index) {
                cart.splice(index, 1);
                renderCart();
            };

            // Clear Cart
            window.clearCart = function () {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¿Deseas vaciar todo el carrito?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, vaciar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        cart = [];
                        renderCart();
                        Swal.fire(
                            '¡Vaciado!',
                            'El carrito ha sido vaciado.',
                            'success'
                        )
                    }
                })
            };

            // Process Payment
            window.processPayment = async function () {
                if (cart.length === 0) return;

                processingModal.classList.remove('hidden');

                const payload = {
                    customer_id: document.getElementById('customerSelect').value || null,
                    items: cart.map(item => ({
                        variant_id: item.variantId,
                        quantity: item.quantity
                    }))
                };

                try {
                    const response = await fetch("{{ route('pos.store') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(payload)
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        // Open ticket in new tab
                        if (data.order_id) {
                            const ticketUrl = "{{ route('orders.ticket', ':id') }}".replace(':id', data.order_id);
                            window.open(ticketUrl, '_blank');
                        }
                        cart = [];
                        renderCart();
                        Swal.fire({
                            icon: 'success',
                            title: '¡Venta Exitosa!',
                            text: 'La venta se ha procesado correctamente.',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Error desconocido al procesar la venta',
                        });
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudo conectar con el servidor.',
                    });
                } finally {
                    processingModal.classList.add('hidden');
                }
            };
            // End of Process Payment
        </script>
        <style>
            .no-scrollbar::-webkit-scrollbar {
                display: none;
            }

            .no-scrollbar {
                -ms-overflow-style: none;
                /* IE and Edge */
                scrollbar-width: none;
                /* Firefox */
            }
        </style>
    @endpush
</x-app-layout>