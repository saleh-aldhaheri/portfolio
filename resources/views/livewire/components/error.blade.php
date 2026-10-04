<div class="ml-2 text-sm sm:ml-4 sm:text-base" style="color: var(--theme-outputText);">
    <p role="alert" class="text-error break-words">
        <span aria-hidden="true">❌</span>
        {{ filled($command ?? null) ? $command : 'error' }}: command not found
    </p>

    <p class="mt-3 text-warning">
        <span aria-hidden="true">💡</span> Did you mean:
    </p>

    <dl class="mt-2 grid grid-cols-1 gap-y-2 sm:grid-cols-[max-content_1fr] sm:gap-x-4">
        @foreach ($data as $item)
            <dt class="whitespace-nowrap font-bold text-primary sm:after:ml-3 sm:after:font-normal sm:after:text-muted sm:after:content-['→']">
                {{ $item['command'] }}
            </dt>
            <dd class="mb-2 opacity-80 sm:mb-0">{{ $item['description'] }}</dd>
        @endforeach
    </dl>
</div>
