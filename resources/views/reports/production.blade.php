<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-display text-3xl font-bold text-bakery-dark-deep">
                Reporte de Producción
            </h2>
            <div class="flex items-center gap-2 no-print">
                <a href="{{ route('reports.production.export', request()->query()) }}"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-bold text-sm flex items-center gap-1 shadow">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Excel
                </a>
                <button onclick="printReport()"
                    class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition font-bold text-sm flex items-center gap-1 shadow">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Imprimir
                </button>
                <a href="{{ route('reports.index') }}"
                    class="text-gray-600 hover:text-bakery-gold flex items-center gap-1 ml-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Filters & Actions -->
            <div id="prod-report-filters" class="no-print">
                <x-modern-card variant="glass">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">

                        {{-- Date Range --}}
                        <div>
                            <label
                                class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Desde</label>
                            <input type="date" name="start_date" value="{{ $startDate }}"
                                class="w-full rounded-lg border-gray-300 focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50 transition">
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Hasta</label>
                            <input type="date" name="end_date" value="{{ $endDate }}"
                                class="w-full rounded-lg border-gray-300 focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50 transition">
                        </div>

                        {{-- Category Filter --}}
                        <div>
                            <label
                                class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Categoría</label>
                            <select name="category_id"
                                class="w-full rounded-lg border-gray-300 focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50 transition">
                                <option value="">Todas</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Product Filter --}}
                        <div>
                            <label
                                class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Producto</label>
                            <select name="product_id"
                                class="w-full rounded-lg border-gray-300 focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50 transition">
                                <option value="">Todos</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ $productId == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Actions --}}
                        <div class="md:col-span-4 flex justify-end gap-3 mt-4">
                            <a href="{{ route('reports.production') }}"
                                class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition font-bold">
                                Limpiar
                            </a>
                            <button type="submit"
                                class="px-6 py-2 bg-bakery-gold text-white rounded-lg shadow hover:bg-yellow-600 transition font-bold">
                                Generar Reporte
                            </button>
                        </div>
                    </form>
                </x-modern-card>
            </div>

            <div id="prod-report-content">
                <!-- Chart Section -->
                <x-modern-card>
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-bakery-dark">Comparativo de Producción Diaria</h3>
                    </div>
                    <div class="h-80 relative">
                        <canvas id="productionChart"></canvas>
                    </div>
                </x-modern-card>

                <!-- Detailed Table -->
                <x-modern-card variant="elevated" class="mt-8">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-bakery-dark flex items-center gap-2">
                            <svg class="w-6 h-6 text-bakery-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Registro Detallado
                        </h3>
                        <span class="bg-bakery-cream px-3 py-1 rounded-full text-bakery-dark font-bold text-sm">
                            Total: {{ $movements->total() }} registros
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Fecha y Hora</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Producto</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Categoría</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Cantidad</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Usuario</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($movements as $movement)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $movement->created_at->format('d/m/Y H:i A') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-900">
                                                {{ $movement->productVariant?->product?->name ?? 'Producto Desconocido' }}
                                            </div>
                                            <div class="text-xs text-gray-500">{{ $movement->productVariant?->name ?? '' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <span class="px-2 py-1 bg-gray-100 rounded-full text-xs font-medium">
                                                {{ $movement->productVariant?->product?->category?->name ?? 'Sin Categoría' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="text-lg font-bold text-bakery-gold">
                                                {{ number_format($movement->quantity) }}
                                            </span>
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 flex items-center gap-2">
                                            <div
                                                class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-500">
                                                {{ substr($movement->user?->name ?? 'S', 0, 1) }}
                                            </div>
                                            {{ $movement->user?->name ?? 'Sistema' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                            No se encontraron registros de producción para los filtros seleccionados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $movements->links() }}
                    </div>
                </x-modern-card>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Initialize Chart
                const chartElement = document.getElementById('productionChart');
                if (chartElement) {
                    const ctx = chartElement.getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: @json($chartLabels),
                            datasets: [{
                                label: 'Unidades Producidas',
                                data: @json($chartValues),
                                borderColor: '#D4AF37',
                                backgroundColor: 'rgba(212, 175, 55, 0.1)',
                                borderWidth: 3,
                                pointBackgroundColor: '#fff',
                                pointBorderColor: '#D4AF37',
                                pointRadius: 5,
                                fill: true,
                                tension: 0.3
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'top' },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    padding: 12,
                                    titleFont: { size: 14, weight: 'bold' },
                                    bodyFont: { size: 13 }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: 'rgba(0, 0, 0, 0.05)' }
                                },
                                x: {
                                    grid: { display: false }
                                }
                            }
                        }
                    });
                }
            });

            function printReport() {
                // 1. Capture Chart as Image
                const originalCanvas = document.getElementById('productionChart');
                if (!originalCanvas) {
                    alert('Error: No se encontró el gráfico para imprimir.');
                    return;
                }
                const chartImgUrl = originalCanvas.toDataURL();

                // 2. Get content and overlay
                const content = document.getElementById('prod-report-content').innerHTML;
                let overlay = document.getElementById('print-overlay');

                // CRITICAL: Move overlay to document body to ensure it's not hidden by parent containers
                document.body.appendChild(overlay);

                // 3. Set content to overlay
                overlay.innerHTML = '<h1 class="text-2xl font-bold mb-4 text-center">Reporte de Producción</h1>' + content;

                // 4. Replace empty canvas in overlay with the captured image
                const overlayCanvas = overlay.querySelector('#productionChart');
                if (overlayCanvas) {
                    const img = document.createElement('img');
                    img.src = chartImgUrl;
                    img.className = 'w-full max-h-[300px] object-contain mx-auto';
                    overlayCanvas.parentNode.replaceChild(img, overlayCanvas);
                }

                // 5. Print
                window.print();

                // Optional: Reload to reset DOM state if needed, though hidden overlay is fine
                // window.location.reload();
            }
        </script>
        <style>
            @media print {
                @page {
                    size: landscape;
                    margin: 0.5cm;
                }

                /* Hide EVERYTHING in the body */
                body>* {
                    display: none !important;
                }

                /* Show ONLY the print overlay */
                body>#print-overlay {
                    display: block !important;
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: white;
                    z-index: 9999;
                }

                /* Reset Overlay Children Styles */
                #print-overlay {
                    font-family: sans-serif;
                    color: black;
                }

                #print-overlay * {
                    visibility: visible !important;
                }

                /* Chart Sizing in Print */
                #print-overlay canvas {
                    max-height: 300px !important;
                    width: 100% !important;
                }

                /* Table Styling */
                #print-overlay table {
                    width: 100% !important;
                    border-collapse: collapse;
                    margin-top: 20px;
                }

                #print-overlay th,
                #print-overlay td {
                    border: 1px solid #ccc !important;
                    padding: 8px !important;
                    color: black !important;
                    font-size: 12px !important;
                }

                #print-overlay th {
                    background-color: #f0f0f0 !important;
                }

                /* Hide cards shadows/borders in print */
                #print-overlay .shadow-sm,
                #print-overlay .rounded-lg,
                #print-overlay .shadow,
                #print-overlay .bg-white,
                #print-overlay .glass-card-white {
                    box-shadow: none !important;
                    border: none !important;
                    background: transparent !important;
                    border-radius: 0 !important;
                }
            }
        </style>
    @endpush

    <!-- Print Overlay Container -->
    <div id="print-overlay" class="hidden"></div>
</x-app-layout>