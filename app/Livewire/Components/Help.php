<?php

namespace App\Livewire\Components;

use App\Services\DataResolverService;
use Illuminate\View\View;
use Livewire\Component;

class Help extends Component
{
    public function render(): View
    {
        return view('livewire.components.help', [
            'data' => DataResolverService::resolve('help'),
        ]);
    }
}
