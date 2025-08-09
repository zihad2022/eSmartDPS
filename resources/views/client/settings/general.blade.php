<x-client.settings.layout>
    @if (session('success') || session('error'))
        <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
    @endif
    <div id="general" class="settings-content bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-primary-900 mb-6">General Settings</h3>

        <form class="space-y-6" method="POST" action="{{ route('client.settings.general.update') }}">
            @csrf
            @method('PUT')

            <x-form.input label="Organization Name" name="organization_name"
                value="{{ old('organization_name', $settings->organization_name ?? '') }}"
                placeholder="Enter organization name" />

            <x-form.input label="Short Name" name="short_name"
                value="{{ old('short_name', $settings->short_name ?? '') }}" placeholder="Enter short name" />

            <x-form.input label="Contact Email" name="contact_email" type="email"
                value="{{ old('contact_email', $settings->contact_email ?? '') }}" placeholder="Enter contact email" />

            <x-form.input label="Contact Phone" name="contact_phone" type="tel"
                value="{{ old('contact_phone', $settings->contact_phone ?? '') }}"
                placeholder="Enter contact phone number" />

            <x-form.textarea label="Address" name="address"
                placeholder="Enter address">{{ old('address', $settings->address ?? '') }}</x-form.textarea>

            <x-form.select label="Currency" name="currency" :options="[
                'USD' => 'USD - US Dollar',
                'EUR' => 'EUR - Euro',
                'GBP' => 'GBP - British Pound',
            ]" :selected="old('currency', $settings->currency)"
                placeholder="Select a currency" />

            <button type="submit"
                class="bg-accent-500 hover:bg-accent-600 text-white px-6 py-2 rounded-lg transition duration-300">
                Save Changes
            </button>
        </form>

    </div>
</x-client.settings.layout>
