<div class="w-full space-y-4 text-sm">
    @foreach($groups as $title => $commands)
        <section>
            <h3 class="font-bold text-secondary mb-1"># {{ $title }}</h3>

            <dl class="grid grid-cols-1 sm:grid-cols-[14rem_1fr] gap-x-6 gap-y-1">
                @foreach($commands as $item)
                    <dt class="font-bold text-primary whitespace-nowrap">
                        {{ $item['command'] }}
                    </dt>
                    <dd class="opacity-80 mb-2 sm:mb-0">
                        {{ $item['description'] }}
                    </dd>
                @endforeach
            </dl>
        </section>
    @endforeach
    <di>
        <p class="text-muted">
             Tip: use ↑ ↓ for history
        </p>
    </di>
</div>
