<footer class="text-center py-6 text-main border-t border-muted/40 w-full">
    <blockquote class="pb-4 flex flex-col items-center">

        <x-terminal-typing
            as="p"
            duration="3s"
            steps="80"
            :cursor="false"
            class="px-2 text-xs italic leading-relaxed"
        >
            “and whoever is patient, allah will make him patient. and no one has ever been given a gift better and more encompassing than patience.”
        </x-terminal-typing>

        <x-terminal-typing
            as="div"
            duration="2s"
            delay="3.2s"
            steps="40"
            :cursor="false"
            class="mt-2 text-xs not-italic"
        >
            — prophet muhammad (sahih al-bukhari)
        </x-terminal-typing>

    </blockquote>

    <p class="text-xs italic">
        © {{ now()->year }} Saleh Al-Dhaheri. Type <span class="text-primary">"help"</span> for commands.
    </p>
</footer>
