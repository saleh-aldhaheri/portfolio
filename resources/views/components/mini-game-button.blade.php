<div x-data="{ open: false }" class="fixed top-4 left-4 z-50">
    <button
        type="button"
        aria-label="Open mini game"
        class="flex h-11 w-11 items-center justify-center rounded-full border border-surface/80 bg-surface text-primary shadow-lg transition-all hover:scale-105 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-primary"
        x-show="!open"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-0"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-0"
        x-on:click="open = true; Livewire.dispatch('log-activity', 'open-game')"
    >
        <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <line x1="6" y1="11" x2="10" y2="11" />
            <line x1="8" y1="9" x2="8" y2="13" />
            <line x1="15" y1="12" x2="15.01" y2="12" />
            <line x1="18" y1="10" x2="18.01" y2="10" />
            <path d="M17.32 5H6.68a4 4 0 0 0-3.978 3.59c-.006.052-.01.101-.017.152C2.604 9.416 2 14.456 2 16a3 3 0 0 0 3 3c1 0 1.5-.5 2-1l1.414-1.414A2 2 0 0 1 9.828 16h4.344a2 2 0 0 1 1.414.586L17 18c.5.5 1 1 2 1a3 3 0 0 0 3-3c0-1.545-.604-6.584-.685-7.258-.007-.05-.011-.1-.017-.151A4 4 0 0 0 17.32 5z" />
        </svg>
    </button>

    <div
        x-show="open"
        x-cloak
        x-transition
        x-on:click.outside="open = false"
        x-on:keydown.escape.window="open = false"
        class="absolute top-full left-0 mt-3 w-72 sm:w-80"
    >
        <div class="mb-1 flex justify-end">
            <button
                type="button"
                aria-label="Close mini game"
                class="flex h-6 w-6 items-center justify-center rounded-full bg-surface text-muted transition-colors hover:text-warning focus:outline-none"
                x-on:click="open = false"
            >
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                    <path d="M6 6l12 12M18 6L6 18" />
                </svg>
            </button>
        </div>

        <livewire:components.mini-game />
    </div>
</div>
