{{-- 
    SMS Settings Page
    -------------------------------------------------------
    This Blade template allows admins to configure SMS-related
    settings such as API keys, sender IDs, URLs, and message templates.

    The $settings variable contains existing saved values which
    are loaded into the form fields by default.
--}}

<x-admin.settings.layout>

    {{-- =================== Flash Messages ===================
         Displays success or error messages after form submission.
         Useful to inform the user about the status of their action.
    --}}
    @if (session('success') || session('error'))
        <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
    @endif

    {{-- =================== Settings Form Container ===================
         Main white card container wrapping the SMS settings form.
    --}}
    <div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">

        {{-- Section Header --}}
        <h2 class="text-xl font-semibold text-primary-900 mb-6">
            SMS Settings
        </h2>

        {{-- =================== SMS Settings Form ===================
             Form to update SMS settings.
             Uses POST method with PUT override to update existing data.
             Submits to route 'admin.settings.sms.update'.
        --}}
        <form method="POST" action="{{ route('admin.settings.sms.update') }}" enctype="multipart/form-data"
            class="space-y-8">
            @csrf
            @method('PUT')

            {{-- =================== API Credentials Inputs ===================
                 Grouped input fields for API key and secret key.
            --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- SMS API Key --}}
                <x-form.input name="sms_api_key" label="SMS API Key" :value="old('sms_api_key', $settings->sms_api_key ?? '')" placeholder="Enter SMS API Key" />

                {{-- SMS Client ID --}}
                <x-form.input name="sms_client_id" label="SMS Client ID" :value="old('sms_client_id', $settings->sms_client_id ?? '')"
                    placeholder="Enter SMS Client ID" />
            </div>

            {{-- =================== Sender & API URL Inputs ===================
                 Grouped input fields for Sender ID and the SMS API endpoint URL.
            --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Sender ID --}}
                <x-form.input name="sms_sender_id" label="Sender ID" :value="old('sms_sender_id', $settings->sms_sender_id ?? '')" placeholder="Enter Sender ID" />

                {{-- SMS API URL --}}
                <x-form.input name="sms_api_url" label="API URL" :value="old('sms_api_url', $settings->sms_api_url ?? '')" placeholder="Enter SMS API URL" />
            </div>

            {{-- =================== Balance API URL Input ===================
                 Single input for API URL to check SMS balance.
            --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- SMS Balance API URL --}}
                <x-form.input name="sms_balance_api" label="Balance API URL" :value="old('sms_balance_api', $settings->sms_balance_api ?? '')"
                    placeholder="Enter SMS Balance API URL" />

                    {{-- SMS Status --}}
                    <x-form.select name="sms_status" label="Status" :options="['1' => 'Active', '0' => 'Inactive']" :selected="old('sms_status', $settings->sms_status ?? '1')" />
            </div>

            {{-- =================== Default SMS Template Textarea ===================
                 Textarea for setting a default SMS message template.
            --}}
            <x-form.textarea name="sms_message_template" label="Default SMS Template" :value="old('sms_message_template', $settings->sms_message_template ?? '')"
                placeholder="Enter default SMS message template" rows="4" />

            {{-- =================== Form Actions ===================
                 Cancel button redirects back to edit page without saving.
                 Save Changes button submits the form to update settings.
            --}}
            <div class="flex justify-end space-x-4 pt-4">

                {{-- Cancel Button --}}
                <a href="{{ route('admin.settings.sms.edit') }}"
                    class="px-4 py-2 border border-gray-300 text-primary-700 rounded-lg hover:bg-gray-50 text-sm transition duration-300">
                    Cancel
                </a>

                {{-- Submit Button --}}
                <button type="submit"
                    class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition duration-300">
                    Save Changes
                </button>

            </div>
        </form>
        {{-- =================== End of Form =================== --}}
    </div>
</x-admin.settings.layout>
