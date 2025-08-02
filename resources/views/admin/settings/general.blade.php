<x-admin.settings.layout>
    @if (session('success') || session('error'))
        <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
    @endif
    <div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">
        <h2 class="text-xl font-semibold text-primary-900 mb-6">General Settings</h2>

        <form method="POST" action="{{ route('admin.settings.general.update') }}" enctype="multipart/form-data"
            class="space-y-8">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-form.input name="site_name" label="Site Name" :value="old('site_name', $settings->site_name ?? '')" placeholder="Enter website name" />

                <x-form.input name="site_slogan" label="Site Slogan" :value="old('site_slogan', $settings->site_slogan ?? '')"
                    placeholder="Enter site slogan" />
            </div>

            <x-form.textarea name="site_description" label="Site Description" :value="old('site_description', $settings->site_description ?? '')"
                placeholder="Enter short description" rows="3" />

            <x-form.textarea name="site_keywords" label="Site Keywords" :value="old('site_keywords', $settings->site_keywords ?? '')"
                placeholder="Enter SEO keywords (comma separated)" rows="2" />

            <x-form.textarea name="meta_codes" label="Meta Codes" :value="old('meta_codes', $settings->meta_codes ?? '')"
                placeholder="Paste analytics or tracking codes here" rows="4" />

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <x-form.input name="site_logo" label="Site Logo" type="file" :editing="true" :previewUrl="$settings->site_logo_url ?? null" />

                <x-form.input name="favicon" label="Favicon" type="file" :editing="true" :previewUrl="$settings->favicon_url ?? null" />

                <x-form.input name="graph_thumbnail" label="Open Graph Thumbnail" type="file" :editing="true"
                    :previewUrl="$settings->graph_thumbnail_url ?? null" />
            </div>

            <div class="flex justify-end space-x-4 pt-4">
                <a href="{{ route('admin.settings.general.edit') }}"
                    class="px-4 py-2 border border-gray-300 text-primary-700 rounded-lg hover:bg-gray-50 text-sm transition duration-300">
                    Cancel
                </a>
                <button type="submit"
                    class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition duration-300">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</x-admin.settings.layout>
