<!-- resources/views/components/alert.blade.php -->
<div x-data="alertManager()" @show-notification.window="showNotification($event.detail)">
    <!-- Flash Messages from Session -->
    @if (session('success'))
        <div class="alert-container">
            <div class="alert alert-success" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400 text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800">Berhasil!</h3>
                        <div class="mt-1 text-sm text-green-700">
                            {{ session('success') }}
                        </div>
                    </div>
                    <div class="ml-auto pl-3">
                        <button @click="show = false" class="alert-close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="alert-container">
            <div class="alert alert-error" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 8000)">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400 text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Terjadi Kesalahan!</h3>
                        <div class="mt-1 text-sm text-red-700">
                            {{ session('error') }}
                        </div>
                    </div>
                    <div class="ml-auto pl-3">
                        <button @click="show = false" class="alert-close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (session('warning'))
        <div class="alert-container">
            <div class="alert alert-warning" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-400 text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Perhatian!</h3>
                        <div class="mt-1 text-sm text-yellow-700">
                            {{ session('warning') }}
                        </div>
                    </div>
                    <div class="ml-auto pl-3">
                        <button @click="show = false" class="alert-close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (session('info'))
        <div class="alert-container">
            <div class="alert alert-info" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400 text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Informasi</h3>
                        <div class="mt-1 text-sm text-blue-700">
                            {{ session('info') }}
                        </div>
                    </div>
                    <div class="ml-auto pl-3">
                        <button @click="show = false" class="alert-close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="alert-container">
            <div class="alert alert-error" x-data="{ show: true }" x-show="show">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400 text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan pada form:</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="ml-auto pl-3">
                        <button @click="show = false" class="alert-close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Dynamic Notification Container -->
    <div id="notification-container" class="fixed top-4 right-4 z-50 space-y-2">
        <!-- Dynamic notifications will be inserted here -->
    </div>
</div>

<style>
    .alert-container {
        @apply px-4 sm:px-6 lg:px-8 py-3;
    }

    .alert {
        @apply max-w-7xl mx-auto rounded-md p-4 mb-4 shadow-sm border;
        transition: all 0.3s ease-in-out;
    }

    .alert-success {
        @apply bg-green-50 border-green-200;
    }

    .alert-error {
        @apply bg-red-50 border-red-200;
    }

    .alert-warning {
        @apply bg-yellow-50 border-yellow-200;
    }

    .alert-info {
        @apply bg-blue-50 border-blue-200;
    }

    .alert-close {
        @apply -mx-1.5 -my-1.5 bg-transparent rounded-md p-1.5 text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-transparent focus:ring-gray-500 transition-colors duration-200;
    }

    /* Dynamic notification styles */
    .notification {
        @apply rounded-lg shadow-lg p-4 max-w-sm w-full;
        transform: translateX(100%);
        animation: slideIn 0.3s ease-out forwards;
    }

    .notification.success {
        @apply bg-green-500 text-white;
    }

    .notification.error {
        @apply bg-red-500 text-white;
    }

    .notification.warning {
        @apply bg-yellow-500 text-white;
    }

    .notification.info {
        @apply bg-blue-500 text-white;
    }

    .notification.slide-out {
        animation: slideOut 0.3s ease-in forwards;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
</style>

<script>
    function alertManager() {
        return {
            notifications: [],

            showNotification(data) {
                const id = Date.now();
                const notification = {
                    id: id,
                    message: data.message,
                    type: data.type || 'info',
                    duration: data.duration || 5000
                };

                this.notifications.push(notification);
                this.renderNotification(notification);

                // Auto remove
                setTimeout(() => {
                    this.removeNotification(id);
                }, notification.duration);
            },

            renderNotification(notification) {
                const container = document.getElementById('notification-container');
                if (!container) return;

                const notificationEl = document.createElement('div');
                notificationEl.id = `notification-${notification.id}`;
                notificationEl.className = `notification ${notification.type}`;

                const icon = this.getIcon(notification.type);

                notificationEl.innerHTML = `
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas ${icon} text-lg"></i>
                        </div>
                        <div class="ml-3 w-0 flex-1">
                            <p class="text-sm font-medium">${notification.message}</p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button onclick="window.alertManager?.removeNotification(${notification.id})"
                                    class="rounded-md text-white hover:text-gray-200 focus:outline-none">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                `;

                container.appendChild(notificationEl);
            },

            removeNotification(id) {
                const notificationEl = document.getElementById(`notification-${id}`);
                if (notificationEl) {
                    notificationEl.classList.add('slide-out');
                    setTimeout(() => {
                        notificationEl.remove();
                    }, 300);
                }

                this.notifications = this.notifications.filter(n => n.id !== id);
            },

            getIcon(type) {
                switch(type) {
                    case 'success': return 'fa-check-circle';
                    case 'error': return 'fa-exclamation-circle';
                    case 'warning': return 'fa-exclamation-triangle';
                    case 'info': return 'fa-info-circle';
                    default: return 'fa-info-circle';
                }
            }
        }
    }

    // Make alertManager globally available
    window.alertManager = alertManager();

    // Global helper function for notifications
    window.showNotification = function(message, type = 'success', duration = 5000) {
        const event = new CustomEvent('show-notification', {
            detail: { message, type, duration }
        });
        window.dispatchEvent(event);
    };

    // Helper functions for different types
    window.showSuccess = function(message) {
        window.showNotification(message, 'success');
    };

    window.showError = function(message) {
        window.showNotification(message, 'error', 8000);
    };

    window.showWarning = function(message) {
        window.showNotification(message, 'warning', 6000);
    };

    window.showInfo = function(message) {
        window.showNotification(message, 'info');
    };

    // Auto-hide flash messages on mobile (shorter duration)
    document.addEventListener('DOMContentLoaded', function() {
        if (window.innerWidth < 768) {
            // On mobile, hide flash messages faster
            document.querySelectorAll('.alert').forEach(function(alert) {
                const alpineData = Alpine.$data(alert);
                if (alpineData && alpineData.show) {
                    setTimeout(() => {
                        alpineData.show = false;
                    }, 3000); // 3 seconds on mobile
                }
            });
        }
    });
</script>
