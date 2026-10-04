<div class="w-full text-sm">
    <h2 class="mb-3 font-bold text-primary">Available commands</h2>

    <dl class="grid grid-cols-1 gap-y-2 sm:grid-cols-[max-content_1fr] sm:gap-x-6">
        @foreach ($data as $item)
            <dt class="font-bold text-primary whitespace-nowrap">{{ $item['command'] }}</dt>
            <dd class="opacity-80 mb-2 sm:mb-0">{{ $item['description'] }}</dd>
        @endforeach
    </dl>

    <p class="mt-4 border-t pt-3 text-muted">
        Tip: use <kbd class="rounded border px-1.5 text-xs">↑</kbd>
        <kbd class="rounded border px-1.5 text-xs">↓</kbd> for history
    </p>
</div>
