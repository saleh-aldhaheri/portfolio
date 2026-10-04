<div class="px-4">
    <header class="mb-6 text-center">
        <h2 class="mb-2 text-xl font-bold text-info"><span class="text-primary">$</span>./ Help</h2>
        <p class="text-sm text-warning">- available commands -</p>
    </header>
    <div class="mx-auto max-w-3xl overflow-hidden rounded-lg border">
        <div class="flex items-center gap-2 border-b px-4 py-3 text-sm">
            <span class="size-2.5 rounded-full bg-error"></span>
            <span class="size-2.5 rounded-full bg-warning"></span>
            <span class="size-2.5 rounded-full bg-primary"></span>
            <span class="ml-1 truncate">help.txt</span>
        </div>

        <div class="p-6 sm:p-8">
            <dl class="grid grid-cols-1 gap-y-2 sm:grid-cols-[max-content_1fr] sm:gap-x-4 sm:gap-y-3">
                @foreach ($data as $item)
                    <dt class="whitespace-nowrap font-bold text-primary sm:after:ml-3 sm:after:font-normal sm:after:text-muted sm:after:content-['→']">
                        {{ $item['command'] }}
                    </dt>
                    <dd class="mb-3 text-outputText sm:mb-0">{{ $item['description'] }}</dd>
                @endforeach
            </dl>

            <p class="mt-6 border-t pt-4 text-sm">
                Tip: use <kbd class="rounded border px-1.5 text-xs">↑</kbd>
                <kbd class="rounded border px-1.5 text-xs">↓</kbd> for history
            </p>
        </div>
    </div>
</div>
