<aside id="sidebar"
    class="sidebar bg-white w-64 h-screen shadow-md fixed left-0 top-0 overflow-y-auto z-50 md:relative md:translate-x-0 flex flex-col">

    @php
        /**
         * =======================================
         * Sidebar Setup: User, Routes, Role
         * =======================================
         */
        $authUser = Auth::guard('client')->user();
    @endphp

    {{-- ===========================
         Sidebar Header
         (Logo + App Name + Close Btn)
    ============================ --}}
    <div class="p-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                {{-- Logo Icon --}}
                <div class="bg-primary-900 text-white p-2 rounded-lg">
                    <i class="fas fa-piggy-bank text-xl"></i>
                </div>

                {{-- App Name --}}
                <div>
                    <span class="font-display font-bold text-lg text-primary-900">DYDS</span>
                    <span class="block text-xs text-primary-500">Savings Software</span>
                </div>
            </div>

            {{-- Close Button (Mobile only) --}}
            <button id="closeSidebar" class="md:hidden text-primary-500 hover:text-primary-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
    </div>

    {{-- ===========================
         Sidebar Body
    ============================ --}}
    <div class="p-4 flex-1">

        {{-- ---------------------------------------
             Logged-in User Info Section
        ---------------------------------------- --}}
        <div class="flex items-center space-x-3 mb-6">
            <img src="https://randomuser.me/api/portraits/men/32.jpg"
                class="w-10 h-10 rounded-full border-2 border-accent-500" alt="User">
            <div>
                <p class="font-medium text-primary-900">{{ $authUser->first_name . ' ' . $authUser->last_name }}</p>
                <p class="text-xs text-primary-500">{{ ucfirst($authUser->role) }}</p>
            </div>
        </div>

        {{-- ---------------------------------------
             Navigation Menu
        ---------------------------------------- --}}
        <nav class="space-y-1">

            {{-- Dashboard --}}
            <a href="{{ route('client.dashboard') }}"
                class="{{ request()->routeIs('client.dashboard') ? 'active' : '' }} sidebar-link flex items-center space-x-3 px-3 py-2 rounded-lg">
                <i class="fas fa-tachometer-alt w-5 text-center"></i>
                <span>Dashboard</span>
            </a>

            {{-- ===========================
                 Members Menu
            ============================ --}}
            <x-sidebar-dropdown id="members-dropdown" :active="request()->routeIs('client.members.*')"
                title="Members" :icon="'fas fa-users'" :items="[
                    ['url' => route('client.members.index'), 'label' => 'All Members'],
                    ['url' => route('client.members.create'), 'label' => 'Add New Member'],
                    ['url' => route('client.members.index', ['status' => 'active']), 'label' => 'Active Members'],
                    ['url' => route('client.members.index', ['status' => 'inactive']), 'label' => 'Inactive Members'],
                ]" />

            {{-- ===========================
                 Projects Menu
            ============================ --}}
            <x-sidebar-dropdown id="projects-dropdown"
                :active="request()->routeIs('client.projects.*') || request()->routeIs('client.project-categories.*')"
                title="Projects" :icon="'fas fa-tasks'" :items="[
                    ['url' => route('client.project-categories.index'), 'label' => 'All Categories'],
                    ['url' => route('client.projects.index'), 'label' => 'All Projects'],
                    ['url' => route('client.projects.create'), 'label' => 'Add New Project'],
                    ['url' => route('client.projects.index', ['status' => 'active']), 'label' => 'Active Projects'],
                    ['url' => route('client.projects.index', ['status' => 'cancelled']), 'label' => 'Cancelled Projects'],
                    ['url' => route('client.projects.index', ['status' => 'completed']), 'label' => 'Completed Projects'],
                ]" />

            {{-- ===========================
                 Payments Menu
            ============================ --}}
            <x-sidebar-dropdown id="payments-dropdown" :active="request()->routeIs('client.payments.*')"
                title="Payments" :icon="'fas fa-money-bill'" :items="[
                    ['url' => route('client.payments.index'), 'label' => 'All Payments'],
                    ['url' => route('client.payments.index', ['status' => App\Enums\PaymentStatus::PENDING->value]), 'label' => 'Pending Payments'],
                    ['url' => route('client.payments.index', ['status' => App\Enums\PaymentStatus::DUE->value]), 'label' => 'Due Payments'],
                    ['url' => route('client.payments.index', ['status' => App\Enums\PaymentStatus::PAID->value]), 'label' => 'Paid Payments'],
                    ['url' => route('client.payments.index', ['status' => App\Enums\PaymentStatus::CANCELLED->value]), 'label' => 'Cancelled Payments'],
                ]" />

            {{-- ===========================
                 Ledgers Menu
            ============================ --}}
            <x-sidebar-dropdown id="ledgers-dropdown"
                :active="request()->routeIs('client.ledgers.*') || request()->routeIs('client.ledger-categories.*')"
                title="Ledgers" :icon="'fas fa-book'" :items="[
                    ['url' => route('client.ledgers.index'), 'label' => 'All Ledgers'],
                    ['url' => route('client.ledgers.create'), 'label' => 'Ledger Entry'],
                    ['url' => route('client.ledgers.index', ['type' => 'income']), 'label' => 'Ledger Income'],
                    ['url' => route('client.ledgers.index', ['type' => 'expense']), 'label' => 'Ledger Expenses'],
                    ['url' => route('client.ledger-categories.index'), 'label' => 'Ledger Categories'],
                    ['url' => route('client.ledgers.report'), 'label' => 'Ledger Reports'],
                ]" />

            {{-- ===========================
                 Tickets Menu
            ============================ --}}
            <x-sidebar-dropdown id="tickets-dropdown" :active="request()->routeIs('client.tickets.*')"
                title="Tickets" :icon="'fas fa-ticket-alt'" :items="[
                    ['url' => route('client.tickets.index'), 'label' => 'All Tickets'],
                    ['url' => route('client.tickets.create'), 'label' => 'Add New Ticket'],
                    ['url' => route('client.tickets.index', ['status' => 'open']), 'label' => 'Open Tickets'],
                    ['url' => route('client.tickets.index', ['status' => 'in_progress']), 'label' => 'In Progress Tickets'],
                    ['url' => route('client.tickets.index', ['status' => 'resolved']), 'label' => 'Resolved Tickets'],
                    ['url' => route('client.tickets.index', ['status' => 'closed']), 'label' => 'Closed Tickets'],
                ]" />

            {{-- ===========================
                 Users Menu
            ============================ --}}
            <x-sidebar-dropdown id="users-dropdown" :active="request()->routeIs('client.users.*')"
                title="Users" :icon="'fas fa-user-shield'" :items="[
                    ['url' => route('client.users.index'), 'label' => 'All Users'],
                    ['url' => route('client.users.create'), 'label' => 'Add New User'],
                    ['url' => route('client.users.index', ['status' => 'active']), 'label' => 'Active Users'],
                    ['url' => route('client.users.index', ['status' => 'inactive']), 'label' => 'Inactive Users'],
                    ['url' => route('client.users.activities'), 'label' => 'User Activities'],
                ]" />

            {{-- ===========================
                 Billing Menu
            ============================ --}}
            <div class="pt-4 mt-4 border-t border-gray-200">
                <x-sidebar-dropdown id="billing-dropdown"
                    :active="request()->routeIs('client.subscription.*') || request()->routeIs('client.invoices.*')"
                    title="Billing" :icon="'fas fa-credit-card'" :items="[
                        ['url' => route('client.subscription.packages'), 'label' => 'Subscription Packages'],
                        ['url' => route('client.invoices.index'), 'label' => 'All Invoices'],
                        ['url' => route('client.invoices.index', ['status' => 'unpaid']), 'label' => 'Unpaid Invoices'],
                        ['url' => route('client.invoices.index', ['status' => 'paid']), 'label' => 'Paid Invoices'],
                    ]" />

                {{-- Settings (only Super Admins) --}}
                @if ($authUser->hasRole(['super-admin', 'admin']))
                    <a href="{{ route('client.settings.general.edit') }}"
                        class="sidebar-link flex items-center space-x-3 px-3 py-2 rounded-lg">
                        <i class="fas fa-cog w-5 text-center"></i>
                        <span>Settings</span>
                    </a>
                @endif

                {{-- Logout --}}
                <form action="{{ route('client.logout') }}" method="POST" id="logoutForm" class="hidden">@csrf</form>
                <a href="#" onclick="document.getElementById('logoutForm').submit();"
                    class="sidebar-link flex items-center space-x-3 px-3 py-2 rounded-lg">
                    <i class="fas fa-sign-out-alt w-5 text-center"></i>
                    <span>Logout</span>
                </a>
            </div>
        </nav>
    </div>

    {{-- ===========================
         Sidebar Footer
    ============================ --}}
    <div class="p-4 border-t border-gray-200 bg-gray-50">
        <div class="text-center">
            <p class="text-xs text-primary-600">&copy; 2025 DYDS</p>
            <p class="text-xs text-primary-500">System v2.0</p>
        </div>
    </div>
</aside>
