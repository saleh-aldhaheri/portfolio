<div
    class="fixed right-6 bottom-6 z-[100] font-mono"
    x-data="{ open: false }"
    @keydown.escape.window="open = false"
>

    <!-- Click-away overlay: closes the chat when clicking outside the box -->
    <div
        x-show="open"
        x-cloak
        @click="open = false"
        class="fixed inset-0"
    ></div>

    <!-- Toggle button -->
    <button
        type="button"
        aria-label="Open chat"
        class="absolute right-0 bottom-0 w-13 h-13 rounded-full bg-primary text-black cursor-pointer flex items-center justify-center hover:scale-105 transition-all origin-bottom-right focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-primary"
        x-show="!open"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-0"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-0"
        @click="open = true"
    >
        <svg viewBox="0 0 12 12" class="w-9 h-9" shape-rendering="crispEdges" aria-hidden="true">
            <!-- antenna -->
            <rect x="5" y="0" width="2" height="2" fill="currentColor"/>
            <!-- head -->
            <rect x="1" y="2" width="10" height="7" fill="currentColor"/>
            <!-- ears -->
            <rect x="0" y="4" width="1" height="3" fill="currentColor"/>
            <rect x="11" y="4" width="1" height="3" fill="currentColor"/>
            <!-- eyes and mouth (cut out in the button color) -->
            <rect x="3" y="4" width="2" height="2" class="fill-primary"/>
            <rect x="7" y="4" width="2" height="2" class="fill-primary"/>
            <rect x="3" y="7" width="6" height="1" class="fill-primary"/>
            <!-- shoulders -->
            <rect x="3" y="10" width="6" height="2" fill="currentColor"/>
        </svg>
    </button>

    <!-- Chat window -->
    <div
        role="dialog"
        aria-label="Chat"
        class="absolute right-0 bottom-0 w-[22rem] max-w-[calc(100vw-3rem)] h-[30rem] max-h-[calc(100vh-3rem)] flex flex-col bg-surface border border-primary/40 rounded-xl shadow-[0_0_40px_rgba(34,197,94,0.18)] overflow-hidden origin-bottom-right"
        x-show="open"
        x-cloak
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
    >
        <!-- Title bar -->
        <div class="flex items-center gap-2 px-4 py-3 border-b border-primary/30 shrink-0">
            <button type="button" aria-label="Close chat" class="w-3 h-3 rounded-full bg-red-500 cursor-pointer" @click="open = false"></button>
            <span class="w-3 h-3 rounded-full bg-amber-400"></span>
            <span class="w-3 h-3 rounded-full bg-primary"></span>
            <span class="ml-2 text-sm text-primary">chat ~ saleh-ahmed</span>
        </div>

        <!-- Messages (Livewire: loop your messages here) -->
        <div class="flex-1 overflow-y-auto px-4 py-3 space-y-3 text-sm leading-relaxed">

            {{-- AI message --}}
            <div class="flex gap-2 text-primary w-full">
                <span class="shrink-0 select-none">&gt;</span>
                <p class="break-words min-w-0">
                    hi, I'm saleh assistant, ask me anything about saleh's projects, skills or even what kind of person he ^_^.
                    I know a lot about him, he is my friend.
                    <svg viewBox="0 0 12 12" class="inline-block w-[1.1em] h-[1.1em] align-[-0.15em] ml-1" fill="currentColor" fill-rule="evenodd" shape-rendering="crispEdges" aria-hidden="true">
                        <path d="M5 0h2v2H5z M1 2h10v2h1v3h-1v2H1V7H0V4h1z M3 4h2v2H3z M7 4h2v2H7z M3 7h6v1H3z M3 10h6v2H3z"/>
                    </svg>
                </p>
            </div>

            {{-- User message --}}
            <div class="flex gap-2 text-main justify-end w-ful ">
                <span class="shrink-0 select-none text-blue-500">$</span>
                <p class="break-words min-w-0">what do you work with?</p>
            </div>

            <div class="flex gap-2 text-primary">
                <span class="select-none">&gt;</span>
                <span class="animate-pulse">...</span>
            </div>
        </div>

        <form class="flex items-center gap-2 px-4 py-3 border-t border-primary/30 shrink-0">
            <span class="text-blue-500 select-none">$</span>
            <input
                type="text"
                autocomplete="off"
                placeholder="type a message"
                aria-label="Message"
                class="flex-1 min-w-0 bg-transparent placeholder:text-main/30 text-sm outline-none caret-primary"
            >
            <button type="submit" class="text-primary text-sm cursor-pointer hover:underline">[ send ]</button>
        </form>
    </div>

</div>
