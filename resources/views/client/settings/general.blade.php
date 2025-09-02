{{-- 
|--------------------------------------------------------------------------
| General Settings Page
|--------------------------------------------------------------------------
| This Blade view is used for updating the website's general settings.
| It is loaded inside the admin settings layout.  
| Sections:
| 1. Flash message display
| 2. General settings form (organization info, contact info, address, currency)
| 3. Save and cancel actions
|--------------------------------------------------------------------------
--}}

<x-client.settings.layout>

    {{-- Display success or error flash message --}}
    @if (session('success') || session('error'))
        <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
    @endif

    {{-- Main container for General Settings form --}}
    <div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">
        <h2 class="text-xl font-semibold text-primary-900 mb-6">General Settings</h2>

        {{-- 
            Form: Update general settings
            Method: PUT (for update request)
            Action: admin.settings.general.update
            Includes: CSRF protection & method spoofing
        --}}
        <form method="POST" action="{{ route('client.settings.general.update') }}" enctype="multipart/form-data"
            class="space-y-8">
            @csrf
            @method('PUT')

            {{-- Organization Information --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-form.input name="organization_name" label="Organization Name" :value="old('organization_name', $settings->organization_name ?? '')"
                    placeholder="Enter organization name" />

                <x-form.input name="short_name" label="Short Name" :value="old('short_name', $settings->short_name ?? '')"
                    placeholder="Enter short name (abbr.)" />
            </div>

            {{-- Contact Information --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-form.input name="contact_email" label="Contact Email" type="email" :value="old('contact_email', $settings->contact_email ?? '')"
                    placeholder="Enter contact email" />

                <x-form.input name="contact_phone" label="Contact Phone" :value="old('contact_phone', $settings->contact_phone ?? '')"
                    placeholder="Enter phone number" />
            </div>

            {{-- Address --}}
            <x-form.textarea name="address" label="Address" :value="old('address', $settings->address ?? '')" placeholder="Enter organization address"
                rows="3" />

            {{-- Currency Dropdown --}}
            <div class="w-full">
                <x-form.select name="currency" label="Currency" :options="[
                    'USD' => 'USD - US Dollar',
                    'EUR' => 'EUR - Euro',
                    'GBP' => 'GBP - British Pound',
                    'BDT' => 'BDT - Bangladeshi Taka',
                    'INR' => 'INR - Indian Rupee',
                    'AUD' => 'AUD - Australian Dollar',
                    'CAD' => 'CAD - Canadian Dollar',
                    'JPY' => 'JPY - Japanese Yen',
                    'CNY' => 'CNY - Chinese Yuan',
                    'SGD' => 'SGD - Singapore Dollar',
                    'MYR' => 'MYR - Malaysian Ringgit',
                    'THB' => 'THB - Thai Baht',
                    'SAR' => 'SAR - Saudi Riyal',
                    'AED' => 'AED - UAE Dirham',
                    'PKR' => 'PKR - Pakistani Rupee',
                    'LKR' => 'LKR - Sri Lankan Rupee',
                    'NZD' => 'NZD - New Zealand Dollar',
                    'CHF' => 'CHF - Swiss Franc',
                    'HKD' => 'HKD - Hong Kong Dollar',
                    'ZAR' => 'ZAR - South African Rand',
                ]" :selected="old('currency', $settings->currency ?? 'USD')" required />
            </div>

            {{-- Form action buttons: Cancel & Save --}}
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
</x-client.settings.layout>
