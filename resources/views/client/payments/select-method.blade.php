<x-client.layout.app>
    <x-slot:title>Checkout Invoice #{{ $invoice->invoice_number }}</x-slot:title>

    @php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('client.dashboard')],
            ['label' => 'Packages', 'url' => route('client.subscription.packages')],
            ['label' => 'Checkout', 'url' => route('client.payments.select', $invoice->id)],
        ];

        $currency = $settings->currency ?? 'BDT';
    @endphp

    <x-breadcrumb :items="$breadcrumbItems" />

    <div class="max-w-4xl mx-auto my-6">
        
        @if (session('error') || session('info'))
            <div class="mb-6">
                <x-flash-message
                    :type="session('error') ? 'error' : 'info'"
                    :title="session('error') ? 'Payment Notice' : 'Notice'"
                    :message="session('error') ?? session('info')"
                />
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
            
            {{-- Order Summary (Left Column - 2 cols) --}}
            <div class="md:col-span-2 bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 mb-4">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span>Package Subscription</span>
                    </div>

                    <h3 class="text-xl font-bold text-slate-900 mb-1">
                        {{ $invoice->package_name ?: ($invoice->package->name ?? 'Subscription Plan') }}
                    </h3>
                    <p class="text-xs text-slate-500 mb-6">
                        Invoice #{{ $invoice->invoice_number }}
                    </p>

                    <div class="space-y-3 border-t border-b border-slate-100 py-4 text-xs">
                        @if ($invoice->billing_start && $invoice->billing_end)
                            <div class="flex justify-between items-center text-slate-600">
                                <span>Billing Period</span>
                                <span class="font-medium text-slate-900">
                                    {{ $invoice->billing_start->format('d M') }} - {{ $invoice->billing_end->format('d M Y') }}
                                </span>
                            </div>
                        @endif

                        @if ($invoice->package)
                            <div class="flex justify-between items-center text-slate-600">
                                <span>Member Capacity</span>
                                <span class="font-medium text-slate-900">{{ $invoice->package->member_limit ?: 'Unlimited' }}</span>
                            </div>
                            <div class="flex justify-between items-center text-slate-600">
                                <span>Project Capacity</span>
                                <span class="font-medium text-slate-900">{{ $invoice->package->project_limit ?: 'Unlimited' }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="mt-4 space-y-2 text-xs">
                        <div class="flex justify-between text-slate-600">
                            <span>Plan Fee</span>
                            <span class="font-medium font-mono text-slate-900">
                                {{ $currency }} {{ number_format($invoice->invoice_amount) }}
                            </span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Gateway Processing</span>
                            <span class="text-emerald-600 font-medium">Free</span>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-4 border-t border-slate-200">
                    <div class="flex items-baseline justify-between">
                        <span class="text-sm font-semibold text-slate-700">Total Payable</span>
                        <span class="text-2xl font-extrabold font-mono text-slate-900">
                            {{ $currency }} {{ number_format($invoice->invoice_amount) }}
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Instant plan activation upon payment completion</p>
                </div>
            </div>

            {{-- Payment Method Selection (Right Column - 3 cols) --}}
            <div class="md:col-span-3 space-y-6">
                
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
                    <h2 class="text-lg font-bold text-slate-900 mb-1">Select Payment Gateway</h2>
                    <p class="text-xs text-slate-500 mb-6">Choose your preferred payment method to complete the transaction.</p>

                    <form action="{{ route('client.payments.process', $invoice->id) }}" method="POST">
                        @csrf

                        <div class="space-y-3">
                            @foreach($paymentMethods as $key => $label)
                                @if ($key === 'bkash')
                                    <label class="relative flex items-center p-4 rounded-xl border-2 border-pink-500/80 bg-pink-50/20 hover:bg-pink-50/40 cursor-pointer transition-all duration-200 group">
                                        <input type="radio" name="payment_method" value="bkash" class="text-pink-600 focus:ring-pink-500 mr-3.5" checked required>
                                        
                                        <div class="flex-1 flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#D12053] to-[#E2136E] text-white flex items-center justify-center font-bold text-sm shadow-md shadow-pink-500/20">
                                                    bKash
                                                </div>
                                                <div>
                                                    <span class="block text-sm font-bold text-slate-900 group-hover:text-pink-600 transition-colors">
                                                        bKash Direct Checkout
                                                    </span>
                                                    <span class="block text-xs text-slate-500">
                                                        Pay via bKash Wallet / OTP
                                                    </span>
                                                </div>
                                            </div>

                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-pink-100 text-pink-700 border border-pink-200">
                                                Instant
                                            </span>
                                        </div>
                                    </label>
                                @elseif ($key === 'sslcommerz')
                                    <label class="relative flex items-center p-4 rounded-xl border border-slate-200 hover:border-slate-300 hover:bg-slate-50/60 cursor-pointer transition-all duration-200 group">
                                        <input type="radio" name="payment_method" value="sslcommerz" class="text-emerald-600 focus:ring-emerald-500 mr-3.5" required>
                                        
                                        <div class="flex-1 flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold text-xs">
                                                    Cards
                                                </div>
                                                <div>
                                                    <span class="block text-sm font-bold text-slate-900">
                                                        SSLCommerz Gateway
                                                    </span>
                                                    <span class="block text-xs text-slate-500">
                                                        Cards, Internet Banking & Other MFS
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                @endif
                            @endforeach
                        </div>

                        <div class="mt-6">
                            <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-pink-600 to-rose-600 hover:from-pink-700 hover:to-rose-700 text-white py-3 px-6 rounded-xl font-bold shadow-lg shadow-pink-500/25 transition-all text-sm transform hover:-translate-y-0.5">
                                <i class="fas fa-lock text-xs"></i>
                                <span>Proceed to bKash Checkout ({{ $currency }} {{ number_format($invoice->invoice_amount) }})</span>
                            </button>
                        </div>
                    </form>
                </div>

                {{-- bKash Sandbox Testing Instructions Box --}}
                <div class="rounded-2xl p-5 bg-gradient-to-r from-slate-900 to-slate-800 text-white border border-slate-700 shadow-xs text-xs">
                    <div class="flex items-center space-x-2 text-pink-400 font-bold mb-2">
                        <i class="fas fa-flask text-sm"></i>
                        <span>Sandbox Test Account Guide</span>
                    </div>
                    <p class="text-slate-300 text-[11px] mb-3">
                        Use any of the following verified sandbox wallets to test checkout without real money:
                    </p>
                    <div class="grid grid-cols-2 gap-2 text-[11px] bg-white/5 rounded-xl p-3 border border-white/10 font-mono">
                        <div>
                            <span class="text-slate-400 block text-[10px]">Test Wallet</span>
                            <span class="text-emerald-400 font-semibold">01770618576</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px]">Alternate Wallet</span>
                            <span class="text-slate-200">01770618575</span>
                        </div>
                        <div class="mt-1">
                            <span class="text-slate-400 block text-[10px]">OTP</span>
                            <span class="text-amber-400 font-semibold">123456</span>
                        </div>
                        <div class="mt-1">
                            <span class="text-slate-400 block text-[10px]">PIN</span>
                            <span class="text-amber-400 font-semibold">12121</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-client.layout.app>
