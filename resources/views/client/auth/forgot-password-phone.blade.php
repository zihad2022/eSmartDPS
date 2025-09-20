<x-client.layout.guest>
    <div class="glass rounded-2xl p-8 login-card max-w-md mx-auto">
        <h2 class="text-2xl font-bold text-white text-center mb-6">Forgot Password</h2>
        
        <!-- Success / Error Message -->
        @if (session('status'))
            <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg mb-6 text-sm">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('status') }}
            </div>
        @elseif(session('error'))
            <div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg mb-6 text-sm">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                {{ session('error') }}
            </div>
        @endif

        {{-- {{ route('client.password.phone.send') }} --}}
        <form method="POST" action="{{ route('client.forgot.password.phone.store') }}" class="space-y-6">
            @csrf

            <!-- Phone Number Field -->
            <div>
                <label for="phone" class="block text-sm font-medium text-primary-200 mb-2">
                    <i class="fas fa-phone mr-2"></i>Phone Number
                </label>
                <input type="tel" id="phone" name="phone" required
                    class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-primary-300 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:border-transparent transition duration-300"
                    placeholder="Enter your phone number">
                @error('phone')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="w-full bg-accent-500 hover:bg-accent-600 text-white font-semibold py-3 px-4 rounded-lg transition duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 focus:ring-offset-primary-900">
                <i class="fas fa-mobile-alt mr-2"></i>Send OTP
            </button>

            <!-- Back to Login Button -->
            <a href="{{ route('client.login') }}"
                class="block w-full text-center bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-300 mt-3">
                <i class="fas fa-arrow-left mr-2"></i>Back to Login
            </a>
        </form>

        <!-- Additional Info -->
        <div class="mt-6 text-center">
            <p class="text-primary-300 text-sm">
                Remember your password?
                <a href="{{ route('client.login') }}" class="text-accent-400 hover:text-accent-300 font-medium transition duration-300">
                    Sign In
                </a>
            </p>
        </div>
    </div>
</x-client.layout.guest>
