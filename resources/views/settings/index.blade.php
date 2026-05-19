<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Configuración del Sistema') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <h3 class="text-lg font-bold mb-6 border-b pb-2">Datos de la Empresa</h3>

                @if(session('success'))
                    <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Logo -->
                    <div class="mb-6">
                        <label class="block font-medium text-sm text-gray-700 mb-2">Logo de la Empresa</label>
                        @if(isset($settings['shop_logo']))
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $settings['shop_logo']) }}" alt="Logo Actual"
                                    class="h-20 w-auto rounded border p-1">
                            </div>
                        @endif
                        <input type="file" name="shop_logo" accept="image/*" class="block w-full text-sm text-gray-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-full file:border-0
                            file:text-sm file:font-semibold
                            file:bg-bakery-gold file:text-bakery-dark
                            hover:file:bg-yellow-500">
                    </div>

                    <!-- Shop Name -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Nombre de la Panadería</label>
                        <input type="text" name="shop_name" value="{{ $settings['shop_name'] ?? 'Mi Panadería' }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50">
                    </div>

                    <!-- Address -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Dirección</label>
                        <input type="text" name="shop_address"
                            value="{{ $settings['shop_address'] ?? 'Calle Principal #123' }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50">
                    </div>

                    <!-- Phone -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Teléfono</label>
                        <input type="text" name="shop_phone" value="{{ $settings['shop_phone'] ?? '555-0000' }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50">
                    </div>

                    <!-- Tax ID / RUC -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">RUC / NIT</label>
                        <input type="text" name="shop_tax_id" value="{{ $settings['shop_tax_id'] ?? '' }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50">
                    </div>

                    <h3 class="text-lg font-bold mb-6 mt-8 border-b pb-2">Configuración Regional</h3>

                    <!-- Currency -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Símbolo de Moneda</label>
                        <input type="text" name="currency_symbol" value="{{ $settings['currency_symbol'] ?? '$' }}"
                            placeholder="Ej: $, S/., €"
                            class="mt-1 block w-24 rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50">
                    </div>

                    <!-- Timezone -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Zona Horaria</label>
                        <select name="timezone"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50">
                            @php
                                $zones = DateTimeZone::listIdentifiers();
                                $current = $settings['timezone'] ?? config('app.timezone');
                            @endphp
                            @foreach($zones as $zone)
                                <option value="{{ $zone }}" {{ $current == $zone ? 'selected' : '' }}>{{ $zone }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit"
                            class="bg-bakery-dark text-white px-6 py-2 rounded shadow hover:bg-gray-900 transition font-bold">
                            Guardar Cambios
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>