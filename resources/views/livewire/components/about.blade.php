<div class="w-full grid grid-cols-1 md:grid-cols-[16rem_minmax(0,1fr)] gap-8 text-sm">

    {{-- Left column: profile --}}
    <aside class="md:sticky md:top-4 self-start border border-primary/30 rounded p-4">
        <h2 class="text-lg font-bold text-primary">{{ $data['name'] }}</h2>
        <p class="opacity-80">{{ $data['role'] }}</p>

        <div class="my-3 opacity-40 select-none overflow-hidden whitespace-nowrap" aria-hidden="true">--------------------</div>

        <dl class="grid grid-cols-[4.5rem_1fr] gap-y-2">
            <dt class="font-bold text-warning">Where</dt>
            <dd>{{ $data['location'] }}</dd>

            <dt class="font-bold text-warning">Status</dt>
            <dd>{{ $data['status'] }}</dd>

            <dt class="font-bold text-warning">Speaks</dt>
            <dd>{{ $data['languages'] }}</dd>
        </dl>
    </aside>

    {{-- Right column: content --}}
    <div class="space-y-8 min-w-0">

        <section>
            <h3 class="font-bold text-warning mb-2"># about</h3>
            <p class="max-w-3xl leading-relaxed">{{ $data['intro'] }}</p>
        </section>

        <section>
            <h3 class="font-bold text-warning mb-3"># experience</h3>

            <ol class="border-l border-primary/40 ml-1.5 space-y-6">
                @foreach($data['experience'] as $job)
                    <li class="relative pl-5">
                        <span class="absolute -left-[6.5px] top-1.5 w-3 h-3 rounded-full bg-primary"></span>

                        <div class="flex flex-wrap justify-between gap-x-4">
                            <h4 class="font-bold">
                                <span class="text-primary">{{ $job['role'] }}</span>
                                <span class="opacity-70 font-normal">at {{ $job['company'] }}</span>
                            </h4>
                            <span class="opacity-60">{{ $job['period'] }}</span>
                        </div>

                        <ul class="mt-1 space-y-1">
                            @foreach($job['points'] as $point)
                                <li class="before:content-['>'] before:mr-2 before:text-primary">{{ $point }}</li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ol>
        </section>

        <section>
            <h3 class="font-bold text-warning mb-3"># skills</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6">
                @foreach($data['skills'] as $group => $items)
                    <div>
                        <h4 class="font-bold text-primary mb-1">{{ $group }}</h4>

                        <ul class="space-y-1">
                            @foreach($items as $skill)
                                <li class="before:content-['>'] before:mr-2 before:text-primary">{{ $skill }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </section>

        <section>
            <h3 class="font-bold text-warning mb-3"># education</h3>

            <ul class="space-y-3">
                @foreach($data['education'] as $item)
                    <li>
                        <div class="flex flex-wrap justify-between gap-x-4">
                            <span class="font-bold text-primary">{{ $item['degree'] }}</span>
                            <span class="opacity-60">{{ $item['year'] }}</span>
                        </div>
                        <div class="opacity-80">{{ $item['school'] }}</div>
                    </li>
                @endforeach
            </ul>
        </section>

        <section>
            <h3 class="font-bold text-warning mb-3"># certificates</h3>

            <ul class="space-y-3">
                @foreach($data['certificates'] as $cert)
                    <li>
                        <div class="flex flex-wrap justify-between gap-x-4">
                            <span class="font-bold text-primary">{{ $cert['name'] }}</span>

                            @isset($cert['date'])
                                <span class="opacity-60">{{ $cert['date'] }}</span>
                            @endisset
                        </div>

                        <div class="opacity-80">{{ $cert['issuer'] }}</div>

                        @isset($cert['url'])
                            <a href="{{ $cert['url'] }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               aria-label="View credential: {{ $cert['name'] }}"
                               class="underline text-primary hover:opacity-80">
                                View credential
                            </a>
                        @endisset
                    </li>
                @endforeach
            </ul>
        </section>

    </div>
</div>
