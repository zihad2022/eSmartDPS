  <aside id="sidebar"
      class="sidebar bg-white w-64 h-screen shadow-md fixed left-0 top-0 overflow-y-auto z-50 md:relative md:translate-x-0 flex flex-col">
      <div class="p-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                  <div class="bg-primary-900 text-white p-2 rounded-lg">
                      <i class="fas fa-piggy-bank text-xl"></i>
                  </div>
                  <div>
                      <span class="font-display font-bold text-lg text-primary-900">DYDS</span>
                      <span class="block text-xs text-primary-500">Savings Software</span>
                  </div>
              </div>
              <button id="closeSidebar" class="md:hidden text-primary-500 hover:text-primary-700">
                  <i class="fas fa-times text-xl"></i>
              </button>
          </div>
      </div>

      <div class="p-4 flex-1">
          <div class="flex items-center space-x-3 mb-6">
              <img src="https://randomuser.me/api/portraits/men/32.jpg"
                  class="w-10 h-10 rounded-full border-2 border-accent-500" alt="User">
              <div>
                  <p class="font-medium text-primary-900">John Smith</p>
                  <p class="text-xs text-primary-500">Administrator</p>
              </div>
          </div>

          <nav class="space-y-1">
              <a href="{{ route('client.dashboard') }}"
                  class="{{ request()->routeIs('client.dashboard') ? 'active' : '' }} sidebar-link flex items-center space-x-3 px-3 py-2 rounded-lg">
                  <i class="fas fa-tachometer-alt w-5 text-center"></i>
                  <span>Dashboard</span>
              </a>

              <!-- Shares Dropdown -->
              <x-sidebar-dropdown id="shares-dropdown" :active="request()->routeIs('client.shares.*')" title="Shares" :icon="'fas fa-chart-line'"
                  :items="[
                      [
                          'url' => route('client.shares.index'),
                          'label' => 'All Shares',
                      ],
                      [
                          'url' => route('client.shares.create'),
                          'label' => 'Add New Share',
                      ],
                  ]" />
              <!-- Members Dropdown -->
              <x-sidebar-dropdown id="members-dropdown" :active="request()->routeIs('client.members.*')" title="Members" :icon="'fas fa-users'"
                  :items="[
                      [
                          'url' => route('client.members.index'),
                          'label' => 'All Members',
                      ],
                      [
                          'url' => route('client.members.create'),
                          'label' => 'Add New Member',
                      ],
                      [
                          'url' => route('client.members.index', ['status' => 'active']),
                          'label' => 'Active Members',
                      ],
                      [
                          'url' => route('client.members.index', ['status' => 'inactive']),
                          'label' => 'Inactive Members',
                      ],
                  ]" />
              <!-- Project Category Dropdown -->
              <x-sidebar-dropdown id="project-category-dropdown" :active="request()->routeIs('client.project-categories.*')" title="Project Category"
                  :icon="'fas fa-project-diagram'" :items="[
                      [
                          'url' => route('client.project-categories.index'),
                          'label' => 'All Project Category',
                      ],
                      [
                          'url' => route('client.project-categories.create'),
                          'label' => 'Add New Project Category',
                      ],
                  ]" />
              <!-- Projects Dropdown -->
              @php
                  use App\Enums\ProjectStatus;
              @endphp
              <x-sidebar-dropdown id="projects-dropdown" :active="request()->routeIs('client.projects.*')" title="Projects" :icon="'fas fa-tasks'"
                  :items="[
                      [
                          'url' => route('client.projects.index'),
                          'label' => 'All Projects',
                      ],
                      [
                          'url' => route('client.projects.create'),
                          'label' => 'Add New Project',
                      ],
                      [
                          'url' => route('client.projects.index', ['status' => ProjectStatus::ACTIVE->value]),
                          'label' => 'Active Projects',
                      ],
                      [
                          'url' => route('client.projects.index', ['status' => ProjectStatus::CANCELLED->value]),
                          'label' => 'Cancelled Projects',
                      ],
                      [
                          'url' => route('client.projects.index', ['status' => ProjectStatus::COMPLETED->value]),
                          'label' => 'Completed Projects',
                      ],
                  ]" />


              <!-- Payments Dropdown -->
              @php
                  use App\Enums\PaymentStatus;
              @endphp
              <x-sidebar-dropdown id="payments-dropdown" :active="request()->routeIs('client.payments.*')" title="Payments" :icon="'fas fa-money-bill'"
                  :items="[
                      [
                          'url' => route('client.payments.index'),
                          'label' => 'All Payments',
                      ],
                      [
                          'url' => route('client.payments.index', ['status' => PaymentStatus::PENDING->value]),
                          'label' => 'Pending Payments',
                      ],
                      [
                          'url' => route('client.payments.index', ['status' => PaymentStatus::DUE->value]),
                          'label' => 'Due Payments',
                      ],
                      [
                          'url' => route('client.payments.index', ['status' => PaymentStatus::PAID->value]),
                          'label' => 'Paid Payments',
                      ],
                      [
                          'url' => route('client.payments.index', ['status' => PaymentStatus::CANCELLED->value]),
                          'label' => 'Cancelled Payments',
                      ],
                  ]" />
              <!-- Ledgers Dropdown -->
              <x-sidebar-dropdown id="ledgers-dropdown" title="Ledgers" icon="fas fa-book" :items="[
                  ['url' => route('client.ledgers.index', ['type' => 'entry']), 'label' => 'Ledger Entry'],
                  ['url' => route('client.ledgers.index', ['type' => 'income']), 'label' => 'Ledger Income'],
                  ['url' => route('client.ledgers.index', ['type' => 'expenses']), 'label' => 'Ledger Expenses'],
                  ['url' => route('client.ledger-categories.index'), 'label' => 'Ledger Categories'],
                  ['url' => route('client.ledgers.index', ['type' => 'reports']), 'label' => 'Ledger Reports'],
              ]" />
              <!-- Tickets Dropdown -->
              <div>
                  <button
                      class="sidebar-link dropdown-toggle flex items-center justify-between w-full px-3 py-2 rounded-lg"
                      data-target="tickets-dropdown">
                      <div class="flex items-center space-x-3">
                          <i class="fas fa-book w-5 text-center"></i>
                          <span>Tickets</span>
                      </div>
                      <i class="fas fa-chevron-down text-xs transition-transform"></i>
                  </button>
                  <div id="tickets-dropdown" class="dropdown-menu ml-8 mt-1 space-y-1">
                      <a href="ledgers.html?type=income"
                          class="block px-3 py-2 text-sm text-primary-600 hover:text-accent-600 rounded">All
                          Tickets</a>
                      <a href="ledgers.html?type=expenses"
                          class="block px-3 py-2 text-sm text-primary-600 hover:text-accent-600 rounded">Add New
                          Ticket</a>
                      <a href="ledgers.html?type=reports"
                          class="block px-3 py-2 text-sm text-primary-600 hover:text-accent-600 rounded">Pending
                          Tickets</a>
                      <a href="ledgers.html?type=reports"
                          class="block px-3 py-2 text-sm text-primary-600 hover:text-accent-600 rounded">Active
                          Tickets</a>
                      <a href="ledgers.html?type=categories"
                          class="block px-3 py-2 text-sm text-primary-600 hover:text-accent-600 rounded">Closed
                          Tickets</a>
                  </div>
              </div>

              <!-- Users Dropdown -->
              @if (in_array(Auth::guard('client')->user()->role, ['admin', 'manager']))
                  <div>
                      <button
                          class="sidebar-link dropdown-toggle flex items-center justify-between w-full px-3 py-2 rounded-lg {{ request()->routeIs('client.users.*') ? 'active' : '' }}"
                          data-target="users-dropdown">
                          <div class="flex items-center space-x-3">
                              <i class="fas fa-user-shield w-5 text-center"></i>
                              <span>Users</span>
                          </div>
                          <i class="fas fa-chevron-down text-xs transition-transform"></i>
                      </button>
                      <div id="users-dropdown"
                          class="dropdown-menu ml-8 mt-1 space-y-1 {{ request()->routeIs('client.users.*') ? 'active' : '' }}">
                          <a href="{{ route('client.users.index') }}"
                              class="block px-3 py-2 text-sm {{ request()->routeIs('client.users.index') ? 'text-accent-600' : 'text-primary-600' }} hover:text-accent-600 rounded">All
                              Users</a>
                          <a href="{{ route('client.users.create') }}"
                              class="block px-3 py-2 text-sm {{ request()->routeIs('client.users.create') ? 'text-accent-600' : 'text-primary-600' }} hover:text-accent-600 rounded">Add
                              New
                              User</a>
                      </div>
                  </div>
              @endif

              <div class="pt-4 mt-4 border-t border-gray-200">
                  <a href="settings.html" class="sidebar-link flex items-center space-x-3 px-3 py-2 rounded-lg">
                      <i class="fas fa-shield w-5 text-center"></i>
                      <span>Subscription</span>
                  </a>
                  <a href="settings.html" class="sidebar-link flex items-center space-x-3 px-3 py-2 rounded-lg">
                      <i class="fas fa-cog w-5 text-center"></i>
                      <span>Settings</span>
                  </a>
                  <a href="login.html" class="sidebar-link flex items-center space-x-3 px-3 py-2 rounded-lg">
                      <i class="fas fa-sign-out-alt w-5 text-center"></i>
                      <span>Logout</span>
                  </a>
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
