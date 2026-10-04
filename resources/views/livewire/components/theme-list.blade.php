<div class="w-full space-y-4 text-sm">
    @if($output)
        <div>{{ $output }}</div>
    @else
        <dl>
            @foreach($themes as $themeName)
                <dt class="font-bold whitespace-nowrap mb-2">{{ $themeName }}</dt>
            @endforeach
        </dl>
    @endif
</div>
