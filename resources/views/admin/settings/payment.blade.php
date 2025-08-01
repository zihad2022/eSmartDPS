<x-admin.settings.layout>
    <x-breadcrumb :items="[['label' => 'Dashboard', 'url' => route('admin.dashboard')], ['label' => 'Payment Settings']]" />

    @if (session('success') || session('error'))
        <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
    @endif

    <div>
        <h2 class="text-xl font-semibold text-primary-900 mb-6">Payment Settings</h2>

        <form method="POST" action="{{ route('admin.settings.payments.update') }}" enctype="multipart/form-data"
            class="space-y-8">
            @csrf
            @method('PUT')

            <!-- ✅ General Payment Settings -->
            <div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">
                <h3 class="text-lg font-semibold text-primary-800 mb-4 border-b pb-2">General Payment Settings</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="currency" label="Currency" :value="old('currency', $settings->currency ?? '')"
                        placeholder="Enter currency code (e.g. USD, BDT)" />
                    <x-form.input name="late_fee" label="Late Fee" type="number" step="0.01" :value="old('late_fee', $settings->late_fee ?? '')"
                        placeholder="Enter late fee amount" />
                </div>
                <div class="flex justify-end pt-4">
                    <button type="submit" name="section" value="general"
                        class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition">
                        Save General Settings
                    </button>
                </div>
            </div>

            <!-- ✅ bKash Settings -->
            <div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">
                <h3 class="text-lg font-semibold text-primary-800 mb-4 border-b pb-2">bKash Settings</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="bkash_app_key" label="App Key" :value="old('bkash_app_key', $settings->bkash_app_key ?? '')"
                        placeholder="Enter bKash App Key" />
                    <x-form.input name="bkash_app_secret" label="App Secret" :value="old('bkash_app_secret', $settings->bkash_app_secret ?? '')"
                        placeholder="Enter bKash App Secret" />
                    <x-form.input name="bkash_username" label="Username" :value="old('bkash_username', $settings->bkash_username ?? '')"
                        placeholder="Enter bKash Username" />
                    <x-form.input name="bkash_password" label="Password" type="password" :value="old('bkash_password', $settings->bkash_password ?? '')"
                        placeholder="Enter bKash Password" />
                </div>
                <div class="flex justify-end pt-4">
                    <button type="submit" name="section" value="bkash"
                        class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition">
                        Save bKash Settings
                    </button>
                </div>
            </div>

            <!-- ✅ UddoktaPay Settings -->
            <div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">
                <h3 class="text-lg font-semibold text-primary-800 mb-4 border-b pb-2">UddoktaPay Settings</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="uddoktapay_api_key" label="API Key" :value="old('uddoktapay_api_key', $settings->uddoktapay_api_key ?? '')"
                        placeholder="Enter UddoktaPay API Key" />
                    <x-form.input name="uddoktapay_secret" label="Secret" :value="old('uddoktapay_secret', $settings->uddoktapay_secret ?? '')"
                        placeholder="Enter UddoktaPay Secret" />
                    <x-form.input name="uddoktapay_callback_url" label="Callback URL" :value="old('uddoktapay_callback_url', $settings->uddoktapay_callback_url ?? '')"
                        placeholder="Enter UddoktaPay Callback URL" />
                </div>
                <div class="flex justify-end pt-4">
                    <button type="submit" name="section" value="uddoktapay"
                        class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition">
                        Save UddoktaPay Settings
                    </button>
                </div>
            </div>

            <!-- ✅ SSLCommerz Settings -->
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
