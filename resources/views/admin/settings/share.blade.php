<div id="shares" class="settings-content bg-white rounded-xl shadow-sm p-6 hidden">
    <h3 class="text-lg font-semibold text-primary-900 mb-6">Share Settings</h3>

    <form class="space-y-6">
        <div>
            <label class="block text-sm font-medium text-primary-700 mb-2">Share Price</label>
            <input type="number" value="250" step="0.01"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-500">
            <p class="text-sm text-primary-500 mt-1">Price per share in your selected currency</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-primary-700 mb-2">Minimum Shares</label>
            <input type="number" value="1" min="1"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-500">
            <p class="text-sm text-primary-500 mt-1">Minimum number of shares a member can purchase</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-primary-700 mb-2">Maximum Shares</label>
            <input type="number" value="50" min="1"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-500">
            <p class="text-sm text-primary-500 mt-1">Maximum number of shares a member can hold</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-primary-700 mb-2">Share Transfer Fee</label>
            <input type="number" value="5" step="0.01"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-500">
            <p class="text-sm text-primary-500 mt-1">Fee charged for transferring shares between members
            </p>
        </div>

        <div class="flex items-center">
            <input type="checkbox" id="allowPartialShares"
                class="w-4 h-4 text-accent-500 bg-gray-100 border-gray-300 rounded focus:ring-accent-500">
            <label for="allowPartialShares" class="ml-2 text-sm text-primary-700">Allow partial share
                purchases</label>
        </div>

        <button type="submit"
            class="bg-accent-500 hover:bg-accent-600 text-white px-6 py-2 rounded-lg transition duration-300">
            Save Changes
        </button>
    </form>
</div>
