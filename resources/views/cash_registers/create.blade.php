<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Apertura de Caja') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8 text-center">

                <div class="mb-6 flex justify-center text-bakery-gold">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                </div>

                <h3 class="text-2xl font-bold text-gray-800 mb-2">¡Hola, {{ Auth::user()->name }}!</h3>
                <p class="text-gray-600 mb-6">Para comenzar a vender, por favor ingrese el monto inicial en caja.</p>

                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative text-left">
                        <strong class="font-bold">¡Error!</strong>
                        <ul class="list-disc mt-1 ml-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('cash-registers.store') }}" method="POST">
                    @csrf

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Monto de Apertura</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span
                                    class="text-gray-500 sm:text-sm">{{ $globalSettings['currency_symbol'] ?? '$' }}</span>
                            </div>
                            <input type="number" name="opening_amount" step="0.01"
                                class="focus:ring-bakery-gold focus:border-bakery-gold block w-full pl-7 pr-12 sm:text-lg border-gray-300 rounded-md py-3 font-bold text-center"
                                placeholder="0.00" required>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-bakery-gold text-bakery-dark font-bold text-lg py-3 rounded-lg shadow hover:bg-yellow-500 transition">
                        ABRIR CAJA
                    </button>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>