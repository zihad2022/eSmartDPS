<x-client.layout.app title="Menu">
<h3 class="section-title"><i class="fas fa-bars"></i> Menu</h3>

<h3 class="profile-heading">Management</h3>
<div class="menu-list">
    <a href="{{ route('client.dashboard') }}" class="menu-item"><div class="menu-icon"><i class="fas fa-home"></i></div><span class="menu-text">Dashboard</span><i class="fas fa-chevron-right menu-arrow"></i></a>
    <a href="{{ route('client.members.index') }}" class="menu-item"><div class="menu-icon"><i class="fas fa-users"></i></div><span class="menu-text">Members</span><i class="fas fa-chevron-right menu-arrow"></i></a>
    <a href="{{ route('client.project-categories.index') }}" class="menu-item"><div class="menu-icon"><i class="fas fa-folder"></i></div><span class="menu-text">Project Categories</span><i class="fas fa-chevron-right menu-arrow"></i></a>
    <a href="{{ route('client.projects.index') }}" class="menu-item"><div class="menu-icon"><i class="fas fa-project-diagram"></i></div><span class="menu-text">Projects</span><i class="fas fa-chevron-right menu-arrow"></i></a>
    <a href="{{ route('client.payments.index') }}" class="menu-item"><div class="menu-icon"><i class="fas fa-credit-card"></i></div><span class="menu-text">Payments</span><i class="fas fa-chevron-right menu-arrow"></i></a>
</div>

<h3 class="profile-heading profile-margin">Accounts</h3>
<div class="menu-list">
    <a href="{{ route('client.ledgers.index') }}" class="menu-item"><div class="menu-icon"><i class="fas fa-book"></i></div><span class="menu-text">Ledgers</span><i class="fas fa-chevron-right menu-arrow"></i></a>
    <a href="{{ route('client.ledger-categories.index') }}" class="menu-item"><div class="menu-icon"><i class="fas fa-tags"></i></div><span class="menu-text">Ledger Categories</span><i class="fas fa-chevron-right menu-arrow"></i></a>
    <a href="{{ route('client.invoices.index') }}" class="menu-item"><div class="menu-icon"><i class="fas fa-file-invoice"></i></div><span class="menu-text">Invoices</span><i class="fas fa-chevron-right menu-arrow"></i></a>
    <a href="{{ route('client.subscription.packages') }}" class="menu-item"><div class="menu-icon"><i class="fas fa-crown"></i></div><span class="menu-text">Subscription Packages</span><i class="fas fa-chevron-right menu-arrow"></i></a>
</div>

<h3 class="profile-heading profile-margin">Account & Support</h3>
<div class="menu-list">
    <a href="{{ route('client.users.index') }}" class="menu-item"><div class="menu-icon"><i class="fas fa-user-cog"></i></div><span class="menu-text">Users</span><i class="fas fa-chevron-right menu-arrow"></i></a>
    <a href="{{ route('client.tickets.index') }}" class="menu-item"><div class="menu-icon"><i class="fas fa-ticket-alt"></i></div><span class="menu-text">Tickets</span><i class="fas fa-chevron-right menu-arrow"></i></a>
    <a href="{{ route('client.settings.index') }}" class="menu-item"><div class="menu-icon"><i class="fas fa-cog"></i></div><span class="menu-text">Settings</span><i class="fas fa-chevron-right menu-arrow"></i></a>
    <button type="button" class="menu-item menu-item-logout w-full" data-logout style="width:100%;border-top:0;border-left:0;border-right:0;background:transparent;text-align:left"><div class="menu-icon menu-icon-logout"><i class="fas fa-sign-out-alt"></i></div><span class="menu-text">Sign Out</span><i class="fas fa-chevron-right menu-arrow"></i></button>
</div>
</x-client.layout.app>
