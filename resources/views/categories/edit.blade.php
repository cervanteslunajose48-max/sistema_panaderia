<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Categoría') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('categories.update', $category) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
                            <input type="text" name="name" id="name" value="{{ $category->name }}"
                                class="mt-1 focus:ring-bakery-gold focus:border-bakery-gold block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                required>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700">Descripción
                                (Opcional)</label>
                            <textarea name="description" id="description" rows="3"
                                class="mt-1 focus:ring-bakery-gold focus:border-bakery-gold block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ $category->description ?? '' }}</textarea>
                        </div>

                        <div class="flex justify-end">
                            <a href="{{ route('categories.index') }}"
                                class="bg-gray-200 text-gray-700 py-2 px-4 rounded mr-2 hover:bg-gray-300">Cancelar</a>
                            <button type="submit"
                                class="bg-bakery-gold hover:bg-bakery-dark text-white font-bold py-2 px-4 rounded">Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>