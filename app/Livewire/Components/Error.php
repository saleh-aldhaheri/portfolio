<?php

namespace App\Livewire\Components;

use Livewire\Component;

class Error extends Component
{
    public string $command;

    public function render()
    {
        return view('livewire.components.error');
    }
}
