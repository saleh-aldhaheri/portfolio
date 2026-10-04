<div class="px-4">
    <header class="mb-6 text-center">
        <h2 class="mb-2 text-xl font-bold text-info"><span class="text-primary">$</span>./Social</h2>
        <p class="text-sm text-warning">- find me online -</p>
    </header>

    <section aria-label="Social links" class="mx-auto max-w-3xl">
        <ul class="space-y-3">
            @foreach ($data as $item)
                <li>
                    <a href="{{ $item['link'] }}" target="_blank" rel="noopener noreferrer"
                       aria-label="{{ $item['name'] }} (new tab)"
                       class="group flex items-center gap-4 rounded-lg border px-5 py-4 transition-colors
                              hover:border-primary hover:bg-primary/5
                              focus-visible:border-primary focus-visible:bg-primary/5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">

                        <span class="w-28 shrink-0 font-bold text-primary">{{ Str::lower($item['name']) }}</span>

                        <span aria-hidden="true" class="text-muted group-hover:text-primary">→</span>

                        <span class="min-w-0 flex-1 truncate text-sm text-outputText sm:text-base">
                            {{ Str::of($item['link'])->after('://')->rtrim('/') }}
                        </span>

                        <span aria-hidden="true" class="hidden text-sm text-muted group-hover:text-primary sm:inline">
                            [ open ]
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
    </section>
</div>
