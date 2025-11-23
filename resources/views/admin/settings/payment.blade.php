<x-admin.settings.layout>

    @if (session('success') || session('error'))
        <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
    @endif

    <div>
        {{-- PAGE HEADER --}}
        <h2 class="text-xl font-semibold text-primary-900 mb-6 bg-white rounded-2xl p-6 w-full mx-auto">
            Payment Settings
        </h2>

        {{-- MAIN FORM - Handles all payment settings --}}
        <form method="POST" action="{{ route('admin.settings.payments.update') }}" enctype="multipart/form-data"
            class="space-y-8">
            @csrf
            @method('PUT')

            {{-- GENERAL PAYMENT SETTINGS --}}
            <div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">
                <h3 class="text-lg font-semibold text-primary-800 mb-4 border-b pb-2">
                    General Payment Settings
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Currency Dropdown --}}
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

                    {{-- Late Fee Input --}}
                    <x-form.input name="late_fee" label="Late Fee" type="number" step="0.01" :value="old('late_fee', $settings->late_fee ?? '')"
                        placeholder="Enter late fee amount" />
                </div>

                {{-- Submit Button for General Settings --}}
                <div class="flex justify-end pt-4">
                    <button type="submit" name="section" value="general"
                        class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition">
                        Save General Settings
                    </button>
                </div>
            </div>

            {{-- BKASH PAYMENT SETTINGS --}}
            <div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">
                <h3 class="text-lg font-semibold text-primary-800 mb-4 border-b pb-2">bKash Settings</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Base URL --}}
                    <x-form.input name="bkash_base_url" label="Base URL" :value="old('bkash_base_url', $settings->bkash_base_url ?? '')"
                        placeholder="Enter bKash Base URL" />

                    {{-- App Key --}}
                    <x-form.input name="bkash_app_key" label="App Key" :value="old('bkash_app_key', $settings->bkash_app_key ?? '')"
                        placeholder="Enter bKash App Key" />

                    {{-- App Secret --}}
                    <x-form.input name="bkash_app_secret" label="App Secret" :value="old('bkash_app_secret', $settings->bkash_app_secret ?? '')"
                        placeholder="Enter bKash App Secret" />

                    {{-- Username --}}
                    <x-form.input name="bkash_username" label="Username" :value="old('bkash_username', $settings->bkash_username ?? '')"
                        placeholder="Enter bKash Username" />

                    {{-- Password --}}
                    <x-form.password-input label="bKash Password" name="bkash_password" :value="$settings->bkash_password ?? ''"
                        placeholder="Enter bKash Password" required />

                    {{-- Charge (optional) --}}
                    <x-form.input name="bkash_charge" label="Extra Charge (%)" type="number" step="0.01"
                        :value="old('bkash_charge', $settings->bkash_charge ?? '0.00')" placeholder="Enter extra charge %" />

                    {{-- Status (optional) --}}
                    <div>
                        <label for="bkash_status" class="block text-sm font-medium text-primary-700 mb-2">
                            Status
                        </label>
                        <select id="bkash_status" name="bkash_status"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-accent-500">
                            <option value="1"
                                {{ old('bkash_status', $settings->bkash_status ?? false) ? 'selected' : '' }}>Active
                            </option>
                            <option value="0"
                                {{ old('bkash_status', $settings->bkash_status ?? false) ? '' : 'selected' }}>Inactive
                            </option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" name="section" value="bkash"
                        class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition">
                        Save bKash Settings
                    </button>
                </div>
            </div>

            {{-- SSLCOMMERZ SETTINGS --}}
            <div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">
                <h3 class="text-lg font-semibold text-primary-800 mb-4 border-b pb-2">SSLCommerz Settings</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="sslcommerz_store_id" label="Store ID" :value="old('sslcommerz_store_id', $settings->sslcommerz_store_id ?? '')"
                        placeholder="Enter Store ID" />
                    <x-form.input name="sslcommerz_store_password" label="Store Password" type="password"
                        :value="old('sslcommerz_store_password', $settings->sslcommerz_store_password ?? '')" placeholder="Enter Store Password" />
                    <x-form.select name="sslcommerz_mode" label="Mode" :options="['live' => 'Live', 'sandbox' => 'Sandbox']" :selected="old('sslcommerz_mode', $settings->sslcommerz_mode ?? 'sandbox')"
                        required />
                </div>
                <div class="flex justify-end pt-4">
                    <button type="submit" name="section" value="sslcommerz"
                        class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition">
                        Save SSLCommerz Settings
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-admin.settings.layout>
