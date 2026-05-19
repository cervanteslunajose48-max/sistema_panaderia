@props(['title', 'value', 'icon', 'color' => 'gold'])

<div
    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-bakery-{{ $color }} relative group hover:shadow-md transition-shadow duration-300">
    <div class="flex justify-between items-start">
        <div>
            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">{{ $title }}</p>
            <h3 class="mt-2 text-3xl font-bold text-gray-900 group-hover:text-bakery-dark transition-colors">
                {{ $value }}</h3>
        </div>
        <div class="p-3 rounded-full bg-bakery-{{ $color }} bg-opacity-20 text-bakery-{{ $color }}-dark">
            {{ $slot }}
        </div>
    </div>
</div>