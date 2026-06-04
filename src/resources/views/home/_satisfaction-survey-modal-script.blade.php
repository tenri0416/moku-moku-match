<script>
  document.addEventListener('DOMContentLoaded', () => {
      const ratingLabels = {
          1: '1：不満',
          2: '2：やや不満',
          3: '3：普通',
          4: '4：満足',
          5: '5：とても満足',
      };

      const pcModal = document.getElementById('satisfaction-survey-modal-pc');
      const spModal = document.getElementById('satisfaction-survey-modal-sp');

      const setupRating = (type) => {
          const group = document.querySelector(`[data-satisfaction-rating-group="${type}"]`);
          const input = document.getElementById(`satisfaction-survey-rating-${type}`);
          const label = document.getElementById(`satisfaction-survey-rating-label-${type}`);

          if (!group || !input || !label) {
              return;
          }

          const stars = Array.from(group.querySelectorAll('[data-satisfaction-rating]'));

          const paintStars = (rating) => {
              stars.forEach((star) => {
                  const value = Number(star.dataset.satisfactionRating);

                  star.classList.toggle('text-amber-400', value <= rating);
                  star.classList.toggle('text-slate-300', value > rating);
              });
          };

          stars.forEach((star) => {
              star.addEventListener('click', () => {
                  const rating = Number(star.dataset.satisfactionRating);

                  input.value = rating;
                  label.textContent = ratingLabels[rating] ?? '';
                  label.classList.remove('hidden');

                  paintStars(rating);
              });
          });
      };

      const setupCounter = (type) => {
          const textarea = document.getElementById(`satisfaction-survey-improvement-${type}`);
          const count = document.getElementById(`satisfaction-survey-count-${type}`);

          if (!textarea || !count) {
              return;
          }

          const updateCount = () => {
              count.textContent = textarea.value.length;
          };

          textarea.addEventListener('input', updateCount);
          updateCount();
      };

      /**
       * アンケートモーダルを閉じる。
       *
       * 「あとで回答する」用。
       * DBには保存しないため、次回ホーム画面アクセス時に再表示される。
       *
       * Tailwindの hidden と md:flex が競合するため、
       * class だけでなく style.display = 'none' で確実に非表示にする。
       */
      const closeSurveyModalOnly = () => {
          if (pcModal) {
              pcModal.classList.add('hidden');
              pcModal.style.display = 'none';
              pcModal.setAttribute('aria-hidden', 'true');
          }

          if (spModal) {
              spModal.classList.add('hidden');
              spModal.style.display = 'none';
              spModal.setAttribute('aria-hidden', 'true');
          }

          document.body.classList.remove('overflow-hidden');
      };

      setupRating('pc');
      setupRating('sp');
      setupCounter('pc');
      setupCounter('sp');

      document.querySelectorAll('[data-satisfaction-survey-close]').forEach((button) => {
          button.addEventListener('click', closeSurveyModalOnly);
      });

      /**
       * モーダルが表示対象の場合は、背景スクロールを止める。
       */
      if (pcModal || spModal) {
          document.body.classList.add('overflow-hidden');
      }

      /**
       * 背景クリックでも「あとで回答する」と同じ扱いで閉じる。
       */
      pcModal?.addEventListener('click', (event) => {
          if (event.target === pcModal) {
              closeSurveyModalOnly();
          }
      });

      spModal?.addEventListener('click', (event) => {
          if (event.target === spModal) {
              closeSurveyModalOnly();
          }
      });

      /**
       * Escキーでも「あとで回答する」と同じ扱いで閉じる。
       */
      document.addEventListener('keydown', (event) => {
          const isPcVisible = pcModal && pcModal.style.display !== 'none' && !pcModal.classList.contains('hidden');
          const isSpVisible = spModal && spModal.style.display !== 'none' && !spModal.classList.contains('hidden');

          if (event.key === 'Escape' && (isPcVisible || isSpVisible)) {
              closeSurveyModalOnly();
          }
      });
  });
</script>
