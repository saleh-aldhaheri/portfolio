<?php

namespace App\Livewire\Components;

use Illuminate\View\View;
use Livewire\Component;

class Error extends Component
{
    public string $command = '';

    public function render(): View
    {
        return view('livewire.components.error');
    }
}
