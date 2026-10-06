<nav class="bg-base p-4 border-b " x-data="{ open: false }">
     <div class="container relative mx-auto flex flex-col md:flex-row justify-between md:items-center gap-4 transition-colors duration-200">
        <div class="flex items-center justify-between">
            <h1 class="text-sm md:text-main tracking-tight flex items-center gap-2">
                <a href={{config('app.url')}}>
                    <div class="flex items-center gap-2  px-4 py-3 text-sm">
                        <span class="size-2.5 rounded-full bg-error"></span>
                        <span class="size-2.5 rounded-full bg-warning"></span>
                        <span class="size-2.5 rounded-full bg-primary"></span>
                        <span class="text-primary text-lg font-bold">saleh-ahmed.dev</span>
                    </div>
                </a>
            </h1>

            <div class="flex items-center flex-row max-[400px]:flex-col gap-1 md:hidden">
                <a href="{{ asset('resume/Saleh_Ahmed_Resume.pdf') }}"
                   target="_blank"
                   aria-label="download resume as pdf"
                   class="text-primary hover:bg-primary/10 px-2 py-0.5 rounded transition-colors"
                   x-on:click="$store.audio.click(); Livewire.dispatch('log-activity', 'open-resume')"
                >
                    [ resume ]
                </a>

                <button x-on:click="open = !open; $store.audio.click()"
                        type="button"
                        class="text-muted hover:text-main p-1 focus:outline-none"
                        aria-label="Toggle terminal menu">
                    <span x-show="!open" >[+ menu]</span>
                    <span x-show="open" x-cloak class="text-warning">[- close]</span>
                </button>
            </div>
        </div>

        <div class="hidden md:flex items-center gap-4 text-sm text-main">|
            <a class="hover:text-primary hover:-translate-y-0.5 transition-all duration-150 ease-in-out" target="_blank" href="https://github.com/saleh-aldhaheri"
                x-on:click="$store.audio.click(), Livewire.dispatch('log-activity', 'open-github')"
            >
                ./Github,
            </a> |
            <a class="hover:text-primary hover:-translate-y-0.5 transition-all duration-150 ease-in-out" target="_blank" href="https://www.linkedin.com/in/saleh-aldhaheri-625b09419/"
               x-on:click="$store.audio.click(), Livewire.dispatch('log-activity', 'open-linked')"
            >
                ./LinkedIn,
            </a> |
            <a class="hover:text-primary hover:-translate-y-0.5 transition-all duration-150 ease-in-out" href="mailto:salehaldhaheri09@gmail.com"
               x-on:click="$store.audio.click(), Livewire.dispatch('log-activity', 'open-email')"
            >
                ./Email
            </a> |

            <a href="{{ asset('resume/Saleh_Ahmed_Resume.pdf') }}"
               target="_blank"
               aria-label="download resume as pdf"
               class="text-primary hover:bg-primary/10 px-2 py-0.5 rounded transition-colors"
               x-on:click="$store.audio.click(); Livewire.dispatch('log-activity', 'open-resume')"
            >
                [ resume ]
            </a>
        </div>

        <div x-show="open"
             x-cloak
             x-transition
             x-on:click.outside="open = false"
             class="md:hidden absolute top-full right-0 z-50 bg-surface border-b border-surface/80 p-4 shadow-xl flex flex-col gap-2 text-xs">
            <div class="text-muted text-[10px] uppercase tracking-widest mb-1">// Social Links</div>

            <a class="hover:text-primary py-1 flex items-center gap-2 transition-colors group" target="_blank" href="https://github.com/saleh-aldhaheri"
               x-on:click="$store.audio.click(); Livewire.dispatch('log-activity', 'open-github')"
            >
                <span class="text-primary">$</span>
                <span class="text-muted group-hover:text-primary transition-colors">cat</span>
                <span>./github</span>
            </a>

            <a class="hover:text-primary py-1 flex items-center gap-2 transition-colors group" target="_blank" href="https://www.linkedin.com/in/saleh-aldhaheri-625b09419/"
               x-on:click="$store.audio.click(); Livewire.dispatch('log-activity', 'open-linkedin')"
            >
                <span class="text-primary">$</span>
                <span class="text-muted group-hover:text-primary transition-colors">cat</span>
                <span>./linkedin</span>
            </a>

            <a class="hover:text-primary py-1 flex items-center gap-2 transition-colors group" href="mailto:salehaldhaheri09@gmail.com"
               x-on:click="$store.audio.click(); Livewire.dispatch('log-activity', 'open-email')"
            >
                <span class="text-primary">$</span>
                <span class="text-muted group-hover:text-primary transition-colors">cat</span>
                <span>./email</span>
            </a>
        </div>
     </div>
</nav>
