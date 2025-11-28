<div id="mobileMenu" class="md:hidden hidden bg-white border-t border-gray-200">
    <div class="px-4 py-4 space-y-4">
        <a href="{{ route('home') }}" class="block text-primary-600 hover:text-accent-600">Home</a>
        <a href="{{ route('about') }}" class="block text-primary-600 hover:text-accent-600">About</a>
        <a href="{{ route('pricing') }}" class="block text-primary-600 hover:text-accent-600">Pricing</a>
        <a href="#contact" class="block text-primary-600 hover:text-accent-600">Contact</a>

        <div class="flex flex-col space-y-2 pt-4 border-t border-gray-200">
            <a href="{{ route('member.login') }}"
               class="bg-primary-100 text-primary-700 px-4 py-2 rounded-lg text-left">
                <i class="fas fa-user mr-2"></i>Member Portal
            </a>

            <a href="{{ route('client.login') }}"
               class="bg-accent-500 text-white px-4 py-2 rounded-lg text-left">
                <i class="fas fa-shield-alt mr-2"></i>Admin Login
            </a>
        </div>
    </div>
</div>
