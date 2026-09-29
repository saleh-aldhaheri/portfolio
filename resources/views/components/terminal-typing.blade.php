@props([
    'text' => '',
    'duration' => '3.5s',
    'delay' => '0s',
    'steps' => 60,
    'cursor' => true,
    'cursorColor' => 'border-primary',
    'as' => 'div',
    'borderDirection' => 'r',
    'loop' => false,
])

@php
    $borderClass = $cursor ? "border-{$borderDirection}-2 {$cursorColor} animate-terminal-blink" : '';
    $iteration = $loop ? 'infinite' : 'forwards';
@endphp

<{{ $as }}
    {{ $attributes->merge([
        'class' => "inline-block overflow-hidden whitespace-nowrap max-w-max font-mono {$borderClass}"
    ]) }}
    style="animation: typing {{ $duration }} steps({{ $steps }}, end) {{ $delay }} {{ $iteration }}, blink 0.75s step-end infinite; width: 0;"
>
{{ $text ?: $slot }}
</{{ $as }}>
