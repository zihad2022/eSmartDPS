<x-client.settings.layout>
     {{-- Display success or error flash message --}}
     @if (session('success') || session('error'))
     <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
 @endif
    {{-- 
        Page Title 
        This will be displayed in the browser/tab and header section 
    --}}
    <x-slot name="title">Share Settings</x-slot>

    {{-- 
        ===============================
        Share Settings Form
        Fields here directly map to 
        the database columns:
        - share_price
        - minimum_shares
        - maximum_shares
        - share_transfer_fee
        - allow_partial_shares
        ===============================
    --}}
    <div id="shares" class="settings-content bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-primary-900 mb-6">Share Settings</h3>

        <form class="space-y-6" action="{{ route('client.settings.share.update') }}" method="POST">
            @csrf
            @method('PUT')
            {{-- Share Price (maps to: share_price) --}}
            <x-form.input name="share_price" label="Share Price" :value="old('share_price', $settings->share_price ?? '')" placeholder="Enter share price" />

            {{-- Minimum Shares (maps to: minimum_shares) --}}
            <x-form.input name="minimum_shares" label="Minimum Shares" :value="old('minimum_shares', $settings->minimum_shares ?? '')" placeholder="Enter minimum shares" />

            {{-- Maximum Shares (maps to: maximum_shares) --}}
            <x-form.input name="maximum_shares" label="Maximum Shares" :value="old('maximum_shares', $settings->maximum_shares ?? '')" placeholder="Enter maximum shares" />

            {{-- Share Transfer Fee (maps to: share_transfer_fee) --}}
            <x-form.input name="share_transfer_fee" label="Share Transfer Fee" :value="old('share_transfer_fee', $settings->share_transfer_fee ?? '')" placeholder="Enter share transfer fee" />

            {{-- Allow Partial Shares (maps to: allow_partial_shares) --}}
            <x-form.checkbox 
            name="allow_partial_shares" 
            label="Allow Partial Shares" 
            :checked="old('allow_partial_shares', $settings->allow_partial_shares) == 1" 
        />

            {{-- Submit Button --}}
            <button type="submit"
                class="bg-accent-500 hover:bg-accent-600 text-white px-6 py-2 rounded-lg transition duration-300">
                Save Changes
            </button>
        </form>
    </div>
</x-client.settings.layout>
