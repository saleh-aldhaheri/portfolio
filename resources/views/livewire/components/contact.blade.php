<div class="px-4">
    <header class="mb-6 text-center">
        <h2 class="mb-2 text-xl font-bold text-info"><span class="text-primary">$</span>./Contact</h2>
        <p class="text-sm text-warning">- leave me a message -</p>
    </header>

    <div class="mx-auto grid min-h-[55vh] max-w-6xl items-stretch gap-6 md:grid-cols-[1fr_1.6fr] lg:gap-8">

        {{-- left: contact info --}}
        <section aria-label="Contact details"
                 class="flex flex-col justify-between rounded-lg border p-6 text-sm leading-8 sm:p-8">
            <div>
                <p class="mb-3">$ cat contact.json</p>
                <p aria-hidden="true">{</p>

                <dl class="pl-5">
                    <div class="flex flex-wrap gap-x-2">
                        <dt class="text-info"><span aria-hidden="true">"</span>email<span aria-hidden="true">":</span></dt>
                        <dd>
                            <a href="mailto:salehaldhaheri09@gmail.com"
                               class="break-all text-outputText hover:text-primary hover:underline focus-visible:text-primary">
                                <span aria-hidden="true">"</span>salehaldhaheri09@gmail.com<span aria-hidden="true">"</span></a><span aria-hidden="true">,</span>
                        </dd>
                    </div>

                    <div class="flex flex-wrap gap-x-2">
                        <dt class="text-info"><span aria-hidden="true">"</span>github<span aria-hidden="true">":</span></dt>
                        <dd>
                            <a href="https://github.com/saleh-aldhaheri" target="_blank" rel="noopener noreferrer"
                               aria-label="GitHub profile (new tab)"
                               class="text-outputText hover:text-primary hover:underline focus-visible:text-primary">
                                <span aria-hidden="true">"</span>saleh-aldhaheri<span aria-hidden="true">"</span></a><span aria-hidden="true">,</span>
                        </dd>
                    </div>

                    <div class="flex flex-wrap gap-x-2">
                        <dt class="text-info"><span aria-hidden="true">"</span>linkedin<span aria-hidden="true">":</span></dt>
                        <dd>
                            <a href="https://www.linkedin.com/in/saleh-aldhaheri-625b09419/" target="_blank" rel="noopener noreferrer"
                               aria-label="LinkedIn profile (new tab)"
                               class="text-outputText hover:text-primary hover:underline focus-visible:text-primary">
                                <span aria-hidden="true">"</span>saleh-aldhaheri<span aria-hidden="true">"</span></a><span aria-hidden="true">,</span>
                        </dd>
                    </div>

                    <div class="flex flex-wrap gap-x-2">
                        <dt class="text-info"><span aria-hidden="true">"</span>reply<span aria-hidden="true">":</span></dt>
                        <dd class="text-outputText"><span aria-hidden="true">"</span>within 24h<span aria-hidden="true">"</span></dd>
                    </div>
                </dl>

                <p aria-hidden="true">}</p>
            </div>

            <p class="mt-8 border-t pt-4 text-sm">
                $ echo "Prefer email? Write to me directly."
            </p>
        </section>

        {{-- right: form --}}
        <div class="flex flex-col overflow-hidden rounded-lg border">
            <div class="flex items-center gap-2 border-b px-4 py-3 text-sm">
                <span class="size-2.5 rounded-full bg-error"></span>
                <span class="size-2.5 rounded-full bg-warning"></span>
                <span class="size-2.5 rounded-full bg-primary"></span>
                <span class="ml-1 truncate">{{ $requireOtp ? 'verify-otp.sh' : 'send-message.sh' }}</span>
            </div>

            @if($requireOtp)
                <form wire:submit="confirmOtp" novalidate
                      class="flex flex-1 flex-col gap-6 p-6 sm:p-8">

                    <div>
                        <p class="text-sm leading-7 text-outputText">
                            $ ./verify-otp.sh
                        </p>
                        <p class="mt-2 text-sm leading-7 text-muted">
                            @if($linkingEmail)
                                <span class="text-warning">email not linked.</span> we sent a code to
                                <span class="text-primary">{{ $otpEmail }}</span> — confirm it to link this address.
                                <span>please take moment to confirm your email</span>
                            @else
                                we sent a code to <span class="text-primary">{{ $otpEmail }}</span> to confirm it is you.
                            @endif
                        </p>
                    </div>

                    {{-- otp --}}
                    <div>
                        <label for="otp" class="mb-2 block text-sm font-bold text-primary">$ code</label>
                        <div class="group flex items-center gap-3 rounded border px-4 transition-colors focus-within:border-primary @error('otp') border-error @enderror">
                            <span aria-hidden="true" class="group-focus-within:text-primary @error('otp') text-error @enderror">&gt;</span>
                            <input type="text" id="otp" wire:model="otp" inputmode="numeric" maxlength="6"
                                   class="w-full bg-transparent py-3 text-lg tracking-[0.5em] outline-none placeholder:text-muted/60"
                                   placeholder="______" autocomplete="one-time-code"
                                   aria-invalid="{{ $errors->has('otp') ? 'true' : 'false' }}"
                                   aria-describedby="otp-error">
                        </div>
                        @error('otp') <p id="otp-error" role="alert" class="mt-2 text-sm text-error">error: {{ $message }}</p> @enderror
                    </div>

                    {{-- actions --}}
                    <div class="flex flex-wrap items-center gap-4">
                        <button type="submit" wire:loading.attr="disabled" wire:target="confirmOtp"
                                class="rounded bg-primary px-6 py-3 font-bold text-black transition-opacity hover:opacity-90
                                   focus-visible:outline focus-visible:outline-offset-2 disabled:cursor-wait disabled:opacity-60">
                            <span wire:loading.remove wire:target="confirmOtp">[ verify ]</span>
                            <span wire:loading wire:target="confirmOtp">verifying...</span>
                        </button>
                        <button type="button" wire:click="resend" wire:loading.attr="disabled" wire:target="resend"
                                class="rounded border border-primary px-6 py-3 font-bold text-primary transition-colors hover:bg-primary/10 focus-visible:outline focus-visible:outline-offset-2 disabled:cursor-wait disabled:opacity-60">
                            <span wire:loading.remove wire:target="resend">[ resend ]</span>
                            <span wire:loading wire:target="resend">sending...</span>
                        </button>
                        <button type="button" wire:click="cancelOtp"
                                class="text-sm text-muted underline-offset-4 hover:text-primary hover:underline focus-visible:text-primary">
                            [ change email ]
                        </button>
                    </div>
                </form>
            @else
                <form wire:submit="submit" novalidate
                      x-data="{ count: {{ strlen($message ?? '') }} }"
                      @keydown.ctrl.enter.prevent="$wire.submit()"
                      @keydown.meta.enter.prevent="$wire.submit()"
                      class="flex flex-1 flex-col gap-6 p-6 sm:p-8">

                    {{-- name --}}
                    <div>
                        <label for="name" class="mb-2 block text-sm font-bold text-primary">$ name</label>
                        <div class="group flex items-center gap-3 rounded border px-4 transition-colors focus-within:border-primary @error('name') @enderror">
                            <span aria-hidden="true" class="group-focus-within:text-primary @error('name') @enderror">&gt;</span>
                            <input type="text" id="name" wire:model="name"
                                   class="w-full bg-transparent py-3  outline-none placeholder:text-muted/60"
                                   placeholder="Your name" maxlength="255" autocomplete="name"
                                   aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
                                   aria-describedby="name-error">
                        </div>
                        @error('name') <p id="name-error" role="alert" class="mt-2 text-sm text-error">error: {{ $message }}</p> @enderror
                    </div>

                    {{-- email --}}
                    <div>
                        <label for="email" class="mb-2 block text-sm font-bold text-primary">$ email</label>
                        <div class="group flex items-center gap-3 rounded border px-4 transition-colors focus-within:border-primary @error('email') @enderror">
                            <span aria-hidden="true" class="group-focus-within:text-primary @error('email') @enderror">&gt;</span>
                            <input type="email" id="email" wire:model="email"
                                   class="w-full bg-transparent py-3 outline-none placeholder:text-muted/60"
                                   placeholder="you@example.com" maxlength="255" autocomplete="email"
                                   aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                                   aria-describedby="email-error">
                        </div>
                        @error('email') <p id="email-error" role="alert" class="mt-2 text-sm text-error">error: {{ $message }}</p> @enderror
                    </div>

                    {{-- message (grows to fill the card) --}}
                    <div class="flex flex-1 flex-col">
                        <div class="mb-2 flex items-baseline justify-between">
                            <label for="message" class="text-sm font-bold text-primary">$ message</label>
                            <span class="text-sm" :class="count > 4500 ? 'text-warning' : ''" x-text="count + ' / 5000'"></span>
                        </div>
                        <div class="group flex flex-1 gap-3 rounded border px-4 transition-colors focus-within:border-primary @error('message') @enderror">
                            <span aria-hidden="true" class="pt-3 group-focus-within:text-primary @error('message') @enderror">&gt;</span>
                            <textarea id="message" wire:model="message" rows="8"
                                      x-on:input="count = $el.value.length"
                                      class="min-h-48 w-full flex-1 resize-y bg-transparent py-3 outline-none placeholder:text-muted/60"
                                      placeholder="Write your message..." maxlength="5000"
                                      aria-invalid="{{ $errors->has('message') ? 'true' : 'false' }}"
                                      aria-describedby="message-error"></textarea>
                        </div>
                        @error('message') <p id="message-error" role="alert" class="mt-2 text-sm text-error">error: {{ $message }}</p> @enderror
                    </div>

                    {{-- actions --}}
                    <div class="flex flex-wrap items-center gap-4">
                        <button type="submit" wire:loading.attr="disabled" wire:target="submit"
                                class="rounded bg-primary px-6 py-3 font-bold text-black transition-opacity hover:opacity-90
                                   focus-visible:outline focus-visible:outline-offset-2 disabled:cursor-wait disabled:opacity-60">
                            <span wire:loading.remove wire:target="submit">[ send ]</span>
                            <span wire:loading wire:target="submit">sending...</span>
                        </button>
                        <span class="hidden text-sm sm:inline">
                        or press <kbd class="rounded border px-1.5">Ctrl</kbd> + <kbd class="rounded border px-1.5">Enter</kbd>
                    </span>
                    </div>
                </form>
            @endif
        </div>

    </div>
</div>
