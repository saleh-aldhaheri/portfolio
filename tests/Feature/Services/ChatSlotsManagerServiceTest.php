<?php

use App\Models\Visitor;
use App\Services\ChatSlotsManagerService;
use Carbon\Carbon;

beforeEach(function () {
    $this->visitor = Visitor::factory()->create();
    $this->chatSlotsManagerService = new ChatSlotsManagerService;

    Config::set('services.ai_chat_slot.ttl', 30);
    Config::set('services.ai_chat_slot.slot_list_ttl', 60);
    Config::set('services.ai_chat_slot.extended_tt', 5);
    Config::set('services.ai_chat_slot.connection_limit', 10);
});

describe('add slot', function () {
    it('should add a new slot for a visitor and return true', function () {
        $result = $this->chatSlotsManagerService->addSlot($this->visitor);

        $slots = Cache::get('chat_slots_manager');

        expect($result)->toBeTrue()
            ->and($slots)->toHaveKey($this->visitor->id)
            ->and($slots[$this->visitor->id]['expired_at'])
            ->toBe(Carbon::now()->addMinutes(30)->toIso8601String());
    });

    it('should extend the expiration of an existing slot and return true', function () {
        $this->chatSlotsManagerService->addSlot($this->visitor);

        $originalExpiredAt = Cache::get('chat_slots_manager')[$this->visitor->id]['expired_at'];

        $this->travel(10)->minutes();

        $result = $this->chatSlotsManagerService->addSlot($this->visitor);

        $extendedExpiredAt = Cache::get('chat_slots_manager')[$this->visitor->id]['expired_at'];

        expect($result)->toBeTrue()
            ->and(count(Cache::get('chat_slots_manager')))->toBe(1)
            ->and(Carbon::parse($extendedExpiredAt)->greaterThan(Carbon::parse($originalExpiredAt)))->toBeTrue()
            ->and($extendedExpiredAt)
            ->toBe(Carbon::parse($originalExpiredAt)->addMinutes(5)->toIso8601String());
    });

    it('should return false when the connection limit is reached', function () {
        Config::set('services.ai_chat_slot.connection_limit', 2);

        $this->chatSlotsManagerService->addSlot($this->visitor);
        $this->chatSlotsManagerService->addSlot(Visitor::factory()->create());

        $visitor3 = Visitor::factory()->create();

        $result = $this->chatSlotsManagerService->addSlot($visitor3);

        expect($result)->toBeFalse();
    });

    it('should prune expired slots before adding a new visitor', function () {
        $this->chatSlotsManagerService->addSlot($this->visitor);

        $this->travel(31)->minutes();

        $newVisitor = Visitor::factory()->create();

        $result = $this->chatSlotsManagerService->addSlot($newVisitor);

        $slots = Cache::get('chat_slots_manager');

        expect($result)->toBeTrue()
            ->and($slots)->not()->toHaveKey($this->visitor->id)
            ->and($slots)->toHaveKey($newVisitor->id)
            ->and(count($slots))->toBe(1);
    });
});

describe('remove slot', function () {
    it('should remove an existing slot for a visitor', function () {
        $this->chatSlotsManagerService->addSlot($this->visitor);
        $this->chatSlotsManagerService->addSlot(Visitor::factory()->create());

        $this->chatSlotsManagerService->removeSlot($this->visitor);

        $slots = Cache::get('chat_slots_manager');

        expect($slots)->not()->toHaveKey($this->visitor->id)
            ->and(count($slots))->toBe(1);
    });

    it('should do nothing when the visitor has no slot', function () {
        $this->chatSlotsManagerService->removeSlot($this->visitor);

        expect(Cache::get('chat_slots_manager'))->not()->toHaveKey($this->visitor->id);
    });
});

describe('refresh slot list', function () {
    it('should keep only slots with a future expiration', function () {
        Cache::put('chat_slots_manager', [
            $this->visitor->id => [
                'expired_at' => Carbon::now()->addMinutes(30)->toIso8601String(),
            ],
            '99999' => [
                'expired_at' => Carbon::now()->subMinutes(5)->toIso8601String(),
            ],
        ]);

        $activeSlots = $this->chatSlotsManagerService->refreshSlotList();

        expect($activeSlots)->toHaveKey($this->visitor->id)
            ->and($activeSlots)->not()->toHaveKey('99999')
            ->and(count($activeSlots))->toBe(1);
    });

    it('should return an empty array when no slots exist in cache', function () {
        expect($this->chatSlotsManagerService->refreshSlotList())->toBe([]);
    });
});
