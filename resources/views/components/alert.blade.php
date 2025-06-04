<!-- Alert Messages Component -->
<div class="px-4 sm:px-6 lg:px-8">

    <!-- Success Messages -->
    @if (session('success'))
        <div x-data="{ show: true }"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90"
             class="mt-4 rounded-md bg-green-50 p-4 border border-green-200">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400 text-lg"></i>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-green-800">
                        Success!
                    </h3>
                    <div class="mt-1 text-sm text-green-700">
                        {{ session('success') }}
                    </div>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button @click="show = false"
                               class="inline-flex bg-green-50 rounded-md p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-green-50 focus:ring-green-600 transition-colors duration-200">
                            <span class="sr-only">Dismiss</span>
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Error Messages -->
    @if (session('error'))
        <div x-data="{ show: true }"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90"
             class="mt-4 rounded-md bg-red-50 p-4 border border-red-200">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400 text-lg"></i>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-red-800">
                        Error!
                    </h3>
                    <div class="mt-1 text-sm text-red-700">
                        {{ session('error') }}
                    </div>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button @click="show = false"
                               class="inline-flex bg-red-50 rounded-md p-1.5 text-red-500 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-red-50 focus:ring-red-600 transition-colors duration-200">
                            <span class="sr-only">Dismiss</span>
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Warning Messages -->
    @if (session('warning'))
        <div x-data="{ show: true }"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90"
             class="mt-4 rounded-md bg-yellow-50 p-4 border border-yellow-200">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-400 text-lg"></i>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-yellow-800">
                        Warning!
                    </h3>
                    <div class="mt-1 text-sm text-yellow-700">
                        {{ session('warning') }}
                    </div>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button @click="show = false"
                               class="inline-flex bg-yellow-50 rounded-md p-1.5 text-yellow-500 hover:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-yellow-50 focus:ring-yellow-600 transition-colors duration-200">
                            <span class="sr-only">Dismiss</span>
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Info Messages -->
    @if (session('info'))
        <div x-data="{ show: true }"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90"
             class="mt-4 rounded-md bg-blue-50 p-4 border border-blue-200">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-400 text-lg"></i>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-blue-800">
                        Information
                    </h3>
                    <div class="mt-1 text-sm text-blue-700">
                        {{ session('info') }}
                    </div>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button @click="show = false"
                               class="inline-flex bg-blue-50 rounded-md p-1.5 text-blue-500 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-blue-50 focus:ring-blue-600 transition-colors duration-200">
                            <span class="sr-only">Dismiss</span>
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Status Messages (for specific operations) -->
    @if (session('status'))
        <div x-data="{ show: true }"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90"
             class="mt-4 rounded-md bg-green-50 p-4 border border-green-200">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400 text-lg"></i>
                </div>
                <div class="ml-3 flex-1">
                    <div class="text-sm text-green-700">
                        {{ session('status') }}
                    </div>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button @click="show = false"
                               class="inline-flex bg-green-50 rounded-md p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-green-50 focus:ring-green-600 transition-colors duration-200">
                            <span class="sr-only">Dismiss</span>
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Validation Errors -->
    @if ($errors->any())
        <div x-data="{ show: true }"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90"
             class="mt-4 rounded-md bg-red-50 p-4 border border-red-200">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400 text-lg"></i>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-red-800">
                        {{ $errors->count() == 1 ? 'There was an error with your submission' : 'There were ' . $errors->count() . ' errors with your submission' }}
                    </h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button @click="show = false"
                               class="inline-flex bg-red-50 rounded-md p-1.5 text-red-500 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-red-50 focus:ring-red-600 transition-colors duration-200">
                            <span class="sr-only">Dismiss</span>
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Dynamic Notification Container (for JavaScript-triggered notifications) -->
<div id="notification-container"
     class="fixed top-4 right-4 z-50 space-y-2"
     x-data="{ notifications: [] }"
     @show-notification.window="
        notifications.push({
            id: Date.now(),
            message: $event.detail.message,
            type: $event.detail.type || 'success'
        });
        setTimeout(() => {
            notifications = notifications.filter(n => n.id !== notifications[0].id);
        }, 5000);
     ">

    <template x-for="notification in notifications" :key="notification.id">
        <div x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-x-full opacity-0"
             x-transition:enter-end="translate-x-0 opacity-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-x-0 opacity-100"
             x-transition:leave-end="translate-x-full opacity-0"
             class="max-w-sm w-full shadow-lg rounded-lg pointer-events-auto"
             :class="{
                'bg-green-50 border-green-200': notification.type === 'success',
                'bg-red-50 border-red-200': notification.type === 'error',
                'bg-yellow-50 border-yellow-200': notification.type === 'warning',
                'bg-blue-50 border-blue-200': notification.type === 'info'
             }">
            <div class="p-4 border rounded-lg">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i :class="{
                            'fas fa-check-circle text-green-400': notification.type === 'success',
                            'fas fa-exclamation-circle text-red-400': notification.type === 'error',
                            'fas fa-exclamation-triangle text-yellow-400': notification.type === 'warning',
                            'fas fa-info-circle text-blue-400': notification.type === 'info'
                        }"></i>
                    </div>
                    <div class="ml-3 w-0 flex-1">
                        <p class="text-sm font-medium"
                           :class="{
                               'text-green-800': notification.type === 'success',
                               'text-red-800': notification.type === 'error',
                               'text-yellow-800': notification.type === 'warning',
                               'text-blue-800': notification.type === 'info'
                           }"
                           x-text="notification.message">
                        </p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button @click="notifications = notifications.filter(n => n.id !== notification.id)"
                                class="inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200"
                                :class="{
                                    'text-green-500 hover:bg-green-100 focus:ring-green-600': notification.type === 'success',
                                    'text-red-500 hover:bg-red-100 focus:ring-red-600': notification.type === 'error',
                                    'text-yellow-500 hover:bg-yellow-100 focus:ring-yellow-600': notification.type === 'warning',
                                    'text-blue-500 hover:bg-blue-100 focus:ring-blue-600': notification.type === 'info'
                                }">
                            <span class="sr-only">Dismiss</span>
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

<!-- Alert Component JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-hide session flash messages after 7 seconds
        const sessionAlerts = document.querySelectorAll('[x-data*="show: true"]');
        sessionAlerts.forEach(function(alert) {
            setTimeout(function() {
                // Trigger the Alpine.js hide
                alert.__x.$data.show = false;
            }, 7000);
        });
    });

    // Global function to show notifications from JavaScript
    window.showAlert = function(message, type = 'success') {
        const event = new CustomEvent('show-notification', {
            detail: { message, type }
        });
        window.dispatchEvent(event);
    };

    // Helper functions for different alert types
    window.showSuccess = function(message) {
        window.showAlert(message, 'success');
    };

    window.showError = function(message) {
        window.showAlert(message, 'error');
    };

    window.showWarning = function(message) {
        window.showAlert(message, 'warning');
    };

    window.showInfo = function(message) {
        window.showAlert(message, 'info');
    };

    // Example usage in forms or AJAX calls:
    // showSuccess('Employee added successfully!');
    // showError('Failed to save employee data.');
    // showWarning('Face recognition confidence is low.');
    // showInfo('System maintenance scheduled for tonight.');
</script>
