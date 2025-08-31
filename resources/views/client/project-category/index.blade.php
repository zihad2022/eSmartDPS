<x-client.layout.app>
    {{-- Set the page title for the browser/tab --}}
    <x-slot:title>All Categories</x-slot:title>

    {{-- Show flash messages (Success or Error) if available --}}
    @if (session('success') || session('error'))
        <x-flash-message 
            :type="session('success') ? 'success' : 'error'" 
            :title="session('success') ? 'Success' : 'Error'" 
            :message="session('success') ?? session('error')" 
        />
    @endif

    <div class="grid grid-cols-1 gap-8">

        {{-- Category Form Section (handles both Add and Edit) --}}
        @include('client.project-category.form')

        {{-- Categories Table Section --}}
        <div class="bg-white rounded-xl shadow-sm">

            {{-- Table Header with Title and Action Buttons --}}
            <div class="p-6 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between">
                <h3 class="text-lg font-semibold text-primary-900 mb-4 md:mb-0">All Categories</h3>

                {{-- Action Buttons: Add New + Export --}}
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

            {{-- Table Body --}}
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    {{-- Table Headings --}}
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">SL</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Category Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Slug</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>

                    {{-- Table Rows --}}
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            // Initialize serial number (keeps track across paginated pages)
                            $sl = $categories->firstItem() ?? 1;
                        @endphp

                        @forelse ($categories as $category)
                            <tr class="hover:bg-gray-50">
                                {{-- Serial Number --}}
                                <td class="px-6 py-4 text-sm text-primary-900">#{{ $sl++ }}</td>

                                {{-- Category Name --}}
                                <td class="px-6 py-4 text-sm text-primary-900">{{ $category->name }}</td>

                                {{-- Slug --}}
                                <td class="px-6 py-4 text-sm text-primary-600">{{ $category->slug }}</td>

                                {{-- Action Buttons (Edit + Delete) --}}
                                <td class="px-6 py-4 text-sm font-medium">
                                    <div class="flex space-x-2">
                                        {{-- Edit Category --}}
                                        <a href="{{ route('client.project-categories.index', ['edit' => $category->id]) }}"
                                            class="text-secondary-600 hover:text-secondary-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        {{-- Delete Category (with confirmation modal) --}}
                                        <form method="POST" action="{{ route('client.project-categories.destroy', $category) }}" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="text-red-600 hover:text-red-900 delete-btn" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>

                                    {{-- Confirmation Modal Component --}}
                                    <x-confirm-modal />
                                </td>
                            </tr>
                        @empty
                            {{-- If no categories exist --}}
                            <tr>
                                <td colspan="4" class="text-center py-4 text-primary-600">No categories found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Table Footer with Pagination --}}
            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                <div class="text-sm text-primary-600">
                    Showing {{ $categories->firstItem() ?? 0 }} to {{ $categories->lastItem() ?? 0 }} of {{ $categories->total() }} results
                </div>
                <x-pagination :paginator="$categories" />
            </div>
        </div>

    </div>
</x-client.layout.app>
