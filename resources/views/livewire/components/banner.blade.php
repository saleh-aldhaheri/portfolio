<div class="justify-between items-center text-primary text-xs">
    <div class="mt-5">
        <x-ascii-art-builder
            :speed="10"
            name="name"
        />
    </div>
    <div class="mt-5">
        <x-ascii-art-builder
            :speed="10"
            :delay="1000"
            name="software"
        />
    </div>
    <div class="flex flex-col py-4  xl:flex-row mt-5  justify-center">
        <x-ascii-art-builder
            name="keyboard"
            :delay="2000"
            :speed="1"
        >
        </x-ascii-art-builder>
        <x-ascii-art-builder
            name="mouse"
            :delay="2000"
            :speed="1"
        >
        </x-ascii-art-builder>
    </div>
</div>
