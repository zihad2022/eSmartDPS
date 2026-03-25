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
        class="fixed inset-0 bg-gray-950/40 backdrop-blur-[6px] transition-opacity"></div>

    <!-- Modal Content -->
    <div x-show="show" 
        class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="show" 
                x-on:click.away="close()"
                x-transition:enter="ease-out duration-400" 
                x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95" 
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave="ease-in duration-200" 
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95" 
                class="relative transform overflow-hidden rounded-[24px] bg-white text-left shadow-[0_20px_50px_rgba(0,0,0,0.15)] transition-all sm:my-8 sm:w-full sm:max-w-md border border-gray-100">
                
                <!-- Close Button (X) -->
                <button x-on:click="close()" 
                    class="absolute right-5 top-5 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors duration-200"
                    aria-label="Close modal">
                    <i class="fas fa-times text-lg"></i>
                </button>

                <div class="bg-white px-8 pt-10 pb-4">
                    <div class="flex flex-col items-center">
                        <!-- Icon with subtle glow -->
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl shadow-sm mb-6 transition-all duration-500"
                            :class="{
                                'bg-red-50 text-red-500 ring-4 ring-red-50/50': type === 'danger' || type === 'warning',
                                'bg-blue-50 text-blue-500 ring-4 ring-blue-50/50': type === 'info',
                                'bg-green-50 text-green-500 ring-4 ring-green-50/50': type === 'success',
                            }">
                            <template x-if="type === 'danger' || type === 'warning'">
                                <i class="fas fa-exclamation-triangle text-2xl"></i>
                            </template>
                            <template x-if="type === 'info'">
                                <i class="fas fa-info-circle text-2xl"></i>
                            </template>
                            <template x-if="type === 'success'">
                                <i class="fas fa-check-circle text-2xl"></i>
                            </template>
                        </div>
                        
                        <!-- Content -->
                        <div class="text-center w-full">
                            <h3 class="text-2xl font-bold text-gray-900 mb-3 tracking-tight font-display" id="modal-title" x-text="title"></h3>
                            <p class="text-[15px] text-gray-500 leading-relaxed font-sans px-2" x-text="message"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="px-8 py-8 flex flex-col sm:flex-row-reverse sm:gap-3">
                    <button type="button" 
                        x-on:click="confirm()"
                        class="inline-flex w-full justify-center rounded-xl px-6 py-3 text-[15px] font-bold text-white shadow-lg transition-all duration-200 sm:w-1/2 outline-none hover:shadow-xl active:scale-[0.98]"
                        :class="{
                            'bg-red-500 hover:bg-red-600 shadow-red-200 focus:ring-2 focus:ring-red-400 focus:ring-offset-2': type === 'danger' || type === 'warning',
                            'bg-blue-500 hover:bg-blue-600 shadow-blue-200 focus:ring-2 focus:ring-blue-400 focus:ring-offset-2': type === 'info',
                            'bg-green-500 hover:bg-green-600 shadow-green-200 focus:ring-2 focus:ring-green-400 focus:ring-offset-2': type === 'success',
                        }"
                        x-text="confirmText"></button>
                    <button type="button" 
                        x-on:click="close()"
                        class="mt-3 inline-flex w-full justify-center rounded-xl bg-gray-50 px-6 py-3 text-[15px] font-bold text-gray-600 hover:bg-gray-100 transition-all duration-200 sm:mt-0 sm:w-1/2 border border-gray-200/50 hover:border-gray-300 active:scale-[0.98]"
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
