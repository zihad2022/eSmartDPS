<div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs md:text-sm text-primary-500 font-medium">{{ $label }}</p>
            <h3 class="text-xl md:text-3xl font-bold text-primary-900">{{ $value }}</h3>
        </div>
        <div class="w-10 h-10 {{ $bgColor }} {{ $textColor }} rounded-lg flex items-center justify-center">
            <i class="{{ $icon }} text-lg"></i>
        </div>
    </div>
</div>
