<?php

test('side button renders on the page without obstructing content', function () {
    $this->withoutVite()->get('/')
        ->assertOk()
        ->assertSee('aria-label="Open mini game"', false)
        ->assertSee('fixed top-4 left-4 z-50', false);
});

test('opening the button reveals a terminal-styled popup with the mini game', function () {
    $this->withoutVite()->get('/')
        ->assertOk()
        ->assertSee('aria-label="Close mini game"', false)
        ->assertSee('./mini-game', false)
        ->assertSee('[ play ]', false)
        ->assertSee('w-72 sm:w-80', false);
});

test('popup dismisses via click-outside, close button and Escape', function () {
    $this->withoutVite()->get('/')
        ->assertOk()
        ->assertSee('x-on:click.outside="open = false"', false)
        ->assertSee('x-on:keydown.escape.window="open = false"', false)
        ->assertSee('x-on:click="open = false"', false);
});

test('opening the button dispatches the open-game analytics event', function () {
    $this->withoutVite()->get('/')
        ->assertOk()
        ->assertSee("Livewire.dispatch('log-activity', 'open-game')", false);
});

test('mini game interaction loop is wired with start, tap and timer', function () {
    $this->withoutVite()->get('/')
        ->assertOk()
        ->assertSee('start()', false)
        ->assertSee('tap(cell)', false)
        ->assertSee('setInterval', false)
        ->assertSee('timeLeft: 15', false);
});

test('layout still renders header, footer and toaster hub alongside the button', function () {
    $this->withoutVite()->get('/')
        ->assertOk()
        ->assertSee('saleh-ahmed.dev', false)
        ->assertSee('whoever is patient', false)
        ->assertSee('id="toaster"', false)
        ->assertSee('aria-label="Open mini game"', false);
});
