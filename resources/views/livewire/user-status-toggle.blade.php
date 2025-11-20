<div x-data="{ active: @js($admin->status) }">
    @if ($admin->id !== 1)
        <button :class="active ? 'bg-green-500' : 'bg-red-500'"
            class="relative inline-flex h-6 w-12 items-center rounded-full transition-colors focus:outline-none"
            wire:click="toggleStatus" @click="active = !active">
            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                :class="active ? 'translate-x-6' : 'translate-x-1'"></span>
        </button>
    @endif
</div>
