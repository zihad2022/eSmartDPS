<nav class="fixed top-0 w-full z-50 bg-white/90 backdrop-blur-md border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div>
                    <x-application-logo/>
            </div>

            <div class="hidden md:flex items-center space-x-8">
                <a href="{{route('home')}}" class="text-primary-600 hover:text-accent-600 transition duration-300">Home</a>
                <a href="{{route('about')}}" class="text-primary-600 hover:text-accent-600 transition duration-300">About</a>
                <a href="{{route('pricing')}}"
                    class="text-primary-600 hover:text-accent-600 transition duration-300">Pricing</a>
                <a href="#contact"
                    class="text-primary-600 hover:text-accent-600 transition duration-300">Contact</a>
            </div>

            <div class="hidden md:flex items-center space-x-4">
                <a href="{{ route('member.login') }}"
                    class="bg-primary-100 text-primary-700 px-4 py-2 rounded-lg hover:bg-primary-200 transition duration-300">
                    <i class="fas fa-user mr-2"></i>Member Portal
                </a>
                <a href="{{ route('client.login') }}"
                    class="bg-accent-500 text-white px-4 py-2 rounded-lg hover:bg-accent-600 transition duration-300 btn-glow">
                    <i class="fas fa-shield-alt mr-2"></i>Admin Login
                </a>
            </div>

            <!-- Mobile Menu Button -->
            <button id="mobileMenuBtn" class="md:hidden text-primary-600">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="md:hidden hidden bg-white border-t border-gray-200">
        <div class="px-4 py-4 space-y-4">
            <a href="{{route('home')}}" class="block text-primary-600 hover:text-accent-600">Home</a>
            <a href="{{route('about')}}" class="block text-primary-600 hover:text-accent-600">About</a>
            <a href="{{route('pricing')}}" class="block text-primary-600 hover:text-accent-600">Pricing</a>
            <a href="#contact" class="block text-primary-600 hover:text-accent-600">Contact</a>
            <div class="flex flex-col space-y-2 pt-4 border-t border-gray-200">
                <a href="{{ route('member.login') }}"
                    class="bg-primary-100 text-primary-700 px-4 py-2 rounded-lg text-left">
                    <i class="fas fa-user mr-2"></i>Member Portal
                </a>
                <button onclick="window.location.href='login.html'"
                    class="bg-accent-500 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-shield-alt mr-2"></i>Admin Login
                </button>
            </div>
        </div>
    </div>
</nav>