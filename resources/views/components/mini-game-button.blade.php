<div x-data="{ open: false }" class="fixed bottom-4 left-4 z-50">
    <button
        type="button"
        class="font-mono text-sm text-primary rounded border border-surface/80 bg-base px-3 py-2 shadow-lg transition-colors hover:bg-primary/10 focus:outline-none"
        x-on:click="open = true; Livewire.dispatch('log-activity', 'open-game')"
        aria-label="Open mini game"
    >
        [ play ]
    </button>

    <div
        x-show="open"
        x-cloak
        x-transition
        x-on:click.outside="open = false"
        x-on:keydown.escape.window="open = false"
        class="absolute bottom-full left-0 mb-3 w-72 sm:w-80"
    >
        <div class="mb-1 flex justify-start">
            <button
                type="button"
                class="font-mono text-xs text-muted transition-colors hover:text-warning"
                x-on:click="open = false"
            >
                [ close ]
            </button>
        </div>

        <livewire:components.mini-game />
    </div>
</div>
