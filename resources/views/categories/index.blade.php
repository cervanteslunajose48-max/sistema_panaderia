<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-display text-3xl font-bold text-bakery-dark-deep">
                    Categorías
                </h2>
                <p class="text-sm text-gray-600 mt-1">Organización del catálogo de productos</p>
            </div>
            <x-action-button href="{{ route('categories.create') }}" variant="primary" size="lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Nueva Categoría
            </x-action-button>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Categories Grid --}}
            @if($categories->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-6">
                    @foreach($categories as $category)
                        <x-modern-card variant="glass" class="group hover:shadow-2xl transition-all duration-300 hover-lift">
                            {{-- Category Icon/Visual --}}
                            <div
                                class="h-32 bg-gradient-to-br from-bakery-gold/20 to-bakery-gold/5 rounded-xl mb-4 flex items-center justify-center relative overflow-hidden">
                                {{-- Background Pattern --}}
                                <div class="absolute inset-0 opacity-10">
                                    <svg class="w-full h-full" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                        <pattern id="grid-{{ $category->id }}" x="0" y="0" width="20" height="20"
                                            patternUnits="userSpaceOnUse">
                                            <circle cx="10" cy="10" r="2" fill="currentColor" class="text-bakery-gold" />
                                        </pattern>
                                        <rect width="100" height="100" fill="url(#grid-{{ $category->id }})" />
                                    </svg>
                                </div>

                                {{-- Icon --}}
                                <svg class="w-16 h-16 text-bakery-gold relative z-10" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                            </div>

                            {{-- Category Info --}}
                            <div class="mb-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h3
                                        class="text-lg font-bold text-bakery-dark group-hover:text-bakery-gold transition-colors">
                                        {{ $category->name }}
                                    </h3>
                                    <span class="badge badge-{{ $category->status ? 'success' : 'danger' }}">
                                        {{ $category->status ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </div>

                                @if($category->slug)
                                    <p class="text-xs text-gray-500 mb-3">
                                        /{{ $category->slug }}
                                    </p>
                                @endif

                                {{-- Stats --}}
                                <div class="p-3 bg-blue-50 rounded-lg flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                        <span class="text-sm text-gray-700 font-medium">Productos</span>
                                    </div>
                                    <span class="text-2xl font-bold text-blue-600">
                                        {{ $category->products_count }}
                                    </span>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex gap-2">
                                <a href="{{ route('categories.edit', $category) }}"
                                    class="flex-1 btn-secondary text-center text-sm">
                                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Editar
                                </a>

                                <form action="{{ route('categories.toggle', $category) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" title="{{ $category->status ? 'Desactivar' : 'Activar' }}"
                                        class="w-full btn-{{ $category->status ? 'warning' : 'success' }} text-sm">
                                        @if($category->status)
                                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @endif
                                    </button>
                                </form>
                            </div>
                        </x-modern-card>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $categories->links() }}
                </div>
            @else
                {{-- Empty State --}}
                <x-modern-card variant="glass">
                    <div class="text-center py-12">
                        <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">No hay categorías</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Comienza creando la primera categoría para organizar tus productos
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('categories.create') }}" class="btn-primary">
                                <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Crear Primera Categoría
                            </a>
                        </div>
                    </div>
                </x-modern-card>
            @endif
        </div>
    </div>
</x-app-layout>