<div id="notifications" class="settings-content bg-white rounded-xl shadow-sm p-6 hidden">
    <h3 class="text-lg font-semibold text-primary-900 mb-6">Notification Settings</h3>

    <form class="space-y-6">
        <div>
            <label class="block text-sm font-medium text-primary-700 mb-2">SMS API Provider</label>
            <select
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-500">
                <option value="">Select SMS Provider</option>
                <option value="twilio">Twilio</option>
                <option value="nexmo">Nexmo</option>
                <option value="africastalking">Africa's Talking</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-primary-700 mb-2">SMS API Key</label>
            <input type="password" placeholder="Enter your SMS API key"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-primary-700 mb-2">Email Notifications</label>
            <div class="space-y-2">
                <div class="flex items-center">
                    <input type="checkbox" id="emailPayments" checked
                        class="w-4 h-4 text-accent-500 bg-gray-100 border-gray-300 rounded focus:ring-accent-500">
                    <label for="emailPayments" class="ml-2 text-sm text-primary-700">Payment
                        confirmations</label>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="emailReminders" checked
                        class="w-4 h-4 text-accent-500 bg-gray-100 border-gray-300 rounded focus:ring-accent-500">
                    <label for="emailReminders" class="ml-2 text-sm text-primary-700">Payment
                        reminders</label>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="emailReports"
                        class="w-4 h-4 text-accent-500 bg-gray-100 border-gray-300 rounded focus:ring-accent-500">
                    <label for="emailReports" class="ml-2 text-sm text-primary-700">Monthly
                        reports</label>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-primary-700 mb-2">SMS Notifications</label>
            <div class="space-y-2">
                <div class="flex items-center">
                    <input type="checkbox" id="smsPayments"
                        class="w-4 h-4 text-accent-500 bg-gray-100 border-gray-300 rounded focus:ring-accent-500">
                    <label for="smsPayments" class="ml-2 text-sm text-primary-700">Payment
                        confirmations</label>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="smsReminders" checked
                        class="w-4 h-4 text-accent-500 bg-gray-100 border-gray-300 rounded focus:ring-accent-500">
                    <label for="smsReminders" class="ml-2 text-sm text-primary-700">Payment
                        reminders</label>
                </div>
            </div>
        </div>

        <button type="submit"
            class="bg-accent-500 hover:bg-accent-600 text-white px-6 py-2 rounded-lg transition duration-300">
            Save Changes
        </button>
    </form>
</div>