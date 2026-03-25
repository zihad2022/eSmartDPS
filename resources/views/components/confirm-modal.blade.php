<!-- resources/views/components/confirmation-modal.blade.php -->
<div x-data="{ 
    show: false, 
    type: 'warning', 
    title: '', 
    message: '', 
    confirmText: 'Confirm', 
    cancelText: 'Cancel',
    onConfirm: null,
    
    open(options) {
        this.type = options.type || 'warning';
        this.title = options.title || 'Are you sure?';
        this.message = options.message || 'This action cannot be undone.';
        this.confirmText = options.confirmText || 'Confirm';
        this.cancelText = options.cancelText || 'Cancel';
        this.onConfirm = options.onConfirm || null;
        this.show = true;
    },
    
    close() {
        this.show = false;
    },
    
    confirm() {
        if (this.onConfirm && typeof window[this.onConfirm] === 'function') {
            window[this.onConfirm]();
        } else if (this.onConfirm instanceof Function) {
            this.onConfirm();
        } else if (typeof this.onConfirm === 'string') {
            const form = document.querySelector(this.onConfirm);
            if (form && form.tagName === 'FORM') {
                form.submit();
            }
        }
        this.close();
    }
}" 
x-on:confirm-action.window="open($event.detail)"
x-on:keydown.escape.window="close()"
x-cloak
class="relative z-[9999]"
aria-labelledby="modal-title" role="dialog" aria-modal="true">

    <!-- Backdrop -->
    <div x-show="show" 
        x-transition:enter="ease-out duration-300" 
        x-transition:enter-start="opacity-0" 
        x-transition:enter-end="opacity-100" 
        x-transition:leave="ease-in duration-200" 
        x-transition:leave-start="opacity-100" 
        x-transition:leave-end="opacity-0" 
        class="fixed inset-0 bg-primary-900/40 backdrop-blur-sm transition-opacity"></div>

    <!-- Modal Content -->
    <div x-show="show" 
        class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="show" 
                x-on:click.away="close()"
                x-transition:enter="ease-out duration-300" 
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave="ease-in duration-200" 
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-gray-200">
                
                <!-- Close Button -->
                <button x-on:click="close()" 
                    class="absolute right-4 top-4 text-primary-400 hover:text-primary-600 focus:outline-none transition-colors duration-200"
                    aria-label="Close modal">
                    <i class="fas fa-times"></i>
                </button>

                <div class="bg-white px-6 pt-8 pb-4">
                    <div class="flex flex-col items-center sm:items-start sm:flex-row">
                        <!-- Icon -->
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg mb-4 sm:mb-0 sm:mr-4"
                            :class="{
                                'bg-red-100 text-red-600': type === 'danger' || type === 'warning',
                                'bg-accent-100 text-accent-700': type === 'success',
                                'bg-primary-100 text-primary-600': type === 'info',
                            }">
                            <template x-if="type === 'danger' || type === 'warning'">
                                <i class="fas fa-exclamation-triangle text-lg"></i>
                            </template>
                            <template x-if="type === 'info'">
                                <i class="fas fa-info-circle text-lg"></i>
                            </template>
                            <template x-if="type === 'success'">
                                <i class="fas fa-check-circle text-lg"></i>
                            </template>
                        </div>
                        
                        <!-- Text -->
                        <div class="text-center sm:text-left">
                            <h3 class="text-lg font-bold text-primary-900 mb-1 font-display" id="modal-title" x-text="title"></h3>
                            <p class="text-sm text-primary-500 font-sans" x-text="message"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Footer Buttons -->
                <div class="px-6 py-6 sm:flex sm:flex-row-reverse sm:gap-3 border-t border-gray-50 bg-gray-50/30">
                    <button type="button" 
                        x-on:click="confirm()"
                        class="inline-flex w-full justify-center rounded-lg px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all duration-200 sm:w-auto outline-none"
                        :class="{
                            'bg-red-600 hover:bg-red-700': type === 'danger' || type === 'warning',
                            'bg-primary-700 hover:bg-primary-800': type === 'info',
                            'bg-accent-500 hover:bg-accent-600': type === 'success',
                        }"
                        x-text="confirmText"></button>
                    <button type="button" 
                        x-on:click="close()"
                        class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-primary-700 shadow-sm border border-gray-300 hover:bg-gray-50 transition-all duration-200 sm:mt-0 sm:w-auto"
                        x-text="cancelText"></button>
                </div>
            </div>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
    /**
     * Dispatch confirmation event globally
     */
    window.confirmAction = function(options) {
        window.dispatchEvent(new CustomEvent('confirm-action', {
            detail: options
        }));
    };

    /**
     * Confirmation directive for buttons and forms
     */
    document.addEventListener('DOMContentLoaded', () => {
        // Handle generic confirmation buttons/links
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-confirm]');
            if (!btn || btn.hasAttribute('data-confirmed')) return;

            e.preventDefault();
            e.stopImmediatePropagation();
            
            const message = btn.getAttribute('data-confirm') || 'Are you sure you want to proceed?';
            const title = btn.getAttribute('data-confirm-title') || 'Confirm Action';
            const type = btn.getAttribute('data-confirm-type') || 'warning';
            const confirmText = btn.getAttribute('data-confirm-text') || 'Confirm';
            
            window.confirmAction({
                title: title,
                message: message,
                type: type,
                confirmText: confirmText,
                onConfirm: () => {
                    btn.setAttribute('data-confirmed', 'true');
                    btn.click();
                    setTimeout(() => btn.removeAttribute('data-confirmed'), 500);
                }
            });
        }, true);

        // Handle specific delete buttons
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.delete-btn:not([data-confirm])');
            if (!btn || btn.hasAttribute('data-confirmed')) return;

            const form = btn.closest('form');
            if (!form) return;

            e.preventDefault();
            e.stopImmediatePropagation();
            
            window.confirmAction({
                title: 'Delete Resource?',
                message: 'This action is permanent and cannot be undone. Are you sure?',
                type: 'danger',
                confirmText: 'Delete Now',
                onConfirm: () => {
                    btn.setAttribute('data-confirmed', 'true');
                    form.submit();
                }
            });
        }, true);

        // Logout specific handling
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (form.getAttribute('action')?.includes('logout') && !form.hasAttribute('data-confirmed')) {
                e.preventDefault();
                window.confirmAction({
                    title: 'Confirm Logout',
                    message: 'Are you sure you want to log out of your account?',
                    type: 'info',
                    confirmText: 'Logout',
                    onConfirm: () => {
                        form.setAttribute('data-confirmed', 'true');
                        form.submit();
                    }
                });
            }
        });
    });
</script>
@endpush
@endonce
