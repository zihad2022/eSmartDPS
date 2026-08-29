<x-client.layout.app>
    <x-slot:title>Subscription Expired</x-slot:title>

    <div class="min-h-[75vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full bg-white rounded-3xl shadow-xl border border-gray-100 p-8 sm:p-12 text-center relative overflow-hidden">
            
            <!-- Warning Header Icon -->
            <div class="mx-auto w-20 h-20 bg-red-50 rounded-2xl flex items-center justify-center mb-6 shadow-inner">
                <i class="fa-solid fa-lock text-red-500 text-3xl"></i>
            </div>

            <!-- Heading -->
            <h1 class="text-3xl font-extrabold text-primary-900 mb-3 font-display">
                Subscription Expired
            </h1>

            <!-- Subheading / Message -->
            <p class="text-primary-600 text-base max-w-lg mx-auto mb-8">
                Your subscription period has ended. Portal features and management tools are temporarily locked until your plan is renewed or a new plan is activated.
            </p>

            @if ($invoice)
                <!-- Pending Invoice Card -->
                <div class="bg-amber-50/80 border border-amber-200 rounded-2xl p-6 mb-8 text-left">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-amber-800 bg-amber-100 px-2.5 py-1 rounded-full">
                                Pending Renewal Invoice
                            </span>
                            <h3 class="text-lg font-bold text-primary-900 mt-2 font-display">
                                Invoice #{{ $invoice->invoice_number }}
                            </h3>
                            <p class="text-sm text-primary-600 mt-0.5">
                                Package: <span class="font-semibold text-primary-800">{{ $invoice->package_name }}</span>
                            </p>
                        </div>
                        <div class="sm:text-right">
                            <span class="text-xs text-primary-500 block">Amount Due</span>
                            <span class="text-2xl font-bold text-primary-900 font-mono">
                                {{ number_format($invoice->invoice_amount, 2) }}
                            </span>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-amber-200/60 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <span class="text-xs text-amber-900">
                            Due Date: <span class="font-semibold">{{ $invoice->due_date?->format('M d, Y') ?? 'Immediate' }}</span>
                        </span>
                        <a href="{{ route('client.payments.select', $invoice->id) }}"
                           class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-primary-900 hover:bg-primary-800 text-white font-semibold rounded-xl text-sm transition-all duration-200 shadow-sm">
                            <i class="fa-solid fa-credit-card mr-2"></i> Pay Invoice Now
                        </a>
                    </div>
                </div>
            @endif

            <!-- Call-to-Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                <!-- View Plans -->
                <a href="{{ route('client.subscription.packages') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3 bg-accent-600 hover:bg-accent-700 text-white rounded-xl font-semibold text-sm transition-all duration-200 shadow-md hover:shadow-lg">
                    <i class="fa-solid fa-layer-group mr-2"></i> Explore Subscription Plans
                </a>

                <!-- Logout Option -->
                <form action="{{ route('client.logout') }}" method="POST" class="w-full sm:w-auto">
                    @csrf
                    <button type="submit"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-primary-700 bg-white hover:bg-gray-50 rounded-xl font-semibold text-sm transition-all duration-200">
                        <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i> Sign Out
                    </button>
                </form>
            </div>

            <!-- Info Footer -->
            <p class="mt-8 text-xs text-primary-400">
                Need assistance? Contact your system administrator or support team to restore access.
            </p>
        </div>
    </div>
</x-client.layout.app>
