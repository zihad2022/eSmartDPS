<footer class="bg-primary-900 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">

            <div class="col-span-1 md:col-span-2 space-y-6">
                <x-footer.logo />
                <x-footer.description />
                <x-footer.social-icons />
            </div>

            <x-footer.quick-links />
            <x-footer.services />

        </div>

        <div class="border-t border-gray-700 mt-12 pt-8 text-center">
            <p class="text-gray-300">
                &copy; {{ date('Y') }} {{ $settings->site_name ?? 'Your Site' }}. All rights reserved.
            </p>
        </div>

    </div>
</footer>
