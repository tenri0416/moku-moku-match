<script>
  document.addEventListener('DOMContentLoaded', function () {
      const modal = document.getElementById('notificationModal');
      const openButtons = document.querySelectorAll('[data-open-notification-modal]');
      const closeButtons = document.querySelectorAll('[data-close-notification-modal]');
      const tabButtons = document.querySelectorAll('[data-notification-tab-button]');
      const tabPanels = document.querySelectorAll('[data-notification-tab-panel]');

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

      function switchTab(tabName) {
          tabButtons.forEach(function (button) {
              const isActive = button.dataset.notificationTabButton === tabName;

              button.classList.toggle('border-indigo-700', isActive);
              button.classList.toggle('text-slate-900', isActive);

              button.classList.toggle('border-transparent', !isActive);
              button.classList.toggle('text-slate-400', !isActive);
          });

          tabPanels.forEach(function (panel) {
              const isActive = panel.dataset.notificationTabPanel === tabName;
              panel.classList.toggle('hidden', !isActive);
          });
      }

      openButtons.forEach(function (button) {
          button.addEventListener('click', function () {
              const details = button.closest('details');

              if (details) {
                  details.removeAttribute('open');
              }

              openModal();
          });
      });

      closeButtons.forEach(function (button) {
          button.addEventListener('click', closeModal);
      });

      tabButtons.forEach(function (button) {
          button.addEventListener('click', function () {
              switchTab(button.dataset.notificationTabButton);
          });
      });

      document.addEventListener('keydown', function (event) {
          if (event.key === 'Escape') {
              closeModal();
          }
      });

      switchTab('general');
  });
</script>
