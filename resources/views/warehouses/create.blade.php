<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-display text-3xl font-bold text-bakery-dark-deep">
                Nuevo Almacén
            </h2>
            <p class="text-sm text-gray-600 mt-1">Crear una nueva ubicación de almacenamiento</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-modern-card variant="glass">
                <form method="POST" action="{{ route('warehouses.store') }}" class="space-y-6">
                    @csrf

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre del Almacén <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" required value="{{ old('name') }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold/50 transition"
                            placeholder="Ej: Almacén Principal, Bodega 2">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Location --}}
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                            Ubicación
                        </label>
                        <input type="text" name="location" id="location" value="{{ old('location') }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold/50 transition"
                            placeholder="Ej: Calle 123, Piso 1, Sector A">
                        @error('location')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                        <a href="{{ route('warehouses.index') }}" class="btn-secondary">
                            Cancelar
                        </a>
                        <button type="submit" class="btn-primary">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Guardar Almacén
                        </button>
                    </div>
                </form>
            </x-modern-card>
        </div>
    </div>
</x-app-layout>