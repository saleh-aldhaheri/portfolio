<?php

use App\Livewire\Components\MiniGame;
use Livewire\Livewire;

test('mini game renders its play button and board', function () {
    Livewire::test(MiniGame::class)
        ->assertSee('[ play ]')
        ->assertSee('./mini-game');
});

test('mini game renders the terminal-styled game board', function () {
    Livewire::test(MiniGame::class)
        ->assertSee('grid grid-cols-3 gap-2', false)
        ->assertSee('font-mono', false)
        ->assertSee('Tap the', false)
        ->assertSee("timeLeft + 's'", false);
});

test('mini game respects reduced motion for the target pulse animation', function () {
    Livewire::test(MiniGame::class)
        ->assertSee('motion-reduce:animate-none', false);
});

test('opening the mini game dispatches the open-game activity event', function () {
    Livewire::test(MiniGame::class)
        ->call('openGame')
        ->assertDispatched('log-activity', 'open-game');
});
