{{-- 
    Social Media Settings Page
    -------------------------------------------------------
    This Blade view is used to update the social media links
    for the application. The settings are stored in the database 
    and are retrieved through the $settings variable.
    Admins can update various social media links like Facebook, 
    WhatsApp, Telegram, etc.
--}}

<x-admin.settings.layout>

    {{-- =================== Flash Messages ===================
        This section displays a success or error message 
        if the session has any. Useful for user feedback.
    --}}
    @if (session('success') || session('error'))
        <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
    @endif

    {{-- =================== Settings Card ===================
        This container holds the social media settings form.
    --}}
    <div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">

        {{-- Section Title --}}
        <h2 class="text-xl font-semibold text-primary-900 mb-6">
            Social Media Settings
        </h2>

        {{-- =================== Form Start ===================
            Form to update social media links.
            - Uses PUT method for updating existing settings.
            - The route admin.settings.social-media.update 
              will handle saving the data.
        --}}
        <form method="POST" action="{{ route('admin.settings.social-media.update') }}" enctype="multipart/form-data"
            class="space-y-8">
            @csrf
            @method('PUT')

            {{-- =================== Input Fields ===================
                Each field represents a social media platform link.
                Pre-filled using old() helper with fallback from $settings.
            --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Facebook Page --}}
                <x-form.input name="facebook_page" label="Facebook Page" :value="old('facebook_page', $settings->facebook_page ?? '')"
                    placeholder="https://facebook.com/yourpage" />

                {{-- Facebook Group --}}
                <x-form.input name="facebook_group" label="Facebook Group" :value="old('facebook_group', $settings->facebook_group ?? '')"
                    placeholder="https://facebook.com/groups/yourgroup" />

                {{-- WhatsApp Channel --}}
                <x-form.input name="whatsapp_channel" label="WhatsApp Channel" :value="old('whatsapp_channel', $settings->whatsapp_channel ?? '')"
                    placeholder="https://wa.me/yourchannel" />

                {{-- Telegram Channel --}}
                <x-form.input name="telegram_channel" label="Telegram Channel" :value="old('telegram_channel', $settings->telegram_channel ?? '')"
                    placeholder="https://t.me/yourchannel" />

                {{-- LinkedIn --}}
                <x-form.input name="linkedin" label="LinkedIn" :value="old('linkedin', $settings->linkedin ?? '')"
                    placeholder="https://linkedin.com/company/yourcompany" />

                {{-- Twitter / X --}}
                <x-form.input name="twitter_x" label="Twitter / X" :value="old('twitter_x', $settings->twitter_x ?? '')"
                    placeholder="https://twitter.com/yourprofile" />

                {{-- YouTube --}}
                <x-form.input name="youtube" label="YouTube" :value="old('youtube', $settings->youtube ?? '')"
                    placeholder="https://youtube.com/@yourchannel" />

                {{-- TikTok --}}
                <x-form.input name="tiktok" label="TikTok" :value="old('tiktok', $settings->tiktok ?? '')"
                    placeholder="https://tiktok.com/@yourprofile" />
            </div>

            {{-- =================== Actions ===================
                Cancel → Redirects back to edit page without saving.
                Save Changes → Submits the form and updates settings.
            --}}
            <div class="flex justify-end space-x-4 pt-4">
                <a href="{{ route('admin.settings.social-media.edit') }}"
                    class="px-4 py-2 border border-gray-300 text-primary-700 rounded-lg hover:bg-gray-50 text-sm transition duration-300">
                    Cancel
                </a>

                <button type="submit"
                    class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition duration-300">
                    Save Changes
                </button>
            </div>
        </form>
        {{-- =================== Form End =================== --}}
    </div>
</x-admin.settings.layout>
