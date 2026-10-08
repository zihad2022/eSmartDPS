<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?: 'SMART DPS' }} - SMART DPS</title>
    <script>
        (function() {
            const savedTheme = localStorage.getItem('smart-dps-theme') || localStorage.getItem('free-sms-theme') ||
                'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
    <link rel="stylesheet" href="{{ asset('scuser/assets/css/frontend.min.css') }}">
    <link rel="stylesheet" href="{{ asset('scuser/assets/css/responsive.min.css') }}">
    <link rel="stylesheet" href="{{ asset('scuser/assets/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('scuser/assets/css/smart-dps.css') }}">
    @stack('styles')
</head>

<body>
    <header>
        <a href="{{ route('client.dashboard') }}">
            <div class="logo"><i class="fas fa-piggy-bank"></i> SMART DPS</div>
        </a>
        <div class="header-actions">
            <button type="button" class="icon-btn" id="theme-toggle" aria-label="Toggle light and dark mode"
                title="Light / Dark mode">
                <i class="fas fa-moon"></i>
            </button>
        </div>
    </header>

    @if (!request()->is('client/checkout*'))
        @php
            $client = Auth::guard('client')->user();
            $latestUnpaidInvoice = $client
                ?->invoices()
                ->where('status', \App\Enums\InvoiceStatus::UNPAID)
                ->latest()
                ->first();
        @endphp
        @if ($latestUnpaidInvoice)
        @endif
    @endif

    <main class="container pb-100 smart-client-main">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        {{ $slot }}
    </main>

    <nav class="bottom-nav">
        <a href="{{ route('client.dashboard') }}" class="nav-item"><i class="fas fa-home"></i>Home</a>
        <a href="{{ route('client.members.index') }}" class="nav-item"><i class="fas fa-users"></i>Members</a>
        {{-- <a href="{{ route('client.projects.index') }}" class="nav-item"><i class="fas fa-project-diagram"></i>Projects</a> --}}
        {{-- <a href="{{ route('client.payments.index') }}" class="nav-item"><i class="fas fa-credit-card"></i>Payments</a> --}}
        <a href="{{ route('client.menu') }}" class="nav-item"><i class="fas fa-bars"></i>Menu</a>
    </nav>

    <div class="modal-overlay" id="record-confirm-modal" role="dialog" aria-modal="true"
        aria-labelledby="recordConfirmTitle" onclick="if(event.target===this) closeRecordConfirmModal()">
        <div class="modal-content confirm-modal-card">
            <h3 class="modal-title-bold" id="recordConfirmTitle">Confirm Delete</h3>
            <p class="modal-desc" id="recordConfirmMessage">Are you sure you want to delete this item? This action
                cannot be undone.</p>
            <div class="confirm-modal-actions">
                <button type="button" class="confirm-btn confirm-btn-cancel" id="recordConfirmCancel">Cancel</button>
                <button type="button" class="confirm-btn confirm-btn-danger" id="recordConfirmDelete">Delete</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="logout-modal" onclick="if(event.target===this) closeModal('logout-modal')">
        <div class="modal-content confirm-modal-card">
            <h3 class="modal-title-bold">Confirm Sign Out</h3>
            <p class="modal-desc">Are you sure you want to sign out?</p>
            <div class="confirm-modal-actions">
                <button type="button" class="confirm-btn confirm-btn-cancel"
                    onclick="closeModal('logout-modal')">Cancel</button>
                <button type="button" class="confirm-btn confirm-btn-primary"
                    onclick="document.getElementById('logoutForm').submit()">Sign out</button>
            </div>
        </div>
    </div>
    <form id="logoutForm" method="POST" action="{{ route('client.logout') }}" class="hidden">@csrf</form>

    <script src="{{ asset('scuser/assets/js/global.min.js') }}"></script>
    @vite(['resources/js/app.js'])
    <script>
        let pendingDeleteForm = null;
        let pendingDeleteTrigger = null;

        function isDeleteForm(form) {
            if (!form) return false;
            const methodField = form.querySelector('input[name="_method"]');
            return methodField && methodField.value.toUpperCase() === 'DELETE';
        }

        function openDeleteConfirm(form, trigger = null) {
            if (!form) return;
            pendingDeleteForm = form;
            pendingDeleteTrigger = trigger;

            const title = 'Confirm Delete';
            const message = trigger?.dataset.confirmMessage || form.dataset.confirmMessage ||
                'Are you sure you want to delete this record? This action cannot be undone.';

            document.getElementById('recordConfirmTitle').textContent = title;
            document.getElementById('recordConfirmMessage').textContent = message;
            const confirmButton = document.getElementById('recordConfirmDelete');
            if (confirmButton) {
                confirmButton.disabled = false;
                confirmButton.textContent = 'Delete';
            }
            openModal('record-confirm-modal');
        }

        function closeRecordConfirmModal() {
            pendingDeleteForm = null;
            pendingDeleteTrigger = null;
            closeModal('record-confirm-modal');
        }

        document.addEventListener('click', function(e) {
            const trigger = e.target.closest('[data-logout]');
            if (trigger) {
                e.preventDefault();
                openModal('logout-modal');
                return;
            }

            const deleteBtn = e.target.closest('.delete-btn, [data-delete-confirm]');
            if (deleteBtn) {
                const form = deleteBtn.closest('form');
                if (!form || !isDeleteForm(form)) return;
                e.preventDefault();
                e.stopPropagation();
                openDeleteConfirm(form, deleteBtn);
            }
        });

        // Enforce the custom modal for every DELETE form in the client area,
        // including forms added later that do not have a dedicated delete button hook.
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (!isDeleteForm(form) || form.dataset.confirmed === 'true') return;
            e.preventDefault();
            openDeleteConfirm(form, form.querySelector('.delete-btn, [data-delete-confirm]'));
        }, true);

        document.getElementById('recordConfirmCancel')?.addEventListener('click', closeRecordConfirmModal);
        document.getElementById('recordConfirmDelete')?.addEventListener('click', function() {
            if (!pendingDeleteForm) return;
            const form = pendingDeleteForm;
            form.dataset.confirmed = 'true';
            this.disabled = true;
            this.textContent = 'Deleting...';
            closeModal('record-confirm-modal');
            form.submit();
        });
    </script>
    @stack('scripts')
</body>

</html>
