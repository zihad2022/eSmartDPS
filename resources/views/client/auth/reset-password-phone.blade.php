<x-client.layout.guest>
    <div class="glass rounded-2xl p-8 login-card max-w-md mx-auto">
        <h2 class="text-2xl font-bold text-white text-center mb-6">Reset Password</h2>

        @if (session('status'))
            <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg mb-6 text-sm">
                {{ session('status') }}
            </div>
        @elseif(session('error'))
            <div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg mb-6 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('client.password.reset.store') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="phone" value="{{ $phone }}">

            <div>
                <label for="password" class="block text-sm font-medium text-primary-200 mb-2">New Password</label>
                <input type="password" name="password" id="password" required
                    class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-primary-300 focus:outline-none focus:ring-2 focus:ring-accent-500 transition duration-300"
                    placeholder="Enter new password">
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-primary-200 mb-2">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                    class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-primary-300 focus:outline-none focus:ring-2 focus:ring-accent-500 transition duration-300"
                    placeholder="Confirm new password">
            </div>

            <button type="submit"
                class="w-full bg-accent-500 hover:bg-accent-600 text-white font-semibold py-3 px-4 rounded-lg">
                Reset Password
            </button>

            <a href="{{ route('client.login') }}" class="block w-full text-center bg-gray-600 hover:bg-gray-700 text-white py-3 rounded-lg mt-3">
                Back to Login
            </a>
        </form>
    </div>
</x-client.layout.guest>
