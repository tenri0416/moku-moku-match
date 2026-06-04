<script>
  document.addEventListener('DOMContentLoaded', () => {
      const ratingLabels = {
          1: '1：不満',
          2: '2：やや不満',
          3: '3：普通',
          4: '4：満足',
          5: '5：とても満足',
      };

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

      setupRating('pc');
      setupRating('sp');
      setupCounter('pc');
      setupCounter('sp');

      document.querySelectorAll('[data-satisfaction-survey-close]').forEach((button) => {
          button.addEventListener('click', () => {
              document.getElementById('satisfaction-survey-modal-pc')?.classList.add('hidden');
              document.getElementById('satisfaction-survey-modal-sp')?.classList.add('hidden');
              document.body.classList.remove('overflow-hidden');
          });
      });

      if (
          document.getElementById('satisfaction-survey-modal-pc')
          || document.getElementById('satisfaction-survey-modal-sp')
      ) {
          document.body.classList.add('overflow-hidden');
      }
  });
</script>
