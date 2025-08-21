  <aside id="sidebar"
      class="sidebar bg-white w-64 h-screen shadow-md fixed left-0 top-0 overflow-y-auto z-50 md:relative md:translate-x-0 flex flex-col">
      <div class="p-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                  <div class="bg-primary-900 text-white p-2 rounded-lg">
                      <i class="fas fa-piggy-bank text-xl"></i>
                  </div>
                  <div>
                      <span class="font-display font-bold text-lg text-primary-900">{{ $settings->site_name }}</span>
                      <span class="block text-xs text-primary-500">{{ $settings->site_slogan }}</span>
                  </div>
              </div>
              <button id="closeSidebar" class="md:hidden text-primary-500 hover:text-primary-700">
                  <i class="fas fa-times text-xl"></i>
              </button>
          </div>
      </div>

      <div class="p-4 flex-1">
          <div class="flex items-center space-x-3 mb-6">
              <img src="{{ $user->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}"
                  class="w-10 h-10 rounded-full border-2 border-accent-500" alt="User">
              <div>
                  <p class="font-medium text-primary-900">{{ $user->name }}</p>
                  {{-- <p class="text-xs text-primary-500">{{ $user->role }}</p> --}}
              </div>
          </div>

          {{-- <nav class="space-y-1">
              <a href="{{ route('admin.dashboard') }}"
                  class="sidebar-link  flex items-center space-x-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                  <i class="fas fa-tachometer-alt w-5 text-center"></i>
                  <span>Dashboard</span>
              </a>

              <!-- Client Dropdown -->
              <x-sidebar-dropdown id="clients-dropdown" :active="request()->routeIs('admin.clients.*')" title="Clients" :items="[
                  [
                      'url' => route('admin.clients.index'),
                      'label' => 'All Clients',
                  ],
                  [
                      'url' => route('admin.clients.create'),
                      'label' => 'Add New Clients',
                  ],
                  [
                      'url' => route('admin.clients.index', ['status' => 'active']),
                      'label' => 'Active Clients',
                  ],
                  [
                      'url' => route('admin.clients.index', ['status' => 'inactive']),
                      'label' => 'Inactive Clients',
                  ],
              ]" />
              <!-- Packages Dropdown -->
              <x-sidebar-dropdown id="packages-dropdown" :active="request()->routeIs('admin.packages.*')" title="Packages" :items="[
                  [
                      'url' => route('admin.packages.index'),
                      'label' => 'All Packages',
                  ],
                  [
                      'url' => route('admin.packages.create'),
                      'label' => 'Add New Packages',
                  ],
                  [
                      'url' => route('admin.packages.index', ['status' => 'active']),
                      'label' => 'Active Packages',
                  ],
                  [
                      'url' => route('admin.packages.index', ['status' => 'inactive']),
                      'label' => 'Inactive Packages',
                  ],
              ]" />

              <!-- Invoices Dropdown -->
              <x-sidebar-dropdown id="invoices-dropdown" :active="request()->routeIs('admin.invoices.*')" title="Invoices" :items="[
                  [
                      'url' => route('admin.invoices.index'),
                      'label' => 'All Invoices',
                  ],
                  [
                      'url' => route('admin.invoices.create'),
                      'label' => 'Add New Invoice',
                  ],
                  [
                      'url' => route('admin.invoices.index', ['status' => 'unpaid']),
                      'label' => 'Unpaid Invoices',
                  ],
                  [
                      'url' => route('admin.invoices.index', ['status' => 'paid']),
                      'label' => 'Paid Invoices',
                  ],
                  [
                      'url' => route('admin.invoices.index', ['status' => 'refunded']),
                      'label' => 'Refunded Invoices',
                  ],
                  [
                      'url' => route('admin.invoices.index', ['status' => 'cancelled']),
                      'label' => 'Cancelled Invoices',
                  ],
              ]" />

              <!-- Tickets Dropdown -->
              <x-sidebar-dropdown id="tickets-dropdown" :active="request()->routeIs('admin.tickets.*')" title="Tickets" :items="[
                  [
                      'url' => route('admin.tickets.index'),
                      'label' => 'All Tickets',
                  ],
                  [
                      'url' => route('admin.tickets.create'),
                      'label' => 'Add New Ticket',
                  ],
                  [
                      'url' => route('admin.tickets.index', ['status' => 'open']),
                      'label' => 'Open Tickets',
                  ],
                  [
                      'url' => route('admin.tickets.index', ['status' => 'in_progress']),
                      'label' => 'In Progress Tickets',
                  ],
                  [
                      'url' => route('admin.tickets.index', ['status' => 'resolved']),
                      'label' => 'Resolved Tickets',
                  ],
                  [
                      'url' => route('admin.tickets.index', ['status' => 'closed']),
                      'label' => 'Closed Tickets',
                  ],
              ]" />
              <!-- Users Dropdown -->
              <x-sidebar-dropdown id="users-dropdown" :active="request()->routeIs('admin.users.*')" title="Users" :items="[
                  [
                      'url' => route('admin.users.index'),
                      'label' => 'All Users',
                  ],
                  [
                      'url' => route('admin.roles.index'),
                      'label' => 'User Roles',
                  ],
                  [
                      'url' => route('admin.users.create'),
                      'label' => 'Add New User',
                  ],
                  [
                      'url' => route('admin.user.activities'),
                      'label' => 'User Activities',
                  ],
              ]" />

              <div class="pt-4 mt-4 border-t border-gray-200">
                  <a href="{{ route('admin.settings.general.edit') }}"
                      class="sidebar-link flex items-center space-x-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.settings.general.edit') ? 'active' : '' }}">
                      <i class="fas fa-cog w-5 text-center"></i>
                      <span>Settings</span>
                  </a>
                  <form method="POST" action="{{ route('admin.logout') }}">
                      @csrf
                      <button type="submit"
                          class="sidebar-link flex items-center space-x-3 px-3 py-2 rounded-lg w-full text-left">
                          <i class="fas fa-sign-out-alt w-5 text-center"></i>
                          <span>Logout</span>
                      </button>
                  </form>
              </div>
          </nav> --}}
          <nav class="space-y-1">

              @adminCan('view dashboard')
                  <a href="{{ route('admin.dashboard') }}"
                      class="sidebar-link flex items-center space-x-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                      <i class="fas fa-tachometer-alt w-5 text-center"></i>
                      <span>Dashboard</span>
                  </a>
              @endadminCan

              @adminCan('view clients')
                  <x-sidebar-dropdown id="clients-dropdown" :active="request()->routeIs('admin.clients.*')" title="Clients" :items="[
                      ['url' => route('admin.clients.index'), 'label' => 'All Clients'],
                      ['url' => route('admin.clients.create'), 'label' => 'Add New Clients'],
                      ['url' => route('admin.clients.index', ['status' => 'active']), 'label' => 'Active Clients'],
                      ['url' => route('admin.clients.index', ['status' => 'inactive']), 'label' => 'Inactive Clients'],
                  ]" />
              @endadminCan


              @adminCan('view packages')
                  <x-sidebar-dropdown id="packages-dropdown" :active="request()->routeIs('admin.packages.*')" title="Packages" :items="[
                      ['url' => route('admin.packages.index'), 'label' => 'All Packages'],
                      ['url' => route('admin.packages.create'), 'label' => 'Add New Packages'],
                      ['url' => route('admin.packages.index', ['status' => 'active']), 'label' => 'Active Packages'],
                      [
                          'url' => route('admin.packages.index', ['status' => 'inactive']),
                          'label' => 'Inactive Packages',
                      ],
                  ]" />
              @endadminCan

              @adminCan('view invoices')
                  <x-sidebar-dropdown id="invoices-dropdown" :active="request()->routeIs('admin.invoices.*')" title="Invoices" :items="[
                      ['url' => route('admin.invoices.index'), 'label' => 'All Invoices'],
                      ['url' => route('admin.invoices.create'), 'label' => 'Add New Invoice'],
                      ['url' => route('admin.invoices.index', ['status' => 'unpaid']), 'label' => 'Unpaid Invoices'],
                      ['url' => route('admin.invoices.index', ['status' => 'paid']), 'label' => 'Paid Invoices'],
                      [
                          'url' => route('admin.invoices.index', ['status' => 'refunded']),
                          'label' => 'Refunded Invoices',
                      ],
                      [
                          'url' => route('admin.invoices.index', ['status' => 'refund-request']),
                          'label' => 'Refund Request Invoices',
                      ],
                      [
                          'url' => route('admin.invoices.index', ['status' => 'cancelled']),
                          'label' => 'Cancelled Invoices',
                      ],
                  ]" />
              @endadminCan

              @adminCan('view tickets')
                  <x-sidebar-dropdown id="tickets-dropdown" :active="request()->routeIs('admin.tickets.*')" title="Tickets" :items="[
                      ['url' => route('admin.tickets.index'), 'label' => 'All Tickets'],
                      ['url' => route('admin.tickets.create'), 'label' => 'Add New Ticket'],
                      ['url' => route('admin.tickets.index', ['status' => 'open']), 'label' => 'Open Tickets'],
                      [
                          'url' => route('admin.tickets.index', ['status' => 'in_progress']),
                          'label' => 'In Progress Tickets',
                      ],
                      ['url' => route('admin.tickets.index', ['status' => 'resolved']), 'label' => 'Resolved Tickets'],
                      ['url' => route('admin.tickets.index', ['status' => 'closed']), 'label' => 'Closed Tickets'],
                  ]" />
              @endadminCan

              @adminCan('view users')
                  <x-sidebar-dropdown id="users-dropdown" :active="request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*')" title="Users" :items="[
                      ['url' => route('admin.users.index'), 'label' => 'All Users'],
                      ['url' => route('admin.users.create'), 'label' => 'Add New User'],
                      ['url' => route('admin.users.index', ['status' => 'active']), 'label' => 'Active Users'],
                      ['url' => route('admin.users.index', ['status' => 'inactive']), 'label' => 'Inactive Users'],
                      ['url' => route('admin.roles.index'), 'label' => 'User Roles'],
                      ['url' => route('admin.user.activities'), 'label' => 'User Activities'],
                  ]" />
              @endadminCan

              @adminCan('view settings')
                  <div class="pt-4 mt-4 border-t border-gray-200">
                      <a href="{{ route('admin.settings.general.edit') }}"
                          class="sidebar-link flex items-center space-x-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.settings.general.edit') ? 'active' : '' }}">
                          <i class="fas fa-cog w-5 text-center"></i>
                          <span>Settings</span>
                      </a>
                  </div>
              @endadminCan

              <!-- Logout button always visible -->
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

      <!-- Footer in Sidebar -->
      <div class="p-4 border-t border-gray-200 bg-gray-50">
          <div class="text-center">
              <p class="text-xs text-primary-600">
                  &copy; 2025 DYDS
              </p>
              <p class="text-xs text-primary-500">
                  System v2.0
              </p>
          </div>
      </div>
  </aside>
