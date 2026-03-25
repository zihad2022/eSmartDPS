<x-client.layout.app>
    @php
        /**
         * =======================================
         * Page Setup: Title, Breadcrumbs
         * =======================================
         */

        // 1. Set the page title
        $pageTitle = 'All Categories';

        // 2. Base breadcrumb items (optional if you want breadcrumbs)
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('client.dashboard')],
            ['label' => 'Project Categories', 'url' => route('client.project-categories.index')],
        ];
    @endphp

    {{-- ===========================
         Set HTML Page Title
    ============================ --}}
    <x-slot:title>{{ $pageTitle }}</x-slot:title>

    {{-- ===========================
         Breadcrumb Navigation (optional)
    ============================ --}}
    {{-- <x-breadcrumb :items="$breadcrumbItems" /> --}}

    {{-- ===========================
         Main Content Wrapper
    ============================ --}}
    <div class="grid grid-cols-1 gap-8">

        {{-- ===========================
             Flash Messages Section
        ============================ --}}
        @if (session('success') || session('error'))
            <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
        @endif

        {{-- ===========================
             Category Form Section (Add / Edit)
        ============================ --}}
        @include('client.project-category.form')

        {{-- ===========================
             Categories Table Section
        ============================ --}}
        <div class="bg-white rounded-xl shadow-sm">

            {{-- ====================================
                 Table Header (Title + Actions)
            ==================================== --}}
            <div class="p-6 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between">

                {{-- Section Title --}}
                <h3 class="text-lg font-semibold text-primary-900 mb-4 md:mb-0">{{ $pageTitle }}</h3>

                {{-- Table Actions (Search + Add New + Export) --}}
                <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">

                    {{-- Search Form --}}
                    <form method="GET" action="{{ route('client.project-categories.index') }}"
                        class="relative w-full md:w-auto">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search categories..."
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-accent-500 focus:border-accent-500 transition duration-300">
                        <button type="submit"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-accent-500 transition">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>

                    {{-- Add New Category --}}
                    <a href="{{ route('client.project-categories.index') }}"
                        class="bg-accent-500 hover:bg-accent-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                        Add New
                    </a>

                    {{-- Export Button --}}
                    <button
                        class="bg-gray-100 hover:bg-gray-200 text-primary-700 px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                        <i class="fas fa-download mr-2"></i> Export
                    </button>
                </div>
            </div>

            {{-- =============================
                 Table Body
            ============================= --}}
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    {{-- Table Headers --}}
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                #SL</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Category Name</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Slug</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>

                    {{-- Table Rows --}}
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            // 1. Initialize serial number across paginated pages
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

                                {{-- Action Buttons --}}
                                <td class="px-6 py-4 text-sm font-medium">
                                    <div class="flex space-x-2">
                                        {{-- Edit Category --}}
                                        <a href="{{ route('client.project-categories.index', ['edit' => $category->id]) }}"
                                            class="text-secondary-600 hover:text-secondary-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        {{-- Delete Category --}}
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

                                    {{-- Confirmation Modal --}}
                                </td>
                            </tr>
                        @empty
                            {{-- Empty State --}}
                            <tr>
                                <td colspan="4" class="text-center py-4 text-primary-600">No categories found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ===============================
                 Pagination & Results Info
            =============================== --}}
            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                {{-- Results Info --}}
                <div class="text-sm text-primary-600">
                    Showing {{ $categories->firstItem() ?? 0 }} to {{ $categories->lastItem() ?? 0 }} of
                    {{ $categories->total() }} results
                </div>

                {{-- Pagination Links --}}
                <x-pagination :paginator="$categories" />
            </div>
        </div> {{-- End Categories Table Section --}}
    </div> {{-- End Main Content Wrapper --}}
</x-client.layout.app>
