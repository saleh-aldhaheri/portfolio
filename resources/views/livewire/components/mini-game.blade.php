<div
    class="w-full bg-surface text-main border border-surface rounded-lg shadow-2xl font-mono"
    x-data="{
        target: 0,
        score: 0,
        timeLeft: 15,
        running: false,
        timer: null,

        init() {
            this.target = this.randomCell();
        },

        randomCell() {
            return Math.floor(Math.random() * 9) + 1;
        },

        start() {
            if (this.running) return;

            this.running = true;
            this.score = 0;
            this.timeLeft = 15;
            this.target = this.randomCell();

            this.timer = setInterval(() => {
                this.timeLeft--;

                if (this.timeLeft <= 0) {
                    this.running = false;
                    clearInterval(this.timer);
                    this.timer = null;
                }
            }, 1000);
        },

        tap(cell) {
            if (!this.running || cell !== this.target) return;

            this.score++;
            this.target = this.randomCell();
        }
    }"
>
    <div class="flex items-center justify-between border-b border-surface/80 px-4 py-3">
        <span class="text-sm text-primary">./mini-game</span>
        <span class="text-xs text-muted" x-text="'score ' + score"></span>
    </div>

    <div class="px-4 py-4">
        <p class="mb-3 text-xs text-muted">
            Tap the <span class="text-primary">*</span> before the timer runs out.
        </p>

        <div class="grid grid-cols-3 gap-2">
            <template x-for="cell in 9" :key="cell">
                <button
                    type="button"
                    class="flex aspect-square items-center justify-center rounded border border-surface/80 bg-base text-xl transition-colors hover:bg-primary/10"
                    :aria-label="'cell ' + cell"
                    x-on:click="tap(cell)"
                >
                    <span
                        x-show="cell === target"
                        class="animate-pulse motion-reduce:animate-none text-primary"
                        aria-hidden="true"
                    >*</span>
                </button>
            </template>
        </div>

        <div class="mt-4 flex items-center justify-between text-xs">
            <button
                type="button"
                class="rounded border border-surface/80 px-3 py-1.5 text-primary transition-colors hover:bg-primary/10"
                x-on:click="start()"
            >
                [ play ]
            </button>

            <span class="text-muted" x-text="timeLeft + 's'"></span>
        </div>
    </div>
</div>
