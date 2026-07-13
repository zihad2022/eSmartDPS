<div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">
    <h2 class="text-xl font-semibold text-primary-900 mb-6">
        {{ isset($editRole) ? 'Edit Role' : 'Add New Role' }}
    </h2>

    <form method="POST"
        action="{{ isset($editRole) ? route('admin.roles.update', $editRole) : route('admin.roles.store') }}"
        class="space-y-6" data-role-form>
        @csrf
        @isset($editRole)
            @method('PUT')
        @endisset

        <x-form.input name="name" label="Role Name" :value="old('name', $editRole->name ?? '')" required
            placeholder="Enter role name" />

        <div>
            <label class="block mb-4 text-lg font-bold text-gray-800">Assign Permissions</label>

            <div class="space-y-6">
                @foreach ($groupedPermissions as $category => $permissions)
                    @php($group = \Illuminate\Support\Str::slug($category, '_'))
                    <section class="rounded-2xl shadow-sm bg-white border border-gray-200">
                        <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 bg-gray-50 rounded-t-2xl">
                            <label class="flex items-center gap-3 cursor-pointer select-none">
                                <input type="checkbox"
                                    class="group-checkbox w-5 h-5 text-accent-500 rounded focus:ring-accent-500 cursor-pointer"
                                    data-group="{{ $group }}">
                                <span class="text-gray-900 font-semibold text-lg">{{ $category }}</span>
                            </label>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 p-5">
                            @foreach ($permissions as $permission)
                                <label class="flex items-center gap-3 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg cursor-pointer transition-colors hover:bg-gray-100">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                        data-group-item="{{ $group }}"
                                        @checked(
                                            (isset($editRole) && $editRole->permissions->contains('id', $permission->id)) ||
                                            in_array($permission->id, old('permissions', []))
                                        )
                                        class="permission-checkbox w-4 h-4 text-accent-500 rounded focus:ring-accent-500 cursor-pointer">
                                    <span class="text-sm font-medium text-gray-800">{{ ucfirst($permission->name) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>

            @error('permissions')
                <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
            @enderror
            @error('permissions.*')
                <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end space-x-4 pt-4">
            @isset($editRole)
                <a href="{{ route('admin.roles.index') }}"
                    class="px-4 py-2 border border-gray-300 text-primary-700 rounded-lg hover:bg-gray-50 text-sm transition duration-300">
                    Cancel
                </a>
            @endisset
            <button type="submit"
                class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition duration-300">
                {{ isset($editRole) ? 'Update Role' : 'Add Role' }}
            </button>
        </div>
    </form>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const form = document.querySelector('[data-role-form]');
                if (!form) return;

                const updateGroupState = (groupName) => {
                    const groupItems = [...form.querySelectorAll(`[data-group-item="${groupName}"]`)];
                    const groupCheckbox = form.querySelector(`.group-checkbox[data-group="${groupName}"]`);
                    if (!groupCheckbox || groupItems.length === 0) return;

                    const checkedCount = groupItems.filter((item) => item.checked).length;
                    groupCheckbox.checked = checkedCount === groupItems.length;
                    groupCheckbox.indeterminate = checkedCount > 0 && checkedCount < groupItems.length;
                };

                form.querySelectorAll('.group-checkbox').forEach((groupCheckbox) => {
                    groupCheckbox.addEventListener('change', () => {
                        form.querySelectorAll(`[data-group-item="${groupCheckbox.dataset.group}"]`)
                            .forEach((item) => item.checked = groupCheckbox.checked);
                    });
                    updateGroupState(groupCheckbox.dataset.group);
                });

                form.querySelectorAll('.permission-checkbox').forEach((permissionCheckbox) => {
                    permissionCheckbox.addEventListener('change', () => {
                        updateGroupState(permissionCheckbox.dataset.groupItem);
                    });
                });
            });
        </script>
    @endpush
@endonce
