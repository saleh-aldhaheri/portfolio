<div
    class="overflow-hidden rounded-xl border border-gray-300 shadow-sm dark:border-white/10"
    x-data="{
        loadingEarlier: false,
        scrollCheck() {
            if (this.loadingEarlier || this.$wire.hasMoreMessages === false) return;
            const el = this.$refs.messages;
            if (! el || el.scrollTop > 40) return;
            this.loadEarlier();
        },
        async loadEarlier() {
            this.loadingEarlier = true;
            const el = this.$refs.messages;
            const before = el.scrollHeight;
            await this.$wire.loadEarlierMessages();
            el.scrollTop += el.scrollHeight - before;
            this.loadingEarlier = false;
        }
    }"
>
    <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;padding:6px 12px;border-bottom:1px solid rgba(100,116,139,0.25);font-size:12px;font-weight:500;color:#64748b;">
        <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $visitor->name ?? 'Visitor' }} · {{ $visitor->email ?? 'no email' }}</span>
        <span style="display:flex;align-items:center;gap:12px;flex-shrink:0;">
            <span style="display:flex;align-items:center;gap:4px;">
                <span style="display:inline-block;width:8px;height:8px;border-radius:999px;background:#94a3b8;"></span>
                Bot
            </span>
            <span style="display:flex;align-items:center;gap:4px;">
                <span style="display:inline-block;width:8px;height:8px;border-radius:999px;background:#22c55e;"></span>
                Visitor
            </span>
            <span style="color:#94a3b8;">{{ count($messages) }} loaded</span>
        </span>
    </div>

    <div
        x-ref="messages"
        style="height:24rem;overflow-y:auto;overscroll-behavior:contain;padding:12px 16px;"
        x-on:scroll.passive="scrollCheck()"
    >
        <div x-show="loadingEarlier" style="text-align:center;font-size:12px;color:#94a3b8;margin-bottom:8px;">
            <span style="opacity:.7;">loading older messages</span>
        </div>

        @forelse ($messages as $message)
            @if ($message['role'] === 'visitor')
                <div wire:key="message-{{ $message['id'] }}" style="display:flex;justify-content:flex-end;margin-bottom:12px;">
                    <div style="display:flex;flex-direction:column;gap:4px;max-width:80%;border-radius:14px;background:rgba(34,197,94,0.06);border:1px solid rgba(34,197,94,0.25);padding:8px;">
                        <div style="display:flex;align-items:center;justify-content:flex-end;gap:6px;">
                            <span style="font-size:11px;font-weight:500;letter-spacing:.04em;text-transform:uppercase;color:#16a34a;">Visitor</span>
                            <span style="display:flex;align-items:center;justify-content:center;width:20px;height:20px;border-radius:999px;background:rgba(34,197,94,0.15);border:1px solid rgba(34,197,94,0.35);">
                                <x-filament::icon icon="heroicon-o-user" style="width:12px;height:12px;color:#16a34a;" />
                            </span>
                        </div>
                        <p style="word-wrap:break-word;border-radius:10px;background:#22c55e;padding:8px 12px;font-size:13px;line-height:1.5;color:#ffffff;">{{ $message['message'] }}</p>
                    </div>
                </div>
            @else
                <div wire:key="message-{{ $message['id'] }}" style="display:flex;justify-content:flex-start;margin-bottom:12px;">
                    <div style="display:flex;flex-direction:column;gap:4px;max-width:80%;border-radius:14px;background:rgba(100,116,139,0.06);border:1px solid rgba(100,116,139,0.25);padding:8px;">
                        <div style="display:flex;align-items:center;gap:6px;">
                            <span style="display:flex;align-items:center;justify-content:center;width:20px;height:20px;border-radius:999px;background:rgba(100,116,139,0.12);border:1px solid rgba(100,116,139,0.35);">
                                <x-filament::icon icon="heroicon-o-chat-bubble-bottom-center-text" style="width:12px;height:12px;color:#64748b;" />
                            </span>
                            <span style="font-size:11px;font-weight:500;letter-spacing:.04em;text-transform:uppercase;color:#64748b;">Bot</span>
                        </div>
                        <p style="word-wrap:break-word;border-radius:10px;background:#f1f5f9;border:1px solid #e2e8f0;padding:8px 12px;font-size:13px;line-height:1.5;color:#1e293b;">{{ $message['message'] }}</p>
                    </div>
                </div>
            @endif
        @empty
            <p style="font-size:13px;color:#94a3b8;">No messages yet.</p>
        @endforelse
    </div>
</div>