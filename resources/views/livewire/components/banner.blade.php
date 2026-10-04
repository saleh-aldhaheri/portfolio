<div class="justify-between items-center text-primary text-xs">
    <div class="mt-2">
        <x-ascii-art-builder
            :speed="10"
            name="name"
        />
    </div>

    <div class="mt-2">
        <x-ascii-art-builder
            :speed="10"
            :delay="1000"
            name="software"
        />
    </div>
    <div  x-on:click="Alpine.store('jump').click()" wire:click="setInput('help')" class="mt-2 z-50 text-warning animate-pulse cursor-pointer transition-transform duration-200 ease-in-out hover:-translate-y-2.5">
        <x-ascii-art-builder
            :speed="10"
            :delay="2000"
            name="type-help"
        />
    </div>
    <div class="flex flex-col py-4  xl:flex-row mt-5  justify-center">
        <x-ascii-art-builder
            name="keyboard"
            :delay="3000"
            :speed="1"
        >
        </x-ascii-art-builder>
        <x-ascii-art-builder
            name="mouse"
            :delay="3000"
            :speed="1"
        >
        </x-ascii-art-builder>
    </div>
</div>
