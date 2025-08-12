<div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">
    <h2 class="text-xl font-semibold text-primary-900 mb-6">
        {{ isset($editRole) ? 'Edit Role' : 'Add New Role' }}
    </h2>

    <form method="POST"
        action="{{ isset($editRole) ? route('admin.roles.update', $editRole) : route('admin.roles.store') }}"
        class="space-y-6">
        @csrf
        @if (isset($editRole))
            @method('PUT')
        @endif

        {{-- Role Name --}}
        <div>
            <x-form.input name="name" label="Role Name" :value="old('name', $editRole->name ?? '')" required placeholder="Enter role name" />
        </div>

        {{-- Permissions Multi-Select --}}
        {{-- <div>
            <label class="block mb-2 font-semibold text-primary-700">Assign Permissions</label>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 max-h-60 overflow-y-auto border rounded p-4 bg-gray-50">
                @foreach ($permissions as $permission)
                    <label class="inline-flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                            {{ (isset($editRole) && $editRole->permissions->contains($permission)) || (is_array(old('permissions')) && in_array($permission->id, old('permissions'))) ? 'checked' : '' }}
                            class="form-checkbox text-accent-500" />
                        <span class="text-sm text-primary-900">{{ $permission->name }}</span>
                    </label>
                @endforeach
            </div>
            @error('permissions')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div> --}}

        {{-- <div>
            <label class="block mb-4 text-lg font-semibold text-primary-700">Assign Permissions</label>

            <div class="space-y-6">
                @foreach ($groupedPermissions as $category => $permissions)
                    <div
                        class="border border-gray-200 bg-white rounded-xl shadow-sm p-4 hover:shadow-md transition-shadow duration-300">
                        <!-- Category Title -->
                        <h3 class="text-primary-800 font-bold text-base mb-3 border-b pb-2">{{ $category }}</h3>

                        <!-- Permissions Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                            @foreach ($permissions as $permission)
                                <label
                                    class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                        {{ (isset($editRole) && $editRole->permissions->contains($permission)) ||
                                        (is_array(old('permissions')) && in_array($permission->id, old('permissions')))
                                            ? 'checked'
                                            : '' }}
                                        class="form-checkbox text-accent-500 rounded focus:ring-accent-500" />
                                    <span class="text-sm text-primary-900">{{ $permission->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            @error('permissions')
                <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div> --}}

        {{-- <div>
            <label class="block mb-4 text-lg font-semibold text-primary-700">Assign Permissions</label>

            <div class="space-y-6">
                @foreach ($groupedPermissions as $category => $permissions)
                    <div
                        class="border border-gray-200 bg-white rounded-xl shadow-sm p-4 hover:shadow-md transition-shadow duration-300">

                        <!-- Group Heading with Select All -->
                        <div class="flex items-center gap-2 mb-3 border-b pb-2">
                            <input type="checkbox"
                                class="group-checkbox form-checkbox text-accent-500 rounded focus:ring-accent-500"
                                data-group="{{ Str::slug($category, '_') }}">
                            <h3 class="text-primary-800 font-bold text-base">{{ $category }}</h3>
                        </div>

                        <!-- Permissions Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                            @foreach ($permissions as $permission)
                                <label
                                    class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                        data-group-item="{{ Str::slug($category, '_') }}"
                                        {{ (isset($editRole) && $editRole->permissions->contains($permission)) ||
                                        (is_array(old('permissions')) && in_array($permission->id, old('permissions')))
                                            ? 'checked'
                                            : '' }}
                                        class="permission-checkbox form-checkbox text-accent-500 rounded focus:ring-accent-500" />
                                    <span class="text-sm text-primary-900">{{ $permission->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            @error('permissions')
                <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Core JS -->
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // When clicking group checkbox
                document.querySelectorAll(".group-checkbox").forEach(groupCheckbox => {
                    groupCheckbox.addEventListener("change", function() {
                        const groupName = this.dataset.group;
                        const groupItems = document.querySelectorAll(
                            `[data-group-item="${groupName}"]`);
                        groupItems.forEach(item => {
                            item.checked = this.checked;
                        });
                    });
                });

                // When clicking individual permission checkbox
                document.querySelectorAll(".permission-checkbox").forEach(itemCheckbox => {
                    itemCheckbox.addEventListener("change", function() {
                        const groupName = this.dataset.groupItem;
                        const groupItems = document.querySelectorAll(
                            `[data-group-item="${groupName}"]`);
                        const groupCheckbox = document.querySelector(
                            `.group-checkbox[data-group="${groupName}"]`);

                        const allChecked = [...groupItems].every(item => item.checked);
                        const someChecked = [...groupItems].some(item => item.checked);

                        // Handle indeterminate state
                        groupCheckbox.checked = allChecked;
                        groupCheckbox.indeterminate = !allChecked && someChecked;
                    });
                });

                // Initialize group checkboxes on page load
                document.querySelectorAll(".group-checkbox").forEach(groupCheckbox => {
                    const groupName = groupCheckbox.dataset.group;
                    const groupItems = document.querySelectorAll(`[data-group-item="${groupName}"]`);
                    const allChecked = [...groupItems].every(item => item.checked);
                    const someChecked = [...groupItems].some(item => item.checked);

                    groupCheckbox.checked = allChecked;
                    groupCheckbox.indeterminate = !allChecked && someChecked;
                });
            });
        </script> --}}

        <div>
            <label class="block mb-6 text-lg font-bold text-gray-800">Assign Permissions</label>

            <div class="space-y-8">
                @foreach ($groupedPermissions as $category => $permissions)
                    <div class="rounded-2xl shadow-sm bg-white border">

                        <!-- Group Heading -->
                        <div
                            class="flex items-center justify-between px-5 py-3 border-b border-gray-100 bg-gray-50 rounded-t-2xl">
                            <label class="flex items-center gap-3 cursor-pointer select-none">
                                <input type="checkbox"
                                    class="group-checkbox w-5 h-5 text-accent-500 rounded focus:ring-accent-500 cursor-pointer"
                                    data-group="{{ Str::slug($category, '_') }}">
                                <h3 class="text-gray-900 font-semibold text-lg">{{ $category }}</h3>
                            </label>
                        </div>

                        <!-- Permissions -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 p-5">
                            @foreach ($permissions as $permission)
                                <label
                                    class="flex items-center gap-3 px-3 py-2 bg-gray-50  border border-gray-200 rounded-lg cursor-pointer transition-colors">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                        data-group-item="{{ Str::slug($category, '_') }}"
                                        {{ (isset($editRole) && $editRole->permissions->contains($permission)) ||
                                        (is_array(old('permissions')) && in_array($permission->id, old('permissions')))
                                            ? 'checked'
                                            : '' }}
                                        class="permission-checkbox w-4 h-4 text-accent-500 rounded focus:ring-accent-500 cursor-pointer" />
                                    <span
                                        class="text-sm font-medium text-gray-800">{{ ucfirst($permission->name) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            @error('permissions')
                <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Core JS -->
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Group checkbox logic
                document.querySelectorAll(".group-checkbox").forEach(groupCheckbox => {
                    groupCheckbox.addEventListener("change", function() {
                        const groupName = this.dataset.group;
                        document.querySelectorAll(`[data-group-item="${groupName}"]`)
                            .forEach(item => item.checked = this.checked);
                    });
                });

                // Individual checkbox updates group state
                document.querySelectorAll(".permission-checkbox").forEach(itemCheckbox => {
                    itemCheckbox.addEventListener("change", function() {
                        const groupName = this.dataset.groupItem;
                        const groupItems = document.querySelectorAll(
                            `[data-group-item="${groupName}"]`);
                        const groupCheckbox = document.querySelector(
                            `.group-checkbox[data-group="${groupName}"]`);

                        const allChecked = [...groupItems].every(item => item.checked);
                        const someChecked = [...groupItems].some(item => item.checked);

                        groupCheckbox.checked = allChecked;
                        groupCheckbox.indeterminate = !allChecked && someChecked;
                    });
                });

                // Initialize states on page load
                document.querySelectorAll(".group-checkbox").forEach(groupCheckbox => {
                    const groupName = groupCheckbox.dataset.group;
                    const groupItems = document.querySelectorAll(`[data-group-item="${groupName}"]`);
                    const allChecked = [...groupItems].every(item => item.checked);
                    const someChecked = [...groupItems].some(item => item.checked);

                    groupCheckbox.checked = allChecked;
                    groupCheckbox.indeterminate = !allChecked && someChecked;
                });
            });
        </script>



        {{-- Buttons --}}
        <div class="flex justify-end space-x-4 pt-4">
            @if (isset($editRole))
                <a href="{{ route('admin.roles.index') }}"
                    class="px-4 py-2 border border-gray-300 text-primary-700 rounded-lg hover:bg-gray-50 text-sm transition duration-300">
                    Cancel
                </a>
            @endif
            <button type="submit"
                class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition duration-300">
                {{ isset($editRole) ? 'Update Role' : 'Add Role' }}
            </button>
        </div>
    </form>
</div>
