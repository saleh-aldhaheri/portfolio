<?php

namespace App\Livewire\Components;

use App\Services\DataResolverService;
use Illuminate\View\View;
use Livewire\Component;

class About extends Component
{
    public function render(): View
    {
        return view('livewire.components.about', [
            'data' => DataResolverService::resolve('about'),
        ]);
    }
}
