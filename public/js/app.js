document.addEventListener('alpine:init', () => {
    const pad = (value) => String(value).padStart(2, '0');

    const format = (milliseconds) => {
        const totalSeconds = Math.floor(milliseconds / 1000);
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);

        return `${hours}:${pad(minutes)}:${pad(totalSeconds % 60)}`;
    };

    Alpine.data('elapsed', () => {
        let interval = null;

        return {
            display: '',

            init() {
                const startedAt = Date.parse(this.$el.dataset.startedAt);
                const offset = Date.now() - Date.parse(this.$el.dataset.serverNow);
                const tick = () => {
                    this.display = format((Date.now() - offset) - startedAt);
                };

                tick();
                interval = setInterval(tick, 1000);
            },

            destroy() {
                clearInterval(interval);
            },
        };
    });
});
