export class Poller {
  constructor({ interval = 2000, callback }) {
      this.interval = interval;
      this.callback = callback;
      this.timerId = null;
      this.isRunning = false;
      this.isProcessing = false;
  }

  start() {
      if (this.isRunning) {
          return;
      }

      this.isRunning = true;
      this.execute();

      this.timerId = window.setInterval(() => {
          this.execute();
      }, this.interval);
  }

  stop() {
      this.isRunning = false;

      if (this.timerId) {
          window.clearInterval(this.timerId);
          this.timerId = null;
      }
  }

  async execute() {
      if (this.isProcessing) {
          return;
      }

      if (document.hidden) {
          return;
      }

      this.isProcessing = true;

      try {
          await this.callback();
      } catch (error) {
          console.error('Polling failed:', error);
      } finally {
          this.isProcessing = false;
      }
  }
}
