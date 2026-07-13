<div>
    @php($actor = auth('admin')->user())

    @if ($actor && $actor->can('edit users') && ! $actor->is($admin) && ! $admin->hasRole('super-admin'))
        <button type="button"
            wire:click="toggleStatus"
            wire:loading.attr="disabled"
            class="relative inline-flex h-6 w-12 items-center rounded-full transition-colors focus:outline-none disabled:opacity-60 {{ $admin->status ? 'bg-green-500' : 'bg-red-500' }}"
            data-confirm="Are you sure you want to change this user's status?"
            data-confirm-title="Update Status"
            data-confirm-type="info"
            aria-label="Toggle {{ $admin->name }} status">
            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $admin->status ? 'translate-x-6' : 'translate-x-1' }}"></span>
        </button>
    @else
        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $admin->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
            {{ $admin->status ? 'Active' : 'Inactive' }}
        </span>
    @endif

    @error('status')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>
