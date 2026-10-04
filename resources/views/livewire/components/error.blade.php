<div class="ml-2 text-sm sm:ml-4 sm:text-main ">
    <p role="alert" class="text-error break-words">
        <span aria-hidden="true">❌</span>
        {{ filled($command ?? null) ? $command : 'error' }}: command not found
    </p>

    <p class="mt-3 text-warning">
        <span aria-hidden="true">💡</span> Did you mean:
    </p>

    <dl class="mt-2 grid grid-cols-1 gap-y-2 sm:grid-cols-[max-content_1fr] sm:gap-x-4">
      <livewire:components.help />
    </dl>
</div>
