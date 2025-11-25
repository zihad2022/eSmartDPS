{{-- 
    ============================
    Contact Information Settings Page
    Admin can update helpline number, email, office address, 
    and Google Map embed code here.
    ============================
--}}

<x-admin.settings.layout>

    {{-- ============================
        Flash Message Section
        - Shows success or error messages after form submission
        - Uses reusable <x-flash-message> component
    ============================ --}}
    @if (session('success') || session('error'))
        <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
    @endif

    {{-- ============================
        Main Settings Form Card
    ============================ --}}
    <div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">

        {{-- Card Title --}}
        <h2 class="text-xl font-semibold text-primary-900 mb-6">
            Contact Information Settings
        </h2>

        {{-- ============================
            Contact Information Form
            - Method: PUT
            - Route: admin.settings.contact_info.update
            - Handles CSRF protection
        ============================ --}}
        <form method="POST" action="{{ route('admin.settings.contact-info.update') }}" enctype="multipart/form-data"
            class="space-y-8">
            @csrf
            @method('PUT')

            {{-- ============================
                Input Fields
                - Arranged in a responsive grid
            ============================ --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Helpline Number --}}
                <x-form.input name="helpline_number" label="Helpline Number" :value="old('helpline_number', $settings->helpline_number ?? '')"
                    placeholder="+880 1XXX-XXXXXX" />

                {{-- Email Address --}}
                <x-form.input name="email_address" label="Email Address" type="email" :value="old('email_address', $settings->email_address ?? '')"
                    placeholder="your@email.com" />

                {{-- Office Address --}}
                <x-form.input name="office_address" label="Office Address" :value="old('office_address', $settings->office_address ?? '')"
                    placeholder="123 Your Street, City, Country" />
            </div>

            {{-- Google Map Embed Code --}}
            <x-form.textarea name="google_map" label="Google Map Embed Code" :value="old('google_map', $settings->google_map ?? '')"
                placeholder="Paste your Google Map embed iframe code here" rows="4" />

            {{-- ============================
                Action Buttons
            ============================ --}}
            <div class="flex justify-end space-x-4 pt-4">

                {{-- Cancel (Reloads edit page without saving changes) --}}
                <a href="{{ route('admin.settings.contact-info.edit') }}"
                    class="px-4 py-2 border border-gray-300 text-primary-700 rounded-lg hover:bg-gray-50 text-sm transition duration-300">
                    Cancel
                </a>

                {{-- Save Changes --}}
                <button type="submit"
                    class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition duration-300">
                    Save Changes
                </button>

            </div>
        </form>
    </div>
</x-admin.settings.layout>
