@if (auth('admin')->check())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('adminNotificationModal');
            const modalBody = document.getElementById('adminNotificationModalBody');
            const openButtons = document.querySelectorAll('[data-open-admin-notification-modal]');
            const closeButtons = document.querySelectorAll('[data-close-admin-notification-modal]');
            const notificationItems = document.querySelectorAll('[data-admin-notification-item]');
            const badges = document.querySelectorAll('[data-admin-notification-badge]');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            function openModal() {
                if (!modal) {
                    return;
                }

                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function closeModal() {
                if (!modal) {
                    return;
                }

                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            function clearBadges() {
                badges.forEach(function (badge) {
                    badge.textContent = '0';
                    badge.classList.add('hidden');
                });
            }

            function showEmptyMessage() {
                if (!modalBody) {
                    return;
                }

                modalBody.innerHTML = `
                    <div class="flex h-full items-center justify-center">
                        <p class="text-lg font-bold text-slate-500">
                            未読通知はありません
                        </p>
                    </div>
                `;
            }

            async function markAllAsRead() {
                if (!csrfToken) {
                    return;
                }

                const response = await fetch('{{ route('admin.notifications.read-all') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) {
                    return;
                }

                clearBadges();
                showEmptyMessage();
                closeModal();
            }

            openButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    openModal();
                });
            });

            closeButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    closeModal();
                });
            });

            notificationItems.forEach(function (item) {
                item.addEventListener('click', function () {
                    markAllAsRead();
                });
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeModal();
                }
            });
        });
    </script>
@endif
