<header class="gradient-bg text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center space-x-3">
                <div class="bg-white/20 text-white p-2 rounded-lg">
                    <i class="fas fa-piggy-bank text-xl"></i>
                </div>
                <div>
                    <span class="font-display font-bold text-xl">ESMARTDPS</span>
                    <span class="block text-xs text-white/80">Member Portal</span>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <div class="hidden md:flex items-center space-x-3">
                    <img src="https://randomuser.me/api/portraits/men/32.jpg"
                        class="w-8 h-8 rounded-full border-2 border-white/30" alt="Member">
                    <div>
                        <span class="text-sm font-medium"
                            id="memberName">{{ Auth::guard('member')->user()->name }}</span>
                        <span class="block text-xs text-white/80" id="memberIdDisplay">ID:
                            {{ Auth::guard('member')->user()->member_id }}</span>
                    </div>
                </div>
                <button onclick="submitPaymentProof()"
                    class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition duration-300">
                    <i class="fas fa-upload mr-2"></i>Submit Payment
                </button>
                <form method="POST" action="{{ route('member.logout') }}" class="inline">
                    @csrf
                    <button type="submit"
                        class="bg-white/20 hover:bg-white/30 text-white px-3 py-2 rounded-lg transition duration-300">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>

            </div>
        </div>
    </div>
</header>
