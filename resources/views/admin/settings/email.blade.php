{{-- 
    Email Settings Page
    -------------------------------------------------------
    This Blade template provides the interface to update
    email SMTP configurations including host, port, username,
    password, encryption type, sender details, and a message template.

    The $settings variable holds existing saved values to prefill the form.
--}}

<x-admin.settings.layout>

    {{-- =================== Flash Messages ===================
         Show success or error notification after form submission.
    --}}
    @if (session('success') || session('error'))
        <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
    @endif

    {{-- =================== Form Container ===================
         Main card container wrapping the email settings form.
    --}}
    <div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">

        {{-- Section Title --}}
        <h2 class="text-xl font-semibold text-primary-900 mb-6">
            Email Settings
        </h2>

        {{-- =================== Email Settings Form ===================
             Form submits via POST with PUT override to update settings.
             Targets the route 'admin.settings.email.update'.
        --}}
        <form method="POST" action="{{ route('admin.settings.email.update') }}" enctype="multipart/form-data"
            class="space-y-8">
            @csrf
            @method('PUT')

            {{-- =================== SMTP Host and Port ===================
                 Input fields for SMTP host and port.
            --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-form.input name="mail_host" label="Mail Host" :value="old('mail_host', $settings->mail_host ?? '')"
                    placeholder="Enter SMTP mail host (e.g. smtp.gmail.com)" />
                <x-form.input name="mail_port" label="Mail Port" :value="old('mail_port', $settings->mail_port ?? '')"
                    placeholder="Enter SMTP mail port (e.g. 587)" />
            </div>

            {{-- =================== SMTP Username and Password ===================
                 Input fields for SMTP credentials.
            --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-form.input name="mail_username" label="Mail Username" :value="old('mail_username', $settings->mail_username ?? '')"
                    placeholder="Enter SMTP username" />
                <x-form.input name="mail_password" label="Mail Password" type="password" :value="old('mail_password', '')"
                    placeholder="Leave blank to keep the current SMTP password" />
            </div>

            {{-- =================== Mail Encryption and From Address ===================
                 Select input for encryption type and email input for sender address.
            --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-form.select name="mail_encryption" label="Mail Encryption" :selected="old('mail_encryption', $settings->mail_encryption ?? 'tls')" :options="['tls' => 'TLS', 'starttls' => 'STARTTLS', 'ssl' => 'SSL', 'none' => 'None']" />
                <x-form.input name="mail_from_address" label="Mail From Address" type="email" :value="old('mail_from_address', $settings->mail_from_address ?? '')"
                    placeholder="Enter from email address" />
            </div>

            {{-- =================== Mail From Name ===================
                 Input for sender name displayed in emails.
            --}}
            <x-form.input name="mail_from_name" label="Mail From Name" :value="old('mail_from_name', $settings->mail_from_name ?? '')"
                placeholder="Enter from name (e.g. Company Name)" />

            {{-- =================== Email Message Template ===================
                 Textarea to customize default email message template.
            --}}
            <x-form.textarea name="email_message_template" label="Email Message Template" :value="old('email_message_template', $settings->email_message_template ?? '')"
                placeholder="Enter email message template" rows="4" />

            {{-- =================== Form Actions ===================
                 Buttons for cancelling or saving the form data.
            --}}
            <div class="flex justify-end space-x-4 pt-4">

                {{-- Cancel button redirects back to edit page --}}
                <a href="{{ route('admin.settings.email.edit') }}"
                    class="px-4 py-2 border border-gray-300 text-primary-700 rounded-lg hover:bg-gray-50 text-sm transition duration-300">
                    Cancel
                </a>

                {{-- Submit button saves changes --}}
                <button type="submit"
                    class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition duration-300">
                    Save Changes
                </button>

            </div>
        </form>

        {{-- =================== End of Form =================== --}}
    </div>
</x-admin.settings.layout>
