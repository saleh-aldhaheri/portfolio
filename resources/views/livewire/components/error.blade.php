<div>
    <p id="command-error" role="alert" class="mt-2 text-sm">
    <div class="ml-2 sm:ml-4 text-sm sm:text-base" style="color: var(--theme-outputText);">
        <div style="color: var(--theme-error);">
            <pre>❌ {{$command ?? 'error'}}: command not found</pre>
        </div>
        <div style="color: var(--theme-outputText);">
            <pre></pre>
        </div>
        <div style="color: var(--theme-warning);">
            <pre>💡 Did you mean:</pre>
        </div>
        <div style="color: var(--theme-outputText);">
            <pre>   • help      - Show available commands</pre>
        </div>
        <div style="color: var(--theme-outputText);">
            <pre>   • projects  - View my projects</pre>
        </div>
        <div style="color: var(--theme-outputText);">
            <pre>   • about     - Learn about me</pre>
        </div>
        <div style="color: var(--theme-outputText);">
            <pre>   • contact   - Contact me</pre>
        </div>
        <div class="break_words">
            <pre>   • banner    - To display the banner</pre>
        </div>
        <div style="color: var(--theme-outputText);">
            <pre>   • clear     - Clear everything</pre>
        </div>
    </div>
    </p>
</div>
