<x-admin.settings.layout>
    <x-breadcrumb :items="[['label' => 'Dashboard', 'url' => route('admin.dashboard')], ['label' => 'Email Settings']]" />
    @if (session('success') || session('error'))
        <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
    @endif
    <div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">
        <h2 class="text-xl font-semibold text-primary-900 mb-6">Email Settings</h2>

        <form method="POST" action="{{ route('admin.settings.email.update') }}" enctype="multipart/form-data"
            class="space-y-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-form.input name="mail_host" label="Mail Host" :value="old('mail_host', $settings->mail_host ?? '')"
                    placeholder="Enter SMTP mail host (e.g. smtp.gmail.com)" />

                <x-form.input name="mail_port" label="Mail Port" :value="old('mail_port', $settings->mail_port ?? '')"
                    placeholder="Enter SMTP mail port (e.g. 587)" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-form.input name="mail_username" label="Mail Username" :value="old('mail_username', $settings->mail_username ?? '')"
                    placeholder="Enter SMTP username" />

                <x-form.input name="mail_password" label="Mail Password" type="password" :value="old('mail_password', $settings->mail_password ?? '')"
                    placeholder="Enter SMTP password" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <x-form.select name="mail_encryption" label="Mail Encryption" :value="old('mail_encryption', $settings->mail_encryption ?? '')" :options="['tls' => 'TLS', 'ssl' => 'SSL']" />

                <x-form.input name="mail_from_address" label="Mail From Address" type="email" :value="old('mail_from_address', $settings->mail_from_address ?? '')"
                    placeholder="Enter from email address" />
            </div>

            <x-form.input name="mail_from_name" label="Mail From Name" :value="old('mail_from_name', $settings->mail_from_name ?? '')"
                placeholder="Enter from name (e.g. Company Name)" />

            <x-form.textarea name="sms_message_template" label="SMS Message Template" :value="old('sms_message_template', $settings->sms_message_template ?? '')"
                placeholder="Enter SMS message template" rows="4" />

            <div class="flex justify-end space-x-4 pt-4">
                <a href="{{ route('admin.settings.email.edit') }}"
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
