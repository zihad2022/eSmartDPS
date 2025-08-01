<x-client.layout.app>
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-2xl w-full bg-white rounded-2xl shadow-sm p-6 text-center">
            <h2 class="text-xl font-semibold text-primary-900 mb-4">
                Subscription Expired
            </h2>

            <p class="text-gray-700 text-sm mb-6">
                Your subscription has ended. To regain access, please renew or purchase a new plan.
            </p>
            <div class="flex flex-col md:flex-row justify-center gap-4">
                <a href=""
                    class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition duration-300">
                    View Subscription Plans
                </a>

                <a href="{{ route('client.dashboard') }}"
                    class="px-4 py-2 border border-gray-300 text-primary-700 rounded-lg hover:bg-gray-50 text-sm transition duration-300">
                    Return to Dashboard
                </a>
                <a href="{{ route('client.subscription.renew') }}"
                    class="px-4 py-2 border border-gray-300 text-primary-700 rounded-lg hover:bg-gray-50 text-sm transition duration-300">
                    Renew Subscription
                </a>
            </div>
        </div>
    </div>
</x-client.layout.app>
