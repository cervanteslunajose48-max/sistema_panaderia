<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reportes y Analíticas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Header Actions -->
            <div class="flex justify-end mb-4">
                <a href="{{ route('reports.production') }}"
                    class="inline-flex items-center px-4 py-2 bg-bakery-gold border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                    Reporte de Producción
                </a>
            </div>

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <style>
                    @media print {
                        /* Aggressively hide UI elements */
                        #sidebar, header, .no-print, #sidebar-overlay { 
                            display: none !important; 
                        }

                        /* Reset Body and Main Containers */
                        body { 
                            background: white !important; 
                            -webkit-print-color-adjust: exact; 
                            print-color-adjust: exact; 
                        }
                        
                        /* Target the layout wrapper divs specifically using Tailwind classes logic if possible, or generic structure */
                        /* The main content wrapper usually has lg:pl-64. We must override this. */
                        .lg\:pl-64 {
                            padding-left: 0 !important;
                        }
                        
                        main { 
                            padding: 0 !important; 
                            margin: 0 !important; 
                            overflow: visible !important; 
                        }

                        .max-w-7xl { 
                            max-width: 100% !important; 
                            margin: 0 !important; 
                            padding: 0 !important; 
                            width: 100% !important;
                        }

                        .shadow-sm { 
                            box-shadow: none !important; 
                            border: 1px solid #ddd !important; 
                        }

                        /* Layout: Stack elements vertically */
                        .grid { 
                            display: block !important; 
                        }
                        .grid > div { 
                            margin-bottom: 20px !important; 
                            page-break-inside: avoid !important; 
                            break-inside: avoid !important;
                            width: 100% !important;
                        }

                        /* Charts */
                        .relative {
                            height: auto !important;
                            width: 100% !important;
                        }
                        canvas {
                            max-width: 100% !important; 
                            max-height: 300px !important; /* Smaller height for print to fit */
                        }
                        
                        /* Alerts / Tables */
                        table {
                            width: 100% !important;
                        }
                    }
                </style>
                <form method="GET" class="flex gap-4 items-end no-print">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Desde</label>
                        <input type="date" name="start_date" value="{{ $startDate }}"
                            class="mt-1 block rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Hasta</label>
                        <input type="date" name="end_date" value="{{ $endDate }}"
                            class="mt-1 block rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50">
                    </div>
                    <button type="submit"
                        class="bg-bakery-dark hover:bg-gray-800 text-white font-bold py-2 px-4 rounded shadow transition">
                        Filtrar
                    </button>
                    <button type="button" onclick="window.print()"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded shadow transition flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                            </path>
                        </svg>
                        Imprimir
                    </button>
                    <!-- Quick Presets -->
                    <a href="{{ route('reports.index', ['start_date' => now()->startOfMonth()->toDateString(), 'end_date' => now()->toDateString()]) }}"
                        class="text-sm text-gray-600 hover:text-bakery-gold underline self-center ml-2">Este Mes</a>
                </form>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Sales Trend -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-bold text-lg mb-4 text-gray-700">Ventas Diarias ($)</h3>
                    <canvas id="salesChart" height="200"></canvas>
                </div>

                <!-- Top Products -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-bold text-lg mb-4 text-gray-700">Top 5 Productos Vendidos (Unidades)</h3>
                    <div class="relative h-64">
                        <canvas id="topProductsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Alerts Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Low Stock Products -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-red-500">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-lg text-red-700">⚠️ Productos con Stock Bajo</h3>
                        <a href="{{ route('production.create') }}"
                            class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded hover:bg-red-200">Producir</a>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead>
                            <tr>
                                <th class="text-left py-2 font-medium text-gray-500">Producto</th>
                                <th class="text-right py-2 font-medium text-gray-500">Stock Actual</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($lowStockProducts as $variant)
                                <tr>
                                    <td class="py-2">{{ $variant->product->name }} ({{ $variant->name }})</td>
                                    <td class="py-2 text-right font-bold text-red-600">{{ $variant->current_stock }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="py-2 text-center text-gray-400">Todo en orden ✅</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Low Stock Supplies -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-orange-400">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-lg text-orange-700">⚠️ Insumos por Agotarse</h3>
                        <a href="{{ route('supplies.index') }}"
                            class="text-xs bg-orange-100 text-orange-700 px-2 py-1 rounded hover:bg-orange-200">Reponer</a>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead>
                            <tr>
                                <th class="text-left py-2 font-medium text-gray-500">Insumo</th>
                                <th class="text-right py-2 font-medium text-gray-500">Stock Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($lowStockSupplies as $supply)
                                <tr>
                                    <td class="py-2">{{ $supply->name }}</td>
                                    <td class="py-2 text-right font-bold text-orange-600">{{ $supply->current_stock }}
                                        {{ $supply->base_unit }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="py-2 text-center text-gray-400">Todo en orden ✅</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Data from Controller
            const salesLabels = @json($chartLabels);
            const salesValues = @json($chartValues);
            const topLabels = @json($topProductsLabels);
            const topValues = @json($topProductsValues);

            // Sales Chart
            const ctxSales = document.getElementById('salesChart').getContext('2d');
            new Chart(ctxSales, {
                type: 'bar',
                data: {
                    labels: salesLabels,
                    datasets: [{
                        label: 'Ventas ($)',
                        data: salesValues,
                        backgroundColor: '#D4AF37', // Bakery Gold
                        borderRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    animation: false, // Disable animation for print
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });

            // Top Products Chart
            const ctxTop = document.getElementById('topProductsChart').getContext('2d');
            new Chart(ctxTop, {
                type: 'doughnut',
                data: {
                    labels: topLabels,
                    datasets: [{
                        data: topValues,
                        backgroundColor: [
                            '#1F2937', // Bakery Dark
                            '#D4AF37', // Gold
                            '#F3E5AB', // Cream
                            '#9CA3AF',
                            '#E5E7EB'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false, // Disable animation for print
                    plugins: {
                        legend: { position: 'right' }
                    }
                }
            });
        </script>
    @endpush
</x-app-layout>