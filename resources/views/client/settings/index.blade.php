<x-client.layout.app title="Settings">
<h3 class="section-title"><i class="fas fa-cog"></i> Settings</h3>
<div class="menu-list">
<a href="{{ route('client.settings.general.edit') }}" class="menu-item"><div class="menu-icon"><i class="fas fa-building"></i></div><span class="menu-text">General Settings</span><i class="fas fa-chevron-right menu-arrow"></i></a>
<a href="{{ route('client.settings.share.edit') }}" class="menu-item"><div class="menu-icon"><i class="fas fa-share-alt"></i></div><span class="menu-text">Share Settings</span><i class="fas fa-chevron-right menu-arrow"></i></a>
<a href="{{ route('client.settings.payment.edit') }}" class="menu-item"><div class="menu-icon"><i class="fas fa-credit-card"></i></div><span class="menu-text">Payment Settings</span><i class="fas fa-chevron-right menu-arrow"></i></a>
<a href="{{ route('client.settings.notification.edit') }}" class="menu-item"><div class="menu-icon"><i class="fas fa-bell"></i></div><span class="menu-text">Notification Settings</span><i class="fas fa-chevron-right menu-arrow"></i></a>
<a href="{{ route('client.settings.backup-security.edit') }}" class="menu-item"><div class="menu-icon"><i class="fas fa-shield-alt"></i></div><span class="menu-text">Backup & Security</span><i class="fas fa-chevron-right menu-arrow"></i></a>
</div>
</x-client.layout.app>
