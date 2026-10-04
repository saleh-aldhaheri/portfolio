<div class="border-main">
    <div>
        {{-- Input --}}
        <div class="mx-auto mb-10">
            <div class="w-full flex items-baseline gap-2">
                <label for="command" class="sr-only">Type a command</label>

                <span class="shrink-0 font-bold tracking-tight">
                    <a href={{config('app.url')}}>
                        <span class="text-secondary font-bold">user@portfolio:</span>
                        <span class="text-info">~</span>
                        <span>$</span>
                    </a>
                </span>

                <input id="command" type="text"
                       class="bg-transparent flex-1 min-w-0 text-sm outline-none caret-primary"
                       @keyup="Alpine.store('audio').click()"
                       @keyup.enter="$wire.runCommand(); $el.value = ''"
                       @keydown.arrow-up.prevent="$wire.historyUp().then(() => { $el.value = $wire.input; })"
                       @keydown.arrow-down.prevent="$wire.historyDown().then(() => { $el.value = $wire.input; })"
                       wire:model="input" maxlength="40" name="input"
                       autocomplete="off" spellcheck="false" aria-describedby="command-error">
            </div>
        </div>

        {{-- Render --}}
        @switch($this->command)
            @case('banner')
                <livewire:components.banner/>
                @break
            @case('help')
                <livewire:components.help/>
                @break
            @case('about')
                <livewire:components.about/>
                @break
            @case('projects')
                <livewire:components.project />
                @break
            @case('contact')
                <livewire:components.contact />
                @break
            @case('theme')
                <livewire:components.theme-list :output="$this->themeOutput" />
                @break
            @case('social')
                <livewire:components.social />
                @break
            @default
                <livewire:components.error :command="$input"/>
        @endswitch
    </div>
</div>


