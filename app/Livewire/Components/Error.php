<?php

namespace App\Livewire\Components;

use App\Services\DataResolverService;
use Illuminate\View\View;
use Livewire\Component;

class Error extends Component
{
    public string $command = '';

    public function render(): View
    {
        return view('livewire.components.error',[
            'data' => DataResolverService::resolve('help'),
        ]);
    }
}
