<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nuevo Proveedor') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                {{-- Display validation errors --}}
                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        <strong class="font-bold">¡Error!</strong>
                        <ul class="mt-2 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('suppliers.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Empresa *</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50"
                                required>
                            @error('name')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Persona de Contacto</label>
                            <input type="text" name="contact_name" value="{{ old('contact_name') }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">RUC / Documento</label>
                            <input type="text" name="document_number" value="{{ old('document_number') }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                            <textarea name="address" rows="2"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50">{{ old('address') }}</textarea>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <a href="{{ route('suppliers.index') }}"
                            class="bg-gray-200 text-gray-700 py-2 px-4 rounded hover:bg-gray-300 transition">
                            Cancelar
                        </a>
                        <button type="submit"
                            class="bg-bakery-gold hover:bg-bakery-dark text-white font-bold py-2 px-4 rounded transition">
                            Guardar Proveedor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
