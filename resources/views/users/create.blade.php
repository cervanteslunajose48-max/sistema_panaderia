<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registrar Nuevo Usuario') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('users.store') }}">
                        @csrf

                        <!-- Name -->
                        <div>
                            <label class="block font-medium text-sm text-gray-700" for="name">Nombre</label>
                            <input id="name"
                                class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50"
                                type="text" name="name" required autofocus />
                        </div>

                        <!-- Email Address -->
                        <div class="mt-4">
                            <label class="block font-medium text-sm text-gray-700" for="email">Email</label>
                            <input id="email"
                                class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50"
                                type="email" name="email" required />
                        </div>

                        <!-- Role -->
                        <div class="mt-4">
                            <label class="block font-medium text-sm text-gray-700" for="role">Rol / Cargo</label>
                            <select id="role" name="role"
                                class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50">
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Password -->
                        <div class="mt-4">
                            <label class="block font-medium text-sm text-gray-700" for="password">Contraseña</label>
                            <input id="password"
                                class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50"
                                type="password" name="password" required autocomplete="new-password" />
                        </div>

                        <!-- Confirm Password -->
                        <div class="mt-4">
                            <label class="block font-medium text-sm text-gray-700" for="password_confirmation">Confirmar
                                Contraseña</label>
                            <input id="password_confirmation"
                                class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-bakery-gold focus:ring focus:ring-bakery-gold focus:ring-opacity-50"
                                type="password" name="password_confirmation" required />
                        </div>

                        <div class="flex items-center justify-end gap-2 mt-4">
                            <a href="{{ route('users.index') }}"
                                class="px-4 py-2 bg-gray-200 text-gray-800 rounded shadow hover:bg-gray-300 transition">
                                Cancelar
                            </a>
                            <button type="submit"
                                class="px-4 py-2 bg-bakery-gold text-white rounded shadow hover:bg-bakery-dark transition">
                                Registrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>