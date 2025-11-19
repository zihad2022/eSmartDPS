@props([
    'action' => '#',
    'name' => 'search',
    'value' => null,
    'placeholder' => 'Search...',
])

<form method="GET" action="{{ $action }}" class="relative w-full md:w-auto">

    <input
        type="text"
        name="{{ $name }}"
        value="{{ $value }}"
        placeholder="{{ $placeholder }}"
        class="w-full border border-gray-300 rounded-lg px-4 py-2 pr-10 text-sm
               focus:ring-2 focus:ring-accent-500 transition"
    >

    <button
        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-accent-500"
    >
        <i class="fas fa-search"></i>
    </button>

</form>
