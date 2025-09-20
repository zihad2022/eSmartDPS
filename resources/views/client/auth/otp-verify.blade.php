<x-client.layout.guest>
    <div class="glass rounded-2xl p-8 login-card max-w-md mx-auto">
        <h2 class="text-2xl font-bold text-white text-center mb-6">Verify OTP</h2>

        <!-- Success / Error Message -->
        @if (session('success'))
            <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg mb-6 text-sm">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @elseif(session('error'))
            <div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg mb-6 text-sm">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('client.otp.verify.post', ['phone' => request('phone')]) }}" class="space-y-6">
            @csrf

            <input type="hidden" name="phone" value="{{ request('phone') }}">

            <!-- OTP Field -->
            <div>
                <label for="otp" class="block text-sm font-medium text-primary-200 mb-2">
                    <i class="fas fa-key mr-2"></i>Enter OTP
                </label>
                <input type="text" id="otp" name="otp" required
                    class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-primary-300 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:border-transparent transition duration-300"
                    placeholder="Enter the OTP sent to your phone">
                @error('otp')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="w-full bg-accent-500 hover:bg-accent-600 text-white font-semibold py-3 px-4 rounded-lg transition duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 focus:ring-offset-primary-900">
                <i class="fas fa-check mr-2"></i>Verify OTP
            </button>

            <!-- Resend OTP -->
            <a href="{{ route('client.forgot.password.phone.store') }}"
                class="block w-full text-center bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-300 mt-3">
                <i class="fas fa-redo-alt mr-2"></i>Resend OTP
            </a>

            <!-- Back to Login -->
            <a href="{{ route('client.login') }}"
                class="block w-full text-center bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-300 mt-3">
                <i class="fas fa-arrow-left mr-2"></i>Back to Login
            </a>
        </form>

        <!-- Additional Info -->
        <div class="mt-6 text-center">
            <p class="text-primary-300 text-sm">
                Didn't receive the OTP? Click "Resend OTP" above or wait a few minutes.
            </p>
        </div>
    </div>
</x-client.layout.guest>
