<?php

namespace App\Livewire\Components;

use Livewire\Component;

class Help extends Component
{
    public function render()
    {
        return view('livewire.components.help',[
            'groups' => config('help'),
        ]);
    }
}
