<x-admin.settings.layout>
    @if (session('success') || session('error'))
        <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
    @endif

    <div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">
        <h2 class="text-xl font-semibold text-primary-900 mb-6">SMS Settings</h2>

        <form method="POST" action="{{ route('admin.settings.sms.update') }}" enctype="multipart/form-data"
            class="space-y-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-form.input name="sms_api_key" label="SMS API Key" :value="old('sms_api_key', $settings->sms_api_key ?? '')" placeholder="Enter SMS API Key" />

                <x-form.input name="sms_secret_key" label="SMS Secret Key" :value="old('sms_secret_key', $settings->sms_secret_key ?? '')"
                    placeholder="Enter SMS Secret Key" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-form.input name="sms_sender_id" label="Sender ID" :value="old('sms_sender_id', $settings->sms_sender_id ?? '')" placeholder="Enter Sender ID" />

                <x-form.input name="sms_api_url" label="API URL" :value="old('sms_api_url', $settings->sms_api_url ?? '')" placeholder="Enter SMS API URL" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-form.input name="sms_balance_api" label="Balance API URL" :value="old('sms_balance_api', $settings->sms_balance_api ?? '')"
                    placeholder="Enter SMS Balance API URL" />
            </div>

            <x-form.textarea name="sms_message_template" label="Default SMS Template" :value="old('sms_message_template', $settings->sms_message_template ?? '')"
                placeholder="Enter default SMS message template" rows="4" />

            <div class="flex justify-end space-x-4 pt-4">
                <a href="{{ route('admin.settings.sms.edit') }}"
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
