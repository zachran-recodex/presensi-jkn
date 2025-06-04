<!-- resources/views/components/modal.blade.php -->
<div x-data="modalManager()" @keydown.escape.window="closeAll()">
    <!-- Confirmation Modal -->
    <div x-show="modals.confirm.show"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div x-show="modals.confirm.show"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                 @click="closeConfirm()"></div>

            <!-- Modal content -->
            <div x-show="modals.confirm.show"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">

                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10"
                         :class="modals.confirm.type === 'danger' ? 'bg-red-100' : 'bg-yellow-100'">
                        <i :class="modals.confirm.type === 'danger' ? 'fas fa-exclamation-triangle text-red-600' : 'fas fa-question-circle text-yellow-600'"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" x-text="modals.confirm.title"></h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500" x-text="modals.confirm.message"></p>
                        </div>
                    </div>
                </div>

                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button type="button"
                            @click="confirmAction()"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-200"
                            :class="modals.confirm.type === 'danger' ? 'bg-red-600 hover:bg-red-700 focus:ring-red-500' : 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500'"
                            x-text="modals.confirm.confirmText">
                    </button>
                    <button type="button"
                            @click="closeConfirm()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm transition-colors duration-200"
                            x-text="modals.confirm.cancelText">
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Modal -->
    <div x-show="modals.loading.show"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>

            <!-- Modal content -->
            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm sm:w-full sm:p-6">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                        <i class="fas fa-spinner fa-spin text-blue-600 text-xl"></i>
                    </div>
                    <div class="mt-3">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" x-text="modals.loading.title"></h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500" x-text="modals.loading.message"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div x-show="modals.success.show"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75"
                 @click="closeSuccess()"></div>

            <!-- Modal content -->
            <div x-show="modals.success.show"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">

                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                        <i class="fas fa-check text-green-600 text-xl"></i>
                    </div>
                    <div class="mt-3">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" x-text="modals.success.title"></h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500" x-text="modals.success.message"></p>
                        </div>
                    </div>
                </div>

                <div class="mt-5 sm:mt-6">
                    <button type="button"
                            @click="closeSuccess()"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:text-sm transition-colors duration-200">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div x-show="modals.error.show"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75"
                 @click="closeError()"></div>

            <!-- Modal content -->
            <div x-show="modals.error.show"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">

                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
                    </div>
                    <div class="mt-3">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" x-text="modals.error.title"></h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500" x-text="modals.error.message"></p>
                        </div>
                    </div>
                </div>

                <div class="mt-5 sm:mt-6">
                    <button type="button"
                            @click="closeError()"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm transition-colors duration-200">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function modalManager() {
        return {
            modals: {
                confirm: {
                    show: false,
                    title: '',
                    message: '',
                    type: 'warning', // 'warning' or 'danger'
                    confirmText: 'Ya',
                    cancelText: 'Batal',
                    onConfirm: null
                },
                loading: {
                    show: false,
                    title: 'Memproses...',
                    message: 'Mohon tunggu sebentar'
                },
                success: {
                    show: false,
                    title: 'Berhasil!',
                    message: ''
                },
                error: {
                    show: false,
                    title: 'Terjadi Kesalahan!',
                    message: ''
                }
            },

            // Confirmation modal methods
            showConfirm(options) {
                this.modals.confirm = {
                    show: true,
                    title: options.title || 'Konfirmasi',
                    message: options.message || 'Apakah Anda yakin?',
                    type: options.type || 'warning',
                    confirmText: options.confirmText || 'Ya',
                    cancelText: options.cancelText || 'Batal',
                    onConfirm: options.onConfirm || null
                };
            },

            confirmAction() {
                if (this.modals.confirm.onConfirm) {
                    this.modals.confirm.onConfirm();
                }
                this.closeConfirm();
            },

            closeConfirm() {
                this.modals.confirm.show = false;
            },

            // Loading modal methods
            showLoading(title = 'Memproses...', message = 'Mohon tunggu sebentar') {
                this.modals.loading = {
                    show: true,
                    title: title,
                    message: message
                };
            },

            hideLoading() {
                this.modals.loading.show = false;
            },

            // Success modal methods
            showSuccess(title = 'Berhasil!', message = '') {
                this.modals.success = {
                    show: true,
                    title: title,
                    message: message
                };

                // Auto close after 3 seconds
                setTimeout(() => {
                    this.closeSuccess();
                }, 3000);
            },

            closeSuccess() {
                this.modals.success.show = false;
            },

            // Error modal methods
            showError(title = 'Terjadi Kesalahan!', message = '') {
                this.modals.error = {
                    show: true,
                    title: title,
                    message: message
                };
            },

            closeError() {
                this.modals.error.show = false;
            },

            // Close all modals
            closeAll() {
                this.modals.confirm.show = false;
                this.modals.loading.show = false;
                this.modals.success.show = false;
                this.modals.error.show = false;
            }
        }
    }

    // Make modal manager globally available
    window.modalManager = modalManager();

    // Global modal helper functions
    window.showConfirm = function(options) {
        const manager = window.modalManager;
        if (manager && manager.showConfirm) {
            manager.showConfirm(options);
        }
    };

    window.showLoading = function(title, message) {
        const manager = window.modalManager;
        if (manager && manager.showLoading) {
            manager.showLoading(title, message);
        }
    };

    window.hideLoading = function() {
        const manager = window.modalManager;
        if (manager && manager.hideLoading) {
            manager.hideLoading();
        }
    };

    window.showSuccess = function(title, message) {
        const manager = window.modalManager;
        if (manager && manager.showSuccess) {
            manager.showSuccess(title, message);
        }
    };

    window.showError = function(title, message) {
        const manager = window.modalManager;
        if (manager && manager.showError) {
            manager.showError(title, message);
        }
    };

    // Common confirmation dialogs
    window.confirmDelete = function(onConfirm, itemName = 'item ini') {
        window.showConfirm({
            title: 'Konfirmasi Hapus',
            message: `Apakah Anda yakin ingin menghapus ${itemName}? Tindakan ini tidak dapat dibatalkan.`,
            type: 'danger',
            confirmText: 'Hapus',
            cancelText: 'Batal',
            onConfirm: onConfirm
        });
    };

    window.confirmLogout = function() {
        window.showConfirm({
            title: 'Konfirmasi Logout',
            message: 'Apakah Anda yakin ingin keluar dari sistem?',
            type: 'warning',
            confirmText: 'Keluar',
            cancelText: 'Batal',
            onConfirm: function() {
                document.querySelector('form[action*="logout"] button[type="submit"]')?.click();
            }
        });
    };

    // Form submission with loading
    window.submitWithLoading = function(form, loadingTitle = 'Menyimpan...', loadingMessage = 'Mohon tunggu sebentar') {
        window.showLoading(loadingTitle, loadingMessage);

        // Add event listener for form response
        form.addEventListener('submit', function() {
            // Loading will be hidden by page navigation or by response handler
        });

        return true;
    };

    // AJAX with loading modal
    window.ajaxWithLoading = function(options) {
        const originalSuccess = options.success;
        const originalError = options.error;
        const originalComplete = options.complete;

        // Show loading
        window.showLoading(options.loadingTitle || 'Memproses...', options.loadingMessage || 'Mohon tunggu sebentar');

        // Override success callback
        options.success = function(data, textStatus, jqXHR) {
            window.hideLoading();
            if (originalSuccess) {
                originalSuccess(data, textStatus, jqXHR);
            }
        };

        // Override error callback
        options.error = function(jqXHR, textStatus, errorThrown) {
            window.hideLoading();

            let errorMessage = 'Terjadi kesalahan pada server';
            if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                errorMessage = jqXHR.responseJSON.message;
            }

            window.showError('Terjadi Kesalahan!', errorMessage);

            if (originalError) {
                originalError(jqXHR, textStatus, errorThrown);
            }
        };

        // Override complete callback
        options.complete = function(jqXHR, textStatus) {
            window.hideLoading();
            if (originalComplete) {
                originalComplete(jqXHR, textStatus);
            }
        };

        return window.axios(options);
    };
</script>
