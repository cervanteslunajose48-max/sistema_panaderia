@php
    // Calculate trends for stat cards
    $yesterday = \Carbon\Carbon::yesterday();
    $yesterdaySales = \App\Models\Order::whereDate('created_at', $yesterday)
        ->where('status', 'completed')
        ->sum('total');
    
    $salesTrend = $yesterdaySales > 0 ? (($dailySales - $yesterdaySales) / $yesterdaySales) * 100 : 0;
    $salesTrendDirection = $salesTrend >= 0 ? 'up' : 'down';
    
    // Calculate completion percentages for circular indicators
    $totalProducts = \App\Models\Product::count();
    $activeProducts = \App\Models\Product::where('status', 'active')->count();
    $activePercentage = $totalProducts > 0 ? round(($activeProducts / $totalProducts) * 100) : 0;
    
    $totalOrders = \App\Models\Order::count();
    $completedOrders = \App\Models\Order::where('status', 'completed')->count();
    $completionRate = $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100) : 0;
    
    $stockPercentage = 100 - min(100, ($lowStockCount / max(1, $totalProducts)) * 100);
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-display text-3xl font-bold text-bakery-dark-deep">
                    Panel de Control
                </h2>
                <p class="text-sm text-gray-600 mt-1">Resumen general de tu negocio</p>
            </div>
            <button class="btn-secondary" onclick="refreshDashboard()">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Actualizar
            </button>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Top Row: Metric Cards & Progress Indicators --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8">
                
                {{-- Left Side: Large Sales Chart --}}
                <div class="lg:col-span-5">
                    <x-modern-card variant="glass">
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-bakery-dark">Ventas Mensuales</h3>
                                <p class="text-sm text-gray-500">Últimos 7 días</p>
                            </div>
                            <button class="px-3 py-1 bg-bakery-gold text-white rounded-full text-xs font-semibold hover:bg-bakery-secondary transition-colors">
                                Este Mes ▼
                            </button>
                        </div>
                        <div class="relative h-64">
                            <canvas id="salesBarChart"></canvas>
                        </div>
                    </x-modern-card>
                </div>
                
                {{-- Middle: Metric Cards --}}
                <div class="lg:col-span-4 space-y-4">
                    <x-metric-card 
                        label="Ventas del Día"
                        :value="($globalSettings['currency_symbol'] ?? '$') . ' ' . number_format($dailySales, 2)"
                        color="success"
                    />
                    <x-metric-card 
                        label="Pedidos Totales"
                        :value="($globalSettings['currency_symbol'] ?? '$') . ' ' . number_format(\App\Models\Order::sum('total'), 2)"
                        color="info"
                    />
                    
                    {{-- Action Badges --}}
                    <div class="flex gap-2">
                        <a href="{{ route('pos.index') }}" class="flex-1 px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl text-sm font-semibold text-center hover:shadow-lg transition-all">
                            Nueva Venta
                        </a>
                        <a href="{{ route('production.create') }}" class="flex-1 px-4 py-2 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl text-sm font-semibold text-center hover:shadow-lg transition-all">
                            Producir
                        </a>
                    </div>
                </div>
                
                {{-- Right: Circular Progress Indicators --}}
                <div class="lg:col-span-3 grid grid-cols-3 lg:grid-cols-1 gap-4">
                    <x-circular-progress 
                        :percentage="$activePercentage"
                        color="success"
                        :label="'Productos Activos'"
                        size="md"
                    />
                    <x-circular-progress 
                        :percentage="$completionRate"
                        color="info"
                        :label="'Tasa de Completado'"
                        size="md"
                    />
                    <x-circular-progress 
                        :percentage="round($stockPercentage)"
                        color="warning"
                        :label="'Stock Saludable'"
                        size="md"
                    />
                </div>
            </div>

            {{-- Middle Row: Multi-Line Chart & Donut Chart --}}
            <div class="grid grid-cols-1 lg:grid-cols-8 gap-6 mb-8">
                
                {{-- Large Area Chart --}}
                <div class="lg:col-span-5">
                    <x-modern-card variant="glass">
                        <div class="mb-4">
                            <h3 class="text-lg font-bold text-bakery-dark mb-1">Tendencia de Ventas & Órdenes</h3>
                            <p class="text-xs text-gray-500">Comparativa últimos 7 días</p>
                        </div>
                        <div class="relative h-80">
                            <canvas id="multiLineChart"></canvas>
                        </div>
                    </x-modern-card>
                </div>
                
                {{-- Right: Checklist & Actions --}}
                <div class="lg:col-span-3 space-y-4">
                    {{-- Quick Tasks --}}
                    <x-modern-card variant="bordered">
                        <h3 class="font-bold text-bakery-dark mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-bakery-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Tareas Pendientes
                        </h3>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-lg transition-colors">
                                <div class="w-3 h-3 rounded-full bg-green-500 flex-shrink-0"></div>
                                <span class="text-sm text-gray-700 flex-1">Revisar inventario bajo</span>
                                <input type="checkbox" class="rounded border-gray-300 text-bakery-gold focus:ring-bakery-gold">
                            </div>
                            <div class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-lg transition-colors">
                                <div class="w-3 h-3 rounded-full bg-green-500 flex-shrink-0"></div>
                                <span class="text-sm text-gray-700 flex-1">Actualizar precios</span>
                                <input type="checkbox" class="rounded border-gray-300 text-bakery-gold focus:ring-bakery-gold">
                            </div>
                            <div class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-lg transition-colors">
                                <div class="w-3 h-3 rounded-full bg-purple-500 flex-shrink-0"></div>
                                <span class="text-sm text-gray-700 flex-1">Preparar producción</span>
                                <input type="checkbox" class="rounded border-gray-300 text-bakery-gold focus:ring-bakery-gold">
                            </div>
                        </div>
                    </x-modern-card>

                    {{-- Progress Bars --}}
                    <x-modern-card variant="bordered">
                        <h3 class="font-bold text-bakery-dark mb-4">Metas del Mes</h3>
                        <div class="space-y-4">
                            {{-- Sales Goal --}}
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-600">Meta de Ventas</span>
                                    <span class="text-sm font-bold text-gray-800">64%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="bg-gradient-to-r from-yellow-400 to-yellow-500 h-3 rounded-full shadow-md" style="width: 64%"></div>
                                </div>
                            </div>
                            
                            {{-- Production Goal --}}
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-600">Meta de Producción</span>
                                    <span class="text-sm font-bold text-gray-800">83%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="bg-gradient-to-r from-yellow-400 to-yellow-500 h-3 rounded-full shadow-md" style="width: 83%"></div>
                                </div>
                            </div>
                        </div>
                    </x-modern-card>
                </div>
            </div>

            {{-- Bottom Row: Small Charts & Stats --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                {{-- Donut Chart (Categories) --}}
                <div class="lg:col-span-3">
                    <x-modern-card variant="glass">
                        <div class="mb-4">
                            <h3 class="font-bold text-bakery-dark">Ventas por Categoría</h3>
                            <p class="text-xs text-gray-500 mt-1">Top 5 categorías</p>
                        </div>
                        <div class="relative h-56">
                            <canvas id="categoryDonutChart"></canvas>
                        </div>
                        <div class="mt-4 space-y-2">
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-600">Ipsum dolor</span>
                                <span class="font-semibold">34%</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-600">Dolor sit</span>
                                <span class="font-semibold">36%</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-600">Amet lorem</span>
                                <span class="font-semibold">12%</span>
                            </div>
                        </div>
                    </x-modern-card>
                </div>

                {{-- Recent Activity --}}
                <div class="lg:col-span-5">
                    <x-modern-card variant="bordered">
                        <h3 class="font-bold text-bakery-dark mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-bakery-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Actividad Reciente
                        </h3>
                        <div class="space-y-3">
                            @forelse($recentOrders->take(4) as $order)
                                <div class="p-3 bg-gradient-to-r from-bakery-cream to-bakery-cream/50 rounded-lg hover:shadow-md transition-shadow border border-bakery-gold/20">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-sm font-semibold text-bakery-dark">
                                                {{ $order->customer->name ?? 'Cliente Anónimo' }}
                                            </p>
                                            <p class="text-xs text-gray-600">
                                                Orden #{{ $order->id }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-bold text-green-600">
                                                {{ $globalSettings['currency_symbol'] ?? '$' }}{{ number_format($order->total, 2) }}
                                            </p>
                                            <span class="badge badge-{{ $order->status === 'completed' ? 'success' : 'warning' }} text-xs">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $order->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            @empty
                                <div class="text-center py-8 text-gray-500">
                                    <p class="text-sm">No hay órdenes recientes</p>
                                </div>
                            @endforelse
                        </div>
                    </x-modern-card>
                </div>

                {{-- Pink Bar Chart (Week Sales) --}}
                <div class="lg:col-span-4">
                    <x-modern-card variant="glass">
                        <div class="mb-4">
                            <h3 class="font-bold text-bakery-dark">Ventas Semanales</h3>
                            <p class="text-xs text-gray-500 mt-1">Por día de la semana</p>
                        </div>
                        <div class="relative h-64">
                            <canvas id="weekBarChart"></canvas>
                        </div>
                    </x-modern-card>
                </div>
            </div>

        </div>
    </div>

    {{-- Chart.js Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const currencySymbol = "{{ $globalSettings['currency_symbol'] ?? '$' }}";
            const colors = {
                pink: '#ec4899',
                pinkLight: '#f9a8d4',
                yellow: '#fbbf24',
                green: '#10b981',
                greenLight: '#6ee7b7',
                purple: '#a855f7',
                blue: '#3b82f6',
                bakeryPrimary: '#D4965A',
                bakerySecondary: '#E9C46A'
            };

            // 1. Sales Bar Chart (Pink bars like in reference image)
            new Chart(document.getElementById('salesBarChart'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($salesLabels) !!},
                    datasets: [{
                        label: 'Ventas',
                        data: {!! json_encode($salesData) !!},
                        backgroundColor: colors.pink,
                        borderRadius: 8,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return currencySymbol + ' ' + context.parsed.y.toFixed(2);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { display: false },
                            ticks: {
                                callback: function(value) {
                                    return currencySymbol + value;
                                }
                            }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });

            // 2. Multi-line Area Chart (Yellow & Green like in reference)
            new Chart(document.getElementById('multiLineChart'), {
                type: 'line',
                data: {
                    labels: {!! json_encode($salesLabels) !!},
                    datasets: [
                        {
                            label: 'Ventas',
                            data: {!! json_encode($salesData) !!},
                            borderColor: colors.yellow,
                            backgroundColor: 'rgba(251, 191, 36, 0.2)',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 0,
                            borderWidth: 3
                        },
                        {
                            label: 'Órdenes',
                            data: [{{ $totalOrders * 0.2 }}, {{ $totalOrders * 0.25 }}, {{ $totalOrders * 0.22 }}, {{ $totalOrders * 0.28 }}, {{ $totalOrders * 0.30 }}, {{ $totalOrders * 0.26 }}, {{ $totalOrders * 0.32 }}],
                            borderColor: colors.green,
                            backgroundColor: 'rgba(16, 185, 129, 0.2)',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 0,
                            borderWidth: 3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            align: 'end',
                            labels: {
                                usePointStyle: true,
                                padding: 15,
                                font: { size: 12 }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });

            // 3. Category Donut Chart (Purple like in reference)
            new Chart(document.getElementById('categoryDonutChart'), {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($categoryLabels) !!},
                    datasets: [{
                        data: {!! json_encode($categoryData) !!},
                        backgroundColor: [
                            colors.purple,
                            colors.pink,
                            colors.blue,
                            colors.bakeryPrimary,
                            colors.green
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: { display: false }
                    }
                }
            });

            // 4. Week Bar Chart (Pink bars)
            new Chart(document.getElementById('weekBarChart'), {
                type: 'bar',
                data: {
                    labels: ['L', 'M', 'M', 'J', 'V', 'S', 'D'],
                    datasets: [{
                        label: 'Ventas',
                        data: {!! json_encode($weekSalesData) !!},
                        backgroundColor: colors.pink,
                        borderRadius: 8,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { display: false },
                            ticks: {
                                callback: function(value) {
                                    return currencySymbol + value;
                                }
                            }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        });

        function refreshDashboard() {
            location.reload();
        }
    </script>
</x-app-layout>