<x-client.layout.app>
    <div class="grid grid-cols-1 gap-8">

        {{-- 🔼 FORM SECTION --}}
        @include('client.project-category.form')

        {{-- 🔽 TABLE SECTION --}}
        <div class="bg-white rounded-xl shadow-sm">
            <!-- Header -->
            <div class="p-6 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between">
                <h3 class="text-lg font-semibold text-primary-900 mb-4 md:mb-0">All Categories</h3>
                <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">
                    <a href="{{ route('client.project-categories.index') }}"
                        class="bg-accent-500 hover:bg-accent-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                        Add New
                    </a>
                    <button
                        class="bg-gray-100 hover:bg-gray-200 text-primary-700 px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                        <i class="fas fa-download mr-2"></i> Export
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                sl
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Category Name
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Slug
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            // Initialize serial number starting from current pagination first item
                            $sl = $categories->firstItem() ?? 1;
                        @endphp
                        @forelse ($categories as $category)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-primary-900">#{{ $sl++ }}</td>
                                <td class="px-6 py-4 text-sm text-primary-900">{{ $category->name }}</td>
                                <td class="px-6 py-4 text-sm text-primary-600">{{ $category->slug }}</td>
                                <td class="px-6 py-4 text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('client.project-categories.index', ['edit' => $category->id]) }}"
                                            class="text-secondary-600 hover:text-secondary-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST"
                                            action="{{ route('client.project-categories.destroy', $category) }}"
                                            class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="text-red-600 hover:text-red-900 delete-btn"
                                                title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                {{-- Confirm modal component for deletion confirmation --}}
                                <x-confirm-modal />
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-primary-600">No categories found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer / Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                <div class="text-sm text-primary-600">
                    Showing {{ $categories->firstItem() ?? 0 }} to {{ $categories->lastItem() ?? 0 }} of
                    {{ $categories->total() }} results
                </div>
                <x-pagination :paginator="$categories" />
            </div>
        </div>

    </div>
</x-client.layout.app>
