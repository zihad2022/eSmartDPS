{{-- 
|--------------------------------------------------------------------------
| General Settings Page
|--------------------------------------------------------------------------
| This Blade view is used for updating the website's general settings.
| It is loaded inside the admin settings layout.  
| Sections:
| 1. Flash message display
| 2. General settings form (site name, slogan, SEO data, meta codes, images)
| 3. Save and cancel actions
|--------------------------------------------------------------------------
--}}

<x-admin.settings.layout>

    {{-- Display success or error flash message --}}
    @if (session('success') || session('error'))
        {{-- 
            Component: <x-flash-message>
            Props:
                - type: 'success' or 'error'
                - title: Success or Error
                - message: The flash message content
        --}}
        <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
    @endif

    {{-- Main container for General Settings form --}}
    <div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">
        {{-- Section title --}}
        <h2 class="text-xl font-semibold text-primary-900 mb-6">General Settings</h2>

        {{-- 
            Form: Update general settings
            Method: PUT (for update request)
            Action: admin.settings.general.update
            Includes: CSRF protection & method spoofing
        --}}
        <form method="POST" action="{{ route('admin.settings.general.update') }}" enctype="multipart/form-data"
            class="space-y-8">
            @csrf
            @method('PUT')

            {{-- Site Name & Site Slogan fields --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-form.input name="site_name" label="Site Name" :value="old('site_name', $settings->site_name ?? '')" placeholder="Enter website name" />

                <x-form.input name="site_slogan" label="Site Slogan" :value="old('site_slogan', $settings->site_slogan ?? '')"
                    placeholder="Enter site slogan" />
            </div>

            {{-- Site Description --}}
            <x-form.textarea name="site_description" label="Site Description" :value="old('site_description', $settings->site_description ?? '')"
                placeholder="Enter short description" rows="3" />

            {{-- Site Keywords (SEO) --}}
            <x-form.textarea name="site_keywords" label="Site Keywords" :value="old('site_keywords', $settings->site_keywords ?? '')"
                placeholder="Enter SEO keywords (comma separated)" rows="2" />

            {{-- Meta Codes (Analytics, tracking scripts) --}}
            <x-form.textarea name="meta_codes" label="Meta Codes" :value="old('meta_codes', $settings->meta_codes ?? '')"
                placeholder="Paste analytics or tracking codes here" rows="4" />

            {{-- Logo, Favicon & Open Graph Thumbnail uploads --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <x-form.input name="site_logo" label="Site Logo" type="file" :editing="true" :previewUrl="$settings->site_logo_url ?? null" />

                <x-form.input name="favicon" label="Favicon" type="file" :editing="true" :previewUrl="$settings->favicon_url ?? null" />

                <x-form.input name="graph_thumbnail" label="Open Graph Thumbnail" type="file" :editing="true"
                    :previewUrl="$settings->graph_thumbnail_url ?? null" />
            </div>

            {{-- Form action buttons: Cancel & Save --}}
            <div class="flex justify-end space-x-4 pt-4">
                {{-- Cancel button: redirects back to edit page without saving --}}
                <a href="{{ route('admin.settings.general.edit') }}"
                    class="px-4 py-2 border border-gray-300 text-primary-700 rounded-lg hover:bg-gray-50 text-sm transition duration-300">
                    Cancel
                </a>

                {{-- Save Changes button --}}
                <button type="submit"
                    class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition duration-300">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</x-admin.settings.layout>
