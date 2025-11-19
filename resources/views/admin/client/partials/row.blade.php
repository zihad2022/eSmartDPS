{{-- resources/views/admin/client/partials/row.blade.php --}}
@php($client = $row)

<td class="px-6 py-4 text-sm text-primary-900 font-mono">{{ $client->user_id }}</td>
<td class="px-6 py-4 whitespace-nowrap flex items-center">
    <img src="{{ $client->profile_photo ? $client->profile_photo_url : 'https://ui-avatars.com/api/?name=' . urlencode($client->first_name . ' ' . $client->last_name) }}"
        alt="Profile Photo" class="w-10 h-10 rounded-full mr-3">
    <span class="text-sm font-medium text-primary-900">{{ $client->first_name }} {{ $client->last_name }}</span>
</td>
<td class="px-6 py-4 text-sm text-primary-600">{{ $client->email }}</td>
<td class="px-6 py-4 text-sm text-primary-600">{{ $client->phone ?? 'N/A' }}</td>
<td class="px-6 py-4 text-sm text-primary-900 font-semibold">{{ ucfirst($client->role) }}</td>
<td class="px-6 py-4">
    <span
        class="px-2 py-1 text-xs font-medium rounded-full {{ $client->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
        {{ $client->status ? 'Active' : 'Inactive' }}
    </span>
</td>
<td class="px-6 py-4 text-sm text-primary-600">
    <span class="px-2 py-1 text-xs font-medium rounded-full text-green-600 bg-green-100">
        {{ $client->created_at->format('M d, Y') }}
    </span>
</td>
<td class="px-6 py-4 text-sm font-medium">
    <div class="flex space-x-2">
        <a href="{{ route('admin.clients.show', $client->id) }}" class="text-accent-600 hover:text-accent-900"
            title="View">
            <i class="fas fa-eye"></i>
        </a>
        <a href="{{ route('admin.clients.edit', $client->id) }}" class="text-secondary-600 hover:text-secondary-900"
            title="Edit">
            <i class="fas fa-edit"></i>
        </a>
        <form method="POST" action="{{ route('admin.clients.destroy', $client->id) }}" class="delete-form">
            @csrf
            @method('DELETE')
            <button type="button" class="text-red-600 hover:text-red-900 delete-btn" title="Delete">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    </div>
    <x-confirm-modal />
</td>
