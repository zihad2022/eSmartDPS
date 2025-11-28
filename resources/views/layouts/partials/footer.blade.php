@php
    $settings = \App\Models\AdminSetting::select('site_description')->first();
    $socialMedia = \App\Models\AdminSetting::select('facebook_page', 'facebook_group', 'whatsapp_channel', 'telegram_channel', 'linkedin', 'twitter_x', 'youtube', 'tiktok')->first();
@endphp
<footer class="bg-primary-900 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="col-span-1 md:col-span-2">
                <div>
                    <x-application-logo />
                </div>
                <p class="text-gray-300 mb-6 max-w-md">
                    {{ $settings->site_description }}
                </p>
                <div class="flex space-x-4">
                    @if ($socialMedia->facebook_page)
                        <a href="{{ $socialMedia->facebook_page }}"
                            class="w-10 h-10 bg-accent-500 rounded-full flex items-center justify-center hover:bg-accent-600 transition duration-300">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                    @endif
                @if ($socialMedia->facebook_group)
                        <a href="{{ $socialMedia->facebook_group }}"
                            class="w-10 h-10 bg-accent-500 rounded-full flex items-center justify-center hover:bg-accent-600 transition duration-300">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                    @endif
                    @if ($socialMedia->whatsapp_channel)
                        <a href="{{ $socialMedia->whatsapp_channel }}"
                            class="w-10 h-10 bg-accent-500 rounded-full flex items-center justify-center hover:bg-accent-600 transition duration-300">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    @endif
                    @if ($socialMedia->telegram_channel)
                        <a href="{{ $socialMedia->telegram_channel }}"
                            class="w-10 h-10 bg-accent-500 rounded-full flex items-center justify-center hover:bg-accent-600 transition duration-300">
                            <i class="fab fa-telegram"></i>
                        </a>
                    @endif
                    @if ($socialMedia->linkedin)
                        <a href="{{ $socialMedia->linkedin }}"
                            class="w-10 h-10 bg-accent-500 rounded-full flex items-center justify-center hover:bg-accent-600 transition duration-300">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    @endif
                        @if ($socialMedia->twitter_x)
                        <a href="{{ $socialMedia->twitter_x }}"
                            class="w-10 h-10 bg-accent-500 rounded-full flex items-center justify-center hover:bg-accent-600 transition duration-300">
                            <i class="fab fa-twitter"></i>
                        </a>
                    @endif
                    @if ($socialMedia->youtube)
                        <a href="{{ $socialMedia->youtube }}"
                            class="w-10 h-10 bg-accent-500 rounded-full flex items-center justify-center hover:bg-accent-600 transition duration-300">
                            <i class="fab fa-youtube"></i>
                        </a>
                    @endif
                    @if ($socialMedia->tiktok)
                        <a href="{{ $socialMedia->tiktok }}"
                            class="w-10 h-10 bg-accent-500 rounded-full flex items-center justify-center hover:bg-accent-600 transition duration-300">
                            <i class="fab fa-tiktok"></i>
                        </a>
                    @endif
                </div>
            </div>

            <div>
                <h3 class="font-semibold text-lg mb-6">Quick Links</h3>
                <ul class="space-y-3">
                    <li><a href="#home" class="text-gray-300 hover:text-accent-400 transition duration-300">Home</a>
                    </li>
                    <li><a href="#about" class="text-gray-300 hover:text-accent-400 transition duration-300">About
                            Us</a></li>
                    <li><a href="#services"
                            class="text-gray-300 hover:text-accent-400 transition duration-300">Services</a></li>
                    <li><a href="#contact"
                            class="text-gray-300 hover:text-accent-400 transition duration-300">Contact</a></li>
                </ul>
            </div>

            <div>
                <h3 class="font-semibold text-lg mb-6">Services</h3>
                <ul class="space-y-3">
                    <li><a href="#" class="text-gray-300 hover:text-accent-400 transition duration-300">Savings
                            Accounts</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-accent-400 transition duration-300">Investment
                            Plans</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-accent-400 transition duration-300">Micro
                            Loans</a>
                    </li>
                    <li><a href="#" class="text-gray-300 hover:text-accent-400 transition duration-300">Financial
                            Education</a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-700 mt-12 pt-8 text-center">
            <p class="text-gray-300">
                &copy; 2025 DYDS - Dream Young Development Society. All rights reserved.
            </p>
        </div>
    </div>
</footer>
