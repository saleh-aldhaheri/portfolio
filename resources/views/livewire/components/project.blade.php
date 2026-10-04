<div class="px-2 text-sm text-muted">
    <header class="mb-6 text-center">
        <h2 class="mb-2 text-xl font-bold text-info"><span class="text-primary">$</span> ls projects/</h2>
        <p class="text-xs text-warning">- most recent works -</p>
    </header>

    <nav aria-label="Project categories">
        <ul class="mb-8 flex flex-wrap justify-center gap-2">
            @foreach($data['categories'] as $c)
                <li>
                    <button type="button"
                            wire:click="setCategory('{{ $c }}')"
                            aria-pressed="{{ $c === $category ? 'true' : 'false' }}"
                            class="rounded text-w px-3 py-1 before:content-['[_'] after:content-['_]']
                                   focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2
                                   {{ $c === $category ? 'bg-primary font-bold text-black' : 'hover:text-outputText' }}">
                        {{ $c }}
                    </button>
                </li>
            @endforeach
        </ul>
    </nav>

    <div class="mx-auto grid max-w-4xl grid-cols-[repeat(auto-fit,minmax(14rem,16rem))] justify-center gap-10">
        @foreach($data['data'] as $project)
            @if($category !== 'all' && ! in_array($category, $project['categories']))
                @continue
            @endif

            <article class="group overflow-hidden rounded-lg border transition-colors hover:border-primary focus-within:border-primary">

                <div class="flex items-center gap-1.5 border-b px-3 py-2 text-xs">
                    <span class="size-2 rounded-full  bg-error"></span>
                    <span class="size-2 rounded-full  bg-warning"></span>
                    <span class="size-2 rounded-full  bg-primary"></span>
                    <span class="ml-1 truncate">{{ $project['file'] ?? Str::slug($project['name']) }}</span>
                </div>

                <div class="p-3">
                    <div class="relative flex aspect-square items-center justify-center overflow-hidden rounded-md border">
                        <span aria-hidden="true" class="text-4xl font-bold tracking-widest text-primary opacity-60">
                            {{ Str::upper(Str::substr($project['name'], 0, 2)) }}
                        </span>

                        <img src="{{ Storage::url($project['image']) }}"
                             alt="Preview of {{ $project['name'] }}"
                             width="400" height="400" loading="lazy"
                             onerror="this.remove()"
                             class="absolute inset-0 size-full object-cover">
                    </div>

                    <h3 class="mb-1 mt-3 text-outputText group-hover:before:text-primary group-hover:before:content-['>_']">
                        {{ $project['name'] }}
                    </h3>

                    <ul class="flex flex-wrap gap-x-4 gap-y-1 text-xs">
                        @if(filled($project['link'] ?? null))
                            <li>
                                <a href="{{ $project['link'] }}" target="_blank" rel="noopener noreferrer"
                                   aria-label="Open live site of {{ $project['name'] }} (new tab)"
                                   class="hover:text-primary focus-visible:text-primary">link →</a>
                            </li>
                        @endif
                        @if(filled($project['github'] ?? null))
                            <li>
                                <a href="{{ $project['github'] }}" target="_blank" rel="noopener noreferrer"
                                   aria-label="Open GitHub repository of {{ $project['name'] }} (new tab)"
                                   class="hover:text-primary focus-visible:text-primary">github →</a>
                            </li>
                        @endif
                    </ul>
                </div>
            </article>
        @endforeach
    </div>
</div>
