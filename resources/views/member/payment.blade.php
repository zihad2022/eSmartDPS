<x-member.layout.app>
    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <form action="{{ route('member.payment.store') }}" method="POST" enctype="multipart/form-data"
            class="space-y-10 bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            @csrf

            <!-- Payment Information -->
            <div>
                <div class="mb-8">
                    <h2 class="text-2xl font-display font-bold text-primary-900 mb-2">Payment Information</h2>
                    <p class="text-primary-600">Please provide details about your payment.</p>
                </div>

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-primary-700 mb-2">Payment Amount *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-primary-500">$</span>
                                <input type="number" name="payment_amount" step="0.01" min="0"
                                    class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-500 focus:border-transparent"
                                    placeholder="250.00" required>
                            </div>
                            {{-- <x-form.input name="payment_amount" type="number" step="0.01" min="0" placeholder="250.00" required /> --}}
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-primary-700 mb-2">Payment Date *</label>
                            <input type="date" name="payment_date"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-500 focus:border-transparent"
                                required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-primary-700 mb-2">Payment Method *</label>
                        <select name="payment_method"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-500 focus:border-transparent"
                            required>
                            <option value="">Select payment method</option>
                    
                            @foreach ($methods as $method)
                                <option value="{{ $method->value }}">{{ $method->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    

                    <div>
                        <label class="block text-sm font-medium text-primary-700 mb-2">Reference Number</label>
                        <input type="text" name="reference_number"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-500 focus:border-transparent"
                            placeholder="Transaction ID or reference number">
                        <p class="text-xs text-primary-500 mt-1">Optional: Enter transaction ID, check number, or
                            reference</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-primary-700 mb-2">Additional Notes</label>
                        <textarea name="payment_notes" rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-500 focus:border-transparent"
                            placeholder="Any additional information about this payment..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Upload Payment Receipt -->
            <div>
                <div class="mb-8">
                    <h2 class="text-2xl font-display font-bold text-primary-900 mb-2">Upload Payment Receipt</h2>
                    <p class="text-primary-600">Please upload a clear photo or scan of your payment receipt.</p>
                </div>

                <div class="space-y-6">
                    <input type="file" name="receipt_file" id="receiptFile" accept="image/*,.pdf"
                        class="block w-full text-sm text-gray-700" required>

                    <div class="bg-blue-50 rounded-lg p-4">
                        <h4 class="font-medium text-blue-900 mb-2">
                            <i class="fas fa-info-circle mr-2"></i>Upload Guidelines
                        </h4>
                        <ul class="text-sm text-blue-800 space-y-1">
                            <li>• Ensure the receipt is clearly visible and readable</li>
                            <li>• Include the full receipt showing amount, date, and payment method</li>
                            <li>• Avoid blurry or dark images</li>
                            <li>• File size should not exceed 5MB</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-between">
                <button type="reset"
                    class="bg-gray-100 text-primary-700 px-6 py-3 rounded-lg font-medium hover:bg-gray-200 transition duration-300">
                    <i class="fas fa-undo mr-2"></i>Reset
                </button>
                <button type="submit"
                    class="bg-accent-500 text-white px-8 py-3 rounded-lg font-medium hover:bg-accent-600 transition duration-300">
                    Submit Payment Proof <i class="fas fa-check ml-2"></i>
                </button>
            </div>
        </form>

        <!-- Step 3: Confirmation -->
        <div id="step3" class="hidden bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center">
            <div class="mb-8">
                <div
                    class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check text-3xl"></i>
                </div>
                <h2 class="text-2xl font-display font-bold text-primary-900 mb-2">Payment Proof Submitted Successfully!
                </h2>
                <p class="text-primary-600">Your payment proof has been received and is being reviewed by our team.</p>
            </div>

            <div class="bg-gray-50 rounded-lg p-6 mb-8">
                <h3 class="font-semibold text-primary-900 mb-4">Submission Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="text-left">
                        <span class="text-primary-500">Amount:</span>
                        <span class="font-medium text-primary-900 ml-2" id="confirmAmount"></span>
                    </div>
                    <div class="text-left">
                        <span class="text-primary-500">Date:</span>
                        <span class="font-medium text-primary-900 ml-2" id="confirmDate"></span>
                    </div>
                    <div class="text-left">
                        <span class="text-primary-500">Method:</span>
                        <span class="font-medium text-primary-900 ml-2" id="confirmMethod"></span>
                    </div>
                    <div class="text-left">
                        <span class="text-primary-500">Reference:</span>
                        <span class="font-medium text-primary-900 ml-2" id="confirmReference"></span>
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 rounded-lg p-4 mb-8">
                <h4 class="font-medium text-blue-900 mb-2">
                    <i class="fas fa-clock mr-2"></i>What happens next?
                </h4>
                <ul class="text-sm text-blue-800 space-y-1 text-left">
                    <li>• Our team will review your payment proof within 1-2 business days</li>
                    <li>• You will receive a confirmation email once approved</li>
                    <li>• Your account balance will be updated accordingly</li>
                    <li>• You can track the status in your member portal</li>
                </ul>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button onclick="window.location.href='member-portal.html'"
                    class="bg-accent-500 text-white px-8 py-3 rounded-lg font-medium hover:bg-accent-600 transition duration-300">
                    <i class="fas fa-home mr-2"></i>Back to Portal
                </button>
                <button onclick="submitAnother()"
                    class="bg-gray-100 text-primary-700 px-8 py-3 rounded-lg font-medium hover:bg-gray-200 transition duration-300">
                    <i class="fas fa-plus mr-2"></i>Submit Another
                </button>
            </div>
        </div>
    </main>
</x-member.layout.app>
