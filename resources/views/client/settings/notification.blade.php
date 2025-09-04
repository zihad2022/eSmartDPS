<x-client.settings.layout>
    {{-- Display success or error flash message --}}
    @if (session('success') || session('error'))
        <x-flash-message 
            :type="session('success') ? 'success' : 'error'" 
            :title="session('success') ? 'Success' : 'Error'" 
            :message="session('success') ?? session('error')" 
        />
    @endif

    <x-slot name="title">Settings</x-slot>

    <div id="settings" class="settings-content bg-white rounded-xl shadow-sm p-6 space-y-10">
     
        {{-- ===============================
            Notification Settings Section
        =============================== --}}
        <div>
            <h3 class="text-lg font-semibold text-primary-900 mb-6">Notification Settings</h3>

            <form class="space-y-6" action="{{ route('client.settings.notification.update') }}" method="POST">
                @csrf
                @method('PUT')

                {{-- SMS API Provider --}}
                <x-form.input 
                    name="sms_api_provider" 
                    label="SMS API Provider" 
                    :value="old('sms_api_provider', $settings->sms_api_provider ?? '')" 
                    placeholder="Enter SMS API provider (e.g., Twilio)" 
                />

                {{-- SMS API Key --}}
                <x-form.input 
                    name="sms_api_key" 
                    label="SMS API Key" 
                    :value="old('sms_api_key', $settings->sms_api_key ?? '')" 
                    placeholder="Enter SMS API key" 
                />

            <div>
                <label class="block text-sm font-medium text-primary-700 mb-2">Email Notifications</label>
                {{-- Email Notifications --}}
                <x-form.checkbox 
                    name="email_payment_confirmations" 
                    label="Email Notifications for Payments" 
                    :checked="old('email_payment_confirmations', $settings->email_payment_confirmations)" 
                />

                <x-form.checkbox 
                    name="email_payment_reminders" 
                    label="Email Notifications for Reminders" 
                    :checked="old('email_payment_reminders', $settings->email_payment_reminders)" 
                />

                <x-form.checkbox 
                    name="email_payment_reports" 
                    label="Email Notifications for Reports" 
                    :checked="old('email_payment_reports', $settings->email_payment_reports)" 
                />
            </div>

            {{-- SMS Notifications --}}
            <div>
                <label class="block text-sm font-medium text-primary-700 mb-2">SMS Notifications</label>
                <x-form.checkbox 
                    name="sms_payment_confirmations" 
                    label="SMS Notifications for Payments" 
                    :checked="old('sms_payment_confirmations', $settings->sms_payment_confirmations)" 
                />

                <x-form.checkbox 
                    name="sms_payment_reminders" 
                    label="SMS Notifications for Reminders" 
                    :checked="old('sms_payment_reminders', $settings->sms_payment_reminders)" 
                />
            </div>

                {{-- Submit --}}
                <button type="submit"
                    class="bg-accent-500 hover:bg-accent-600 text-white px-6 py-2 rounded-lg transition duration-300">
                    Save Changes
                </button>
            </form>
        </div>
    </div>
</x-client.settings.layout>
