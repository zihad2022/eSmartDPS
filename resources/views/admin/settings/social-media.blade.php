<x-admin.settings.layout>
    @if (session('success') || session('error'))
        <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
    @endif

    <div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">
        <h2 class="text-xl font-semibold text-primary-900 mb-6">Social Media Settings</h2>

        <form method="POST" action="{{ route('admin.settings.social_media.update') }}" enctype="multipart/form-data"
            class="space-y-8">
            @csrf
            @method('PUT')
            {{-- =================== Social Media =================== --}}
            <h3 class="text-lg font-semibold text-primary-800 mt-10">Social Media Links</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-form.input name="facebook_page" label="Facebook Page" :value="old('facebook_page', $settings->facebook_page ?? '')"
                    placeholder="https://facebook.com/yourpage" />

                <x-form.input name="facebook_group" label="Facebook Group" :value="old('facebook_group', $settings->facebook_group ?? '')"
                    placeholder="https://facebook.com/groups/yourgroup" />

                <x-form.input name="whatsapp_channel" label="WhatsApp Channel" :value="old('whatsapp_channel', $settings->whatsapp_channel ?? '')"
                    placeholder="https://wa.me/yourchannel" />

                <x-form.input name="telegram_channel" label="Telegram Channel" :value="old('telegram_channel', $settings->telegram_channel ?? '')"
                    placeholder="https://t.me/yourchannel" />

                <x-form.input name="linkedin" label="LinkedIn" :value="old('linkedin', $settings->linkedin ?? '')"
                    placeholder="https://linkedin.com/company/yourcompany" />

                <x-form.input name="twitter_x" label="Twitter / X" :value="old('twitter_x', $settings->twitter_x ?? '')"
                    placeholder="https://twitter.com/yourprofile" />

                <x-form.input name="youtube" label="YouTube" :value="old('youtube', $settings->youtube ?? '')"
                    placeholder="https://youtube.com/@yourchannel" />

                <x-form.input name="tiktok" label="TikTok" :value="old('tiktok', $settings->tiktok ?? '')"
                    placeholder="https://tiktok.com/@yourprofile" />
            </div>

            {{-- =================== Actions =================== --}}
            <div class="flex justify-end space-x-4 pt-4">
                <a href="{{ route('admin.settings.social_media.edit') }}"
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
