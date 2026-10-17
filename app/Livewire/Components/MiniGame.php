<?php

namespace App\Livewire\Components;

use Illuminate\View\View;
use Livewire\Component;

class MiniGame extends Component
{
    public function render(): View
    {
        return view('livewire.components.mini-game');
    }

    public function openGame(): void
    {
        $this->dispatch('log-activity', 'open-game');
    }
}
