<?php

use App\Livewire\Components\MiniGame;
use Livewire\Livewire;

test('mini game renders its play button and board', function () {
    Livewire::test(MiniGame::class)
        ->assertSee('[ play ]')
        ->assertSee('./mini-game');
});

test('opening the mini game dispatches the open-game activity event', function () {
    Livewire::test(MiniGame::class)
        ->call('openGame')
        ->assertDispatched('log-activity', 'open-game');
});
