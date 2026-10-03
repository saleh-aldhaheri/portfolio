@props([
    'name' => '',
    'speed' => 1,
    'delay' => 0,
])

<div
    x-data="{
        text: @js(config('ascii.'.$name)),
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
    class="h-full flex items-center justify-center"
>
    <pre class="font-mono leading-none bg-black p-4 rounded" x-text="displayText"></pre>
</div>
