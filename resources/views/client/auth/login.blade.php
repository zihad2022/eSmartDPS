<x-client.layout.guest>
    <div class="glass rounded-2xl p-8 login-card">
        <h2 class="text-2xl font-bold text-white text-center mb-6">Welcome Back</h2>
        
        <!-- Error Message -->
        @if (session('error'))
            <div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg mb-6 text-sm">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('client.authenticate') }}" id="loginForm" class="space-y-6">
            @csrf

            <!-- Username Field -->
            <div>
                <label for="user_id" class="block text-sm font-medium text-primary-200 mb-2">
                    <i class="fas fa-user mr-2"></i>User ID
                </label>
                <input type="text" id="user_id" name="user_id" required
                    class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-primary-300 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:border-transparent transition duration-300"
                    placeholder="Enter your user ID">
                @error('user_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Field -->
            <div>
                <label for="password" class="block text-sm font-medium text-primary-200 mb-2">
                    <i class="fas fa-lock mr-2"></i>Password
                </label>
                <div class="relative">
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-primary-300 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:border-transparent transition duration-300"
                        placeholder="Enter your password">
                    <button type="button" id="togglePassword"
                        class="absolute right-3 top-3 text-primary-300 hover:text-white">
                        <i class="fas fa-eye"></i>
                    </button>
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Remember me --}}
            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <input type="checkbox" name="remember"
                        class="w-4 h-4 text-accent-500 bg-white/10 border-white/20 rounded focus:ring-accent-500">
                    <span class="ml-2 text-sm text-primary-200">Remember me</span>
                </label>
                <a href="{{route('client.forgot.password.phone')}}" class="text-sm text-accent-400 hover:text-accent-300 transition duration-300">
                    Forgot password?
                </a>
            </div>

            <!-- Login Button -->
            <button type="submit"
                class="w-full bg-accent-500 hover:bg-accent-600 text-white font-semibold py-3 px-4 rounded-lg transition duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 focus:ring-offset-primary-900">
                <i class="fas fa-sign-in-alt mr-2"></i>Sign In
            </button>

            <!-- Back Home Button -->
            <a href="{{ url('/') }}"
                class="block w-full text-center bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-300 mt-3">
                <i class="fas fa-home mr-2"></i>Go Back Home
            </a>
        </form>

        <!-- Additional Options -->
        <div class="mt-6 text-center">
            <p class="text-primary-300 text-sm">
                Don't have an account?
                <a href="{{ route('pricing') }}" class="text-accent-400 hover:text-accent-300 font-medium transition duration-300">
                    Choose Plan & Register
                </a>
            </p>
        </div>
    </div>
</x-client.layout.guest>