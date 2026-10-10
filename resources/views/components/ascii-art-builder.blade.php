@props([
    'name' => '',
    'speed' => 1,
    'delay' => 0,
])

@php
    $data = \App\Services\DataResolverService::resolve('ascii');
    $art = $data[$name] ?? '';

    // Widest line in display columns drives the responsive font size so the
    // art always fits the viewport instead of overflowing horizontally.
    $columns = 1;
    foreach (explode("\n", $art) as $line) {
        $columns = max($columns, mb_strlen($line, 'UTF-8'));
    }

    // Monospace glyphs advance ~0.6em across the font stack; reserve a small
    // safety margin (0.65) plus the padding/gutters (100vw - 4rem) so the art
    // always fits and never surfaces a horizontal scrollbar.
    $fitDivisor = round($columns * 0.65, 2);
@endphp

<div
    x-data="{
        text: @js($data[$name]),
        displayText: '',
        index: 0,
        display() {
            let interval = setInterval(() => {
                if (this.index < this.text.length) {
                    this.index += {{ 10 }};
                    this.displayText = this.text.slice(0, this.index);
                } else {
                    clearInterval(interval);
                }
            }, {{ $speed }});
        }
    }"
    x-init="setTimeout(() => display(), {{ $delay }})"
    class="h-full max-w-full flex items-center justify-center overflow-x-auto [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
>
    <pre class="font-mono leading-none p-4 rounded"
         style="font-size: min(0.75rem, calc((100vw - 4rem) / {{ $fitDivisor }}));"
         x-text="displayText"></pre>
</div>
