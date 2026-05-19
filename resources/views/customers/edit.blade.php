<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Cliente') }}: {{ $customer->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form action="{{ route('customers.update', $customer) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Nombre Completo *</label>
                        <input type="text" name="name" value="{{ $customer->name }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50"
                            required>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Email</label>
                        <input type="email" name="email" value="{{ $customer->email }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50">
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Teléfono</label>
                        <input type="text" name="phone" value="{{ $customer->phone }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50">
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Dirección</label>
                        <textarea name="address" rows="2"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50">{{ $customer->address }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Cumpleaños</label>
                            <input type="date" name="birthday"
                                value="{{ $customer->birthday ? $customer->birthday->format('Y-m-d') : '' }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Notas / Preferencias (CRM)</label>
                        <textarea name="notes" rows="3"
                            placeholder="Ej: Alérgico a las nueces, prefiere facturas quemadas..."
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50">{{ $customer->notes }}</textarea>
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('customers.index') }}"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded shadow hover:bg-gray-300 transition">Cancelar</a>
                        <button type="submit"
                            class="px-4 py-2 bg-bakery-gold text-white rounded shadow hover:bg-bakery-dark transition">Actualizar
                            Cliente</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>