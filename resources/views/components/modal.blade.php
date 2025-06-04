<!-- Reusable Modal Component -->
<div x-data="{
        modalOpen: false,
        modalTitle: '',
        modalContent: '',
        modalType: 'info',
        modalSize: 'md',
        onConfirm: null,
        onCancel: null,
        confirmText: 'Confirm',
        cancelText: 'Cancel',
        showFooter: true
     }"
     @open-modal.window="
        modalOpen = true;
        modalTitle = $event.detail.title || 'Modal';
        modalContent = $event.detail.content || '';
        modalType = $event.detail.type || 'info';
        modalSize = $event.detail.size || 'md';
        onConfirm = $event.detail.onConfirm || null;
        onCancel = $event.detail.onCancel || null;
        confirmText = $event.detail.confirmText || 'Confirm';
        cancelText = $event.detail.cancelText || 'Cancel';
        showFooter = $event.detail.showFooter !== false;
     "
     @close-modal.window="modalOpen = false"
     @keydown.escape.window="modalOpen = false">

    <!-- Modal Backdrop -->
    <div x-show="modalOpen"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
         @click="modalOpen = false">
    </div>

    <!-- Modal Container -->
    <div x-show="modalOpen"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         class="fixed inset-0 z-50 overflow-y-auto">

        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

            <!-- Modal Panel -->
            <div @click.stop
                 class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:w-full"
                 :class="{
                     'sm:max-w-sm': modalSize === 'sm',
                     'sm:max-w-lg': modalSize === 'md',
                     'sm:max-w-2xl': modalSize === 'lg',
                     'sm:max-w-4xl': modalSize === 'xl',
                     'sm:max-w-6xl': modalSize === '2xl'
                 }">

                <!-- Modal Header -->
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">

                        <!-- Icon based on modal type -->
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10"
                             :class="{
                                 'bg-blue-100': modalType === 'info',
                                 'bg-green-100': modalType === 'success',
                                 'bg-yellow-100': modalType === 'warning',
                                 'bg-red-100': modalType === 'danger' || modalType === 'error',
                                 'bg-gray-100': modalType === 'default'
                             }">
                            <i :class="{
                                'fas fa-info-circle text-blue-600': modalType === 'info',
                                'fas fa-check-circle text-green-600': modalType === 'success',
                                'fas fa-exclamation-triangle text-yellow-600': modalType === 'warning',
                                'fas fa-exclamation-circle text-red-600': modalType === 'danger' || modalType === 'error',
                                'fas fa-question-circle text-gray-600': modalType === 'default'
                            }"></i>
                        </div>

                        <!-- Modal Content -->
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" x-text="modalTitle"></h3>
                            <div class="mt-2">
                                <div class="text-sm text-gray-500" x-html="modalContent"></div>
                            </div>
                        </div>

                        <!-- Close button -->
                        <div class="absolute top-0 right-0 pt-4 pr-4">
                            <button @click="modalOpen = false"
                                   class="bg-white rounded-md text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                <span class="sr-only">Close</span>
                                <i class="fas fa-times text-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div x-show="showFooter"
                     class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">

                    <!-- Confirm Button -->
                    <button x-show="onConfirm"
                           @click="if(onConfirm) onConfirm(); modalOpen = false;"
                           type="button"
                           class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-200"
                           :class="{
                               'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500': modalType === 'info' || modalType === 'default',
                               'bg-green-600 hover:bg-green-700 focus:ring-green-500': modalType === 'success',
                               'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500': modalType === 'warning',
                               'bg-red-600 hover:bg-red-700 focus:ring-red-500': modalType === 'danger' || modalType === 'error'
                           }"
                           x-text="confirmText">
                    </button>

                    <!-- Cancel Button -->
                    <button @click="if(onCancel) onCancel(); modalOpen = false;"
                           type="button"
                           class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-200"
                           x-text="cancelText">
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Specific Modal Types -->

