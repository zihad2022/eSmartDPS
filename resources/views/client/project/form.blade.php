<x-client.layout.app>
    {{-- Set the page title dynamically for tab/browser --}}
    <x-slot:title>{{ isset($project) ? 'Edit Project' : 'Add New Project' }}</x-slot:title>

    {{-- Breadcrumb navigation to help users understand their location --}}
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('client.dashboard')],
        ['label' => 'All Projects', 'url' => route('client.projects.index')],
        ['label' => isset($project) ? 'Edit Project' : 'Add New Project'],
    ]" />

    {{-- Flash messages: show success or error feedback if available --}}
    @if (session('success') || session('error'))
        <x-flash-message 
            :type="session('success') ? 'success' : 'error'" 
            :title="session('success') ? 'Success' : 'Error'" 
            :message="session('success') ?? session('error')" />
    @endif

    <div>
        {{-- Project form card --}}
        <div class="bg-white rounded-2xl shadow-sm p-6 max-w-3xl mx-auto">
            {{-- Page heading: changes dynamically for Add vs Edit --}}
            <h2 class="text-xl font-semibold text-primary-900 mb-6">
                {{ isset($project) ? 'Edit Project' : 'Add New Project' }}
            </h2>

            {{-- Form for creating or updating a project --}}
            <form method="POST"
                action="{{ isset($project) ? route('client.projects.update', $project->id) : route('client.projects.store') }}"
                class="space-y-6">

                {{-- CSRF protection --}}
                @csrf

                {{-- If editing project, spoof PUT method --}}
                @if (isset($project))
                    @method('PUT')
                @endif

                {{-- Input: Project Name + Project Category --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input 
                        name="name" 
                        label="Project Name" 
                        :value="old('name', $project->name ?? '')" 
                        required
                        placeholder="Enter project name" />

                    {{-- Dropdown: Select project category --}}
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

                {{-- Input: Investment amount + Expected return --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input 
                        name="investment_amount" 
                        label="Investment Amount" 
                        type="number" 
                        :value="old('investment_amount', $project->investment_amount ?? '')"
                        required 
                        placeholder="Enter amount" 
                        step="0.01" />

                    <x-form.input 
                        name="expected_return" 
                        label="Expected Return (%)" 
                        type="number" 
                        :value="old('expected_return', $project->expected_return ?? '')"
                        required 
                        placeholder="Enter return %" 
                        step="0.1" />
                </div>

                {{-- Input: Start date + End date --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input 
                        name="start_date" 
                        label="Start Date" 
                        type="date" 
                        :value="old('start_date', isset($project) ? $project->start_date->format('Y-m-d') : '')" 
                        required />

                    <x-form.input 
                        name="end_date" 
                        label="End Date" 
                        type="date" 
                        :value="old('end_date', isset($project) ? $project->end_date->format('Y-m-d') : '')" 
                        required />
                </div>

                {{-- Dropdown: Project status --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="status" class="block text-sm font-medium text-primary-700 mb-2">
                            Project Status
                        </label>
                        <select name="status" id="status"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-accent-500"
                            required>
                            <option value="">Select status</option>
                            @foreach (\App\Enums\ProjectStatus::cases() as $status)
                                <option value="{{ $status->value }}"
                                    {{ old('status', $project->status ?? '') == $status->value ? 'selected' : '' }}>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Input: Project description (optional) --}}
                <div>
                    <x-form.textarea 
                        name="description" 
                        label="Project Description" 
                        :value="old('description', $project->description ?? '')"
                        placeholder="Enter description" />
                </div>

                {{-- Form actions: Cancel or Submit --}}
                <div class="flex justify-end space-x-4 pt-4">
                    <a href="{{ route('client.projects.index') }}"
                        class="px-4 py-2 border border-gray-300 text-primary-700 rounded-lg hover:bg-gray-50 text-sm transition duration-300">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition duration-300">
                        {{ isset($project) ? 'Update Project' : 'Add Project' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-client.layout.app>
