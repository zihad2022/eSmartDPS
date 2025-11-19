<x-client.layout.app>
    <x-slot:title>Subscription Expired</x-slot:title>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-r from-accent-50 to-accent-100">
        
        <!-- Expired Subscription Card -->
        <div class="max-w-3xl w-full bg-white rounded-3xl shadow-2xl p-8 md:p-12 text-center relative overflow-hidden">
            
            <!-- Decorative Red Icon -->
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-accent-200 rounded-full flex items-center justify-center opacity-30">
                <i class="fa-solid fa-circle-exclamation text-accent-500 text-5xl"></i>
            </div>

            <!-- Heading -->
            <h1 class="text-3xl md:text-4xl font-bold text-accent-600 mb-4">
                Subscription Expired
            </h1>

            <!-- Subheading / Message -->
            <p class="text-gray-700 text-lg md:text-xl mb-8">
                Oops! Your subscription has ended. Without an active plan, access to premium features is disabled.
            </p>

            <!-- Call-to-Action Buttons -->
            <div class="flex flex-col md:flex-row justify-center gap-4">
                <!-- View Invoice Button -->
                {{-- <a href="{{ route('client.invoices.show', $invoice->id) }}"
                   class="flex-1 px-6 py-3 bg-accent-500 text-white rounded-xl font-semibold hover:bg-accent-600 transition duration-300">
                    View Invoice
                </a> --}}
            
                <!-- Return to Dashboard -->
                <a href="{{ route('client.dashboard') }}"
                   class="flex-1 px-6 py-3 border border-accent-500 text-accent-500 rounded-xl font-semibold hover:bg-accent-50 transition duration-300">
                    Return to Dashboard
                </a>
            
                <!-- View Plans -->
                <a href="{{ route('client.subscription.packages') }}"
                   class="flex-1 px-6 py-3 bg-yellow-500 text-white rounded-xl font-semibold hover:bg-yellow-600 transition duration-300">
                    View Plans
                </a>
            </div>
            

            <!-- Info Footer -->
            <p class="mt-8 text-sm text-gray-500">
                Need help? Contact our support team for assistance in renewing or upgrading your subscription.
            </p>
        </div>
    </div>
</x-client.layout.app>
