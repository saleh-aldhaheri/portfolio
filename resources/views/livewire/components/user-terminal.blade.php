<div class="border-main">
    <div>
        {{-- Input --}}
        <div class="mx-auto mb-3">
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
                       @keydown.arrow-up.prevent="$wire.historyUp().then(() => { $el.value = $wire.command; })"
                       @keydown.arrow-down.prevent="$wire.historyDown().then(() => { $el.value = $wire.command; })"
                       wire:model="command" maxlength="40" name="command"
                       autocomplete="off" spellcheck="false" aria-describedby="command-error">
            </div>
        </div>
        {{-- Render --}}
            @if($component['command'] !== 'banner')
                <div class="font-bold">
                    <div class="text-primary">user@portfolio:~$ {{$component['command']}}</div>
                </div>
            @endif
            <div class="pb-3">
                @livewire($component['component'], $component['props'], key($component['key']))
            </div>
    </div>
</div>


