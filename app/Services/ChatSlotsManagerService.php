<?php

namespace App\Services;

use App\Models\Visitor;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class ChatSlotsManagerService
{
    public function addSlot(Visitor $visitor): bool
    {
        $activeSlots = $this->refreshSlotList();

        if (isset($activeSlots[$visitor->id])) {
            $activeSlots[$visitor->id]['expired_at'] = Carbon::parse($activeSlots[$visitor->id]['expired_at'])
                ->addMinutes((int) config('services.ai_chat_slot.extended_tt'))
                ->toIso8601String();

            Cache::put($this->key(), $activeSlots, now()->addMinutes(config('services.ai_chat_slot.slot_list_ttl')));

            return true;
        }

        if (count($activeSlots) >= (int) config('services.ai_chat_slot.connection_limit')) {
            return false;
        }

        $activeSlots[$visitor->id] = [
            'expired_at' => now()->addMinutes((int) config('services.ai_chat_slot.ttl'))->toIso8601String(),
        ];

        Cache::put($this->key(), $activeSlots, now()->addMinutes((int) config('services.ai_chat_slot.slot_list_ttl')));

        return true;
    }

    public function removeSlot(Visitor $visitor): void
    {
        $chatStack = Cache::get($this->key(), []);

        if (isset($chatStack[$visitor->id])) {
            unset($chatStack[$visitor->id]);
            Cache::put($this->key(), $chatStack, now()->addMinutes((int) config('services.ai_chat_slot.slot_list_ttl')));
        }
    }

    public function refreshSlotList(): array
    {
        $chatStack = Cache::get($this->key(), []);

        $activeSlots = array_filter($chatStack, function ($chat) {
            return isset($chat['expired_at']) && Carbon::parse($chat['expired_at'])->isFuture();
        });

        Cache::put($this->key(), $activeSlots, now()->addMinutes((int) config('services.ai_chat_slot.slot_list_ttl')));

        return $activeSlots;
    }

    private function key(): string
    {
        return 'chat_slots_manager';
    }
}
