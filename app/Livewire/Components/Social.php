<?php

namespace App\Livewire\Components;

use App\Services\DataResolverService;
use Illuminate\View\View;
use Livewire\Component;

class Social extends Component
{
    public function render(): View
    {
        return view('livewire.components.social',
            [
                'data' => DataResolverService::resolve('social'),
            ]);
    }
}
