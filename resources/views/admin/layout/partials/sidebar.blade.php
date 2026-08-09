@php($adminUser = $user)

<aside id="sidebar"
    class="sidebar bg-white w-64 h-screen shadow-md fixed left-0 top-0 overflow-y-auto z-50 md:relative md:translate-x-0 flex flex-col">
    <div class="p-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-primary-900 text-white p-2 rounded-lg">
                    <i class="fas fa-piggy-bank text-xl"></i>
                </div>
                <div>
                    <span
                        class="font-display font-bold text-lg text-primary-900">{{ $settings->site_name ?: config('app.name') }}</span>
                    <span class="block text-xs text-primary-500">{{ $settings->site_slogan }}</span>
                </div>
            </div>
            <button id="closeSidebar" type="button" class="md:hidden text-primary-500 hover:text-primary-700"
                aria-label="Close sidebar">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
    </div>

    <div class="p-4 flex-1">
        <nav class="space-y-1">
            <a href="{{ route('home') }}" target="_blank" rel="noopener"
                class="sidebar-link flex items-center space-x-3 px-3 py-2 rounded-lg">
                <i class="fas fa-home w-5 text-center"></i>
                <span>Visit Site</span>
            </a>

            @if ($adminUser?->can('view dashboard'))
                <a href="{{ route('admin.dashboard') }}"
                    class="sidebar-link flex items-center space-x-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt w-5 text-center"></i>
                    <span>Dashboard</span>
                </a>
            @endif

            @if ($adminUser?->can('view clients'))
                <x-sidebar-dropdown id="clients-dropdown" :active="request()->routeIs('admin.clients.*')" title="Clients" icon="fas fa-users"
                    :items="[
                        [
                            'url' => route('admin.clients.index'),
                            'label' => 'All Clients',
                            'permission' => 'view clients',
                        ],
                        [
                            'url' => route('admin.clients.create'),
                            'label' => 'Add New Client',
                            'permission' => 'create clients',
                        ],
                        [
                            'url' => route('admin.clients.index', ['status' => 'active']),
                            'label' => 'Active Clients',
                            'permission' => 'view clients',
                        ],
                        [
                            'url' => route('admin.clients.index', ['status' => 'inactive']),
                            'label' => 'Inactive Clients',
                            'permission' => 'view clients',
                        ],
                    ]" />
            @endif

            @if ($adminUser?->can('view packages'))
                <x-sidebar-dropdown id="packages-dropdown" :active="request()->routeIs('admin.packages.*')" title="Packages" icon="fas fa-box"
                    :items="[
                        [
                            'url' => route('admin.packages.index'),
                            'label' => 'All Packages',
                            'permission' => 'view packages',
                        ],
                        [
                            'url' => route('admin.packages.create'),
                            'label' => 'Add New Package',
                            'permission' => 'create packages',
                        ],
                        [
                            'url' => route('admin.packages.index', ['status' => 'active']),
                            'label' => 'Active Packages',
                            'permission' => 'view packages',
                        ],
                        [
                            'url' => route('admin.packages.index', ['status' => 'inactive']),
                            'label' => 'Inactive Packages',
                            'permission' => 'view packages',
                        ],
                    ]" />
            @endif

            @if ($adminUser?->can('view invoices'))
                <x-sidebar-dropdown id="invoices-dropdown" :active="request()->routeIs('admin.invoices.*')" title="Invoices"
                    icon="fas fa-file-invoice-dollar" :items="[
                        [
                            'url' => route('admin.invoices.index'),
                            'label' => 'All Invoices',
                            'permission' => 'view invoices',
                        ],
                        [
                            'url' => route('admin.invoices.create'),
                            'label' => 'Add New Invoice',
                            'permission' => 'create invoices',
                        ],
                        [
                            'url' => route('admin.invoices.index', ['status' => 'unpaid']),
                            'label' => 'Unpaid Invoices',
                            'permission' => 'view invoices',
                        ],
                        [
                            'url' => route('admin.invoices.index', ['status' => 'paid']),
                            'label' => 'Paid Invoices',
                            'permission' => 'view invoices',
                        ],
                        [
                            'url' => route('admin.invoices.index', ['status' => 'refund-request']),
                            'label' => 'Refund Requests',
                            'permission' => 'view invoices',
                        ],
                        [
                            'url' => route('admin.invoices.index', ['status' => 'refunded']),
                            'label' => 'Refunded Invoices',
                            'permission' => 'view invoices',
                        ],
                        [
                            'url' => route('admin.invoices.index', ['status' => 'cancelled']),
                            'label' => 'Cancelled Invoices',
                            'permission' => 'view invoices',
                        ],
                    ]" />
            @endif

            @if ($adminUser?->can('view tickets'))
                <x-sidebar-dropdown id="tickets-dropdown" :active="request()->routeIs('admin.tickets.*')" title="Tickets" icon="fas fa-ticket-alt"
                    :items="[
                        [
                            'url' => route('admin.tickets.index'),
                            'label' => 'All Tickets',
                            'permission' => 'view tickets',
                        ],
                        [
                            'url' => route('admin.tickets.create'),
                            'label' => 'Add New Ticket',
                            'permission' => 'create tickets',
                        ],
                        [
                            'url' => route('admin.tickets.index', ['status' => 'open']),
                            'label' => 'Open Tickets',
                            'permission' => 'view tickets',
                        ],
                        [
                            'url' => route('admin.tickets.index', ['status' => 'in_progress']),
                            'label' => 'In Progress Tickets',
                            'permission' => 'view tickets',
                        ],
                        [
                            'url' => route('admin.tickets.index', ['status' => 'resolved']),
                            'label' => 'Resolved Tickets',
                            'permission' => 'view tickets',
                        ],
                        [
                            'url' => route('admin.tickets.index', ['status' => 'closed']),
                            'label' => 'Closed Tickets',
                            'permission' => 'view tickets',
                        ],
                    ]" />
            @endif

            @if ($adminUser?->canAny(['view users', 'view roles', 'view user activities']))
                <x-sidebar-dropdown id="users-dropdown" :active="request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*')" title="Users & Roles"
                    icon="fas fa-user-shield" :items="[
                        ['url' => route('admin.users.index'), 'label' => 'All Users', 'permission' => 'view users'],
                        [
                            'url' => route('admin.users.create'),
                            'label' => 'Add New User',
                            'permission' => 'create users',
                        ],
                        [
                            'url' => route('admin.users.index', ['status' => 'active']),
                            'label' => 'Active Users',
                            'permission' => 'view users',
                        ],
                        [
                            'url' => route('admin.users.index', ['status' => 'inactive']),
                            'label' => 'Inactive Users',
                            'permission' => 'view users',
                        ],
                        ['url' => route('admin.roles.index'), 'label' => 'User Roles', 'permission' => 'view roles'],
                        [
                            'url' => route('admin.users.activities'),
                            'label' => 'User Activities',
                            'permission' => 'view user activities',
                        ],
                    ]" />
            @endif

            @if ($adminUser?->can('view settings'))
                <div class="pt-4 mt-4 border-t border-gray-200">
                    <a href="{{ route('admin.settings.general.edit') }}"
                        class="sidebar-link flex items-center space-x-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <i class="fas fa-cog w-5 text-center"></i>
                        <span>Settings</span>
                    </a>
                </div>
            @endif

            <div class="pt-4 mt-4 border-t border-gray-200">
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit"
                        class="sidebar-link flex items-center space-x-3 px-3 py-2 rounded-lg w-full text-left">
                        <i class="fas fa-sign-out-alt w-5 text-center"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </nav>
    </div>

    <div class="p-4 border-t border-gray-200 bg-gray-50 text-center">
        <p class="text-xs text-primary-600">&copy; {{ now()->year }} {{ $settings->site_name ?: config('app.name') }}
        </p>
        <p class="text-xs text-primary-500">System v2.0</p>
    </div>
</aside>