<!-- Confirmation Modal -->
<div x-data="{
        confirmModalOpen: false,
        confirmTitle: '',
        confirmMessage: '',
        confirmAction: null,
        confirmDanger: false
     }"
     @open-confirm-modal.window="
        confirmModalOpen = true;
        confirmTitle = $event.detail.title || 'Confirm Action';
        confirmMessage = $event.detail.message || 'Are you sure?';
        confirmAction = $event.detail.action || null;
        confirmDanger = $event.detail.danger || false;
     "
     @close-confirm-modal.window="confirmModalOpen = false">

    <!-- Confirmation Modal Backdrop -->
    <div x-show="confirmModalOpen"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
         @click="confirmModalOpen = false">
    </div>

    <!-- Confirmation Modal -->
    <div x-show="confirmModalOpen"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         class="fixed inset-0 z-50 overflow-y-auto">

        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div @click.stop
                 class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10"
                             :class="confirmDanger ? 'bg-red-100' : 'bg-yellow-100'">
                            <i :class="confirmDanger ? 'fas fa-exclamation-triangle text-red-600' : 'fas fa-question-circle text-yellow-600'"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" x-text="confirmTitle"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500" x-text="confirmMessage"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button @click="if(confirmAction) confirmAction(); confirmModalOpen = false;"
                           type="button"
                           class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-200"
                           :class="confirmDanger ? 'bg-red-600 hover:bg-red-700 focus:ring-red-500' : 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500'">
                        <span x-text="confirmDanger ? 'Delete' : 'Confirm'"></span>
                    </button>
                    <button @click="confirmModalOpen = false"
                           type="button"
                           class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm transition-colors duration-200">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div x-data="{
        loadingModalOpen: false,
        loadingMessage: 'Processing...'
     }"
     @open-loading-modal.window="
        loadingModalOpen = true;
        loadingMessage = $event.detail.message || 'Processing...';
     "
     @close-loading-modal.window="loadingModalOpen = false">

    <!-- Loading Modal Backdrop -->
    <div x-show="loadingModalOpen"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50">
    </div>

    <!-- Loading Modal -->
    <div x-show="loadingModalOpen"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         class="fixed inset-0 z-50 overflow-y-auto">

        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="inline-block align-middle bg-white rounded-lg text-center overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-sm sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                    <div class="flex flex-col items-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-4"></div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">Please Wait</h3>
                        <p class="text-sm text-gray-500" x-text="loadingMessage"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal JavaScript Functions -->
<script>
    // Global modal functions
    window.openModal = function(options) {
        const event = new CustomEvent('open-modal', {
            detail: {
                title: options.title || 'Modal',
                content: options.content || '',
                type: options.type || 'info',
                size: options.size || 'md',
                onConfirm: options.onConfirm || null,
                onCancel: options.onCancel || null,
                confirmText: options.confirmText || 'Confirm',
                cancelText: options.cancelText || 'Cancel',
                showFooter: options.showFooter !== false
            }
        });
        window.dispatchEvent(event);
    };

    window.closeModal = function() {
        window.dispatchEvent(new CustomEvent('close-modal'));
    };

    // Confirmation modal
    window.confirmAction = function(options) {
        const event = new CustomEvent('open-confirm-modal', {
            detail: {
                title: options.title || 'Confirm Action',
                message: options.message || 'Are you sure?',
                action: options.action || null,
                danger: options.danger || false
            }
        });
        window.dispatchEvent(event);
    };

    // Loading modal
    window.showLoading = function(message = 'Processing...') {
        const event = new CustomEvent('open-loading-modal', {
            detail: { message }
        });
        window.dispatchEvent(event);
    };

    window.hideLoading = function() {
        window.dispatchEvent(new CustomEvent('close-loading-modal'));
    };

    // Helper functions for common modal types
    window.showInfoModal = function(title, content) {
        openModal({
            title: title,
            content: content,
            type: 'info',
            showFooter: false
        });
    };

    window.showSuccessModal = function(title, content) {
        openModal({
            title: title,
            content: content,
            type: 'success',
            showFooter: false
        });
    };

    window.showErrorModal = function(title, content) {
        openModal({
            title: title,
            content: content,
            type: 'error',
            showFooter: false
        });
    };

    // Delete confirmation helper
    window.confirmDelete = function(itemName, deleteAction) {
        confirmAction({
            title: 'Confirm Deletion',
            message: `Are you sure you want to delete "${itemName}"? This action cannot be undone.`,
            action: deleteAction,
            danger: true
        });
    };

    // Example Usage:
    /*

    // Basic modal
    openModal({
        title: 'Employee Details',
        content: '<p>Employee information goes here...</p>',
        type: 'info'
    });

    // Modal with action
    openModal({
        title: 'Save Changes',
        content: 'Do you want to save your changes?',
        type: 'warning',
        onConfirm: function() {
            // Save logic here
            console.log('Saving...');
        }
    });

    // Confirmation modal
    confirmAction({
        title: 'Delete Employee',
        message: 'Are you sure you want to delete this employee?',
        action: function() {
            // Delete logic here
            console.log('Deleting...');
        },
        danger: true
    });

    // Loading modal
    showLoading('Uploading photo...');
    // ... after operation
    hideLoading();

    */
</script>
