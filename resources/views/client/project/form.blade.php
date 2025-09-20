<x-client.layout.app>
    @php
        /**
         * =======================================
         * Page Setup: Title and Breadcrumbs
         * =======================================
         */

        // 1. Determine if the page is for adding or editing a project
        $isEdit = isset($project);

        // 2. Set page title dynamically
        $pageTitle = $isEdit ? 'Edit Project' : 'Add New Project';

        // 3. Breadcrumb items to show navigation path
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('client.dashboard')],
            ['label' => 'All Projects', 'url' => route('client.projects.index')],
            ['label' => $pageTitle],
        ];
    @endphp

    {{-- ===========================
         Set HTML Page Title
    ============================ --}}
    <x-slot:title>{{ $pageTitle }}</x-slot:title>

    {{-- ===========================
         Breadcrumb Navigation
    ============================ --}}
    <x-breadcrumb :items="$breadcrumbItems" />

    {{-- ===========================
         Main Content Wrapper
    ============================ --}}
    <div>

        {{-- ===========================
             Flash Messages Section
        ============================ --}}
        @if (session('success') || session('error'))
            <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
        @endif

        {{-- ===========================
             Project Form Card
        ============================ --}}
        <div class="bg-white rounded-2xl shadow-sm p-6 max-w-3xl mx-auto">

            {{-- Page Heading --}}
            <h2 class="text-xl font-semibold text-primary-900 mb-6">
                {{ $pageTitle }}
            </h2>

            {{-- ===========================
                 Project Form (Add/Edit)
            ============================ --}}
            <form method="POST"
                action="{{ $isEdit ? route('client.projects.update', $project->id) : route('client.projects.store') }}"
                class="space-y-6">

                {{-- CSRF Protection --}}
                @csrf

                {{-- Spoof PUT method if editing --}}
                @if ($isEdit)
                    @method('PUT')
                @endif

                {{-- ---------------------------
                     Project Name & Category
                --------------------------- --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Project Name --}}
                    <x-form.input name="name" label="Project Name" :value="old('name', $project->name ?? '')" required
                        placeholder="Enter project name" />

                    {{-- Project Category Dropdown --}}
                    <div>
                        <label for="project_category_id" class="block text-sm font-medium text-primary-700 mb-2">
                            Project Category
                        </label>
                        <select name="project_category_id" id="project_category_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-accent-500"
                            required>
                            <option value="">Select a category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('project_category_id', $project->project_category_id ?? '') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_category_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- ---------------------------
                     Investment & Expected Return
                --------------------------- --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="investment_amount" label="Investment Amount" type="number" :value="old('investment_amount', $project->investment_amount ?? '')"
                        required placeholder="Enter amount" step="0.01" />

                    <x-form.input name="expected_return" label="Expected Return (%)" type="number" :value="old('expected_return', $project->expected_return ?? '')"
                        required placeholder="Enter return %" step="0.1" />
                </div>

                {{-- ---------------------------
                     Start & End Dates
                --------------------------- --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="start_date" label="Start Date" type="date" :value="old('start_date', $isEdit ? $project->start_date->format('Y-m-d') : '')" required />

                    <x-form.input name="end_date" label="End Date" type="date" :value="old('end_date', $isEdit ? $project->end_date->format('Y-m-d') : '')" required />
                </div>

                {{-- ---------------------------
                     Project Status Dropdown
                --------------------------- --}}
                <x-form.select name="status" label="Project Status" :options="collect(\App\Enums\ProjectStatus::cases())
                    ->mapWithKeys(fn($type) => [$type->value => $type->label()])
                    ->toArray()" :selected="old('status', $project->status?->value ?? '')" />

                {{-- ---------------------------
                     Project Description (Optional)
                --------------------------- --}}
                <div>
                    <x-form.textarea name="description" label="Project Description" :value="old('description', $project->description ?? '')"
                        placeholder="Enter description" />
                </div>

                {{-- ---------------------------
                     Form Actions: Cancel & Submit
                --------------------------- --}}
                <div class="flex justify-end space-x-4 pt-4">
                    {{-- Cancel Button --}}
                    <a href="{{ route('client.projects.index') }}"
                        class="px-4 py-2 border border-gray-300 text-primary-700 rounded-lg hover:bg-gray-50 text-sm transition duration-300">
                        Cancel
                    </a>
                    {{-- Submit Button --}}
                    <button type="submit"
                        class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition duration-300">
                        {{ $isEdit ? 'Update Project' : 'Add Project' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-client.layout.app>
