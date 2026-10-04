<?php

namespace App\Livewire\Components;

use App\Services\DataResolverService;
use Illuminate\View\View;
use Livewire\Component;

class ThemeList extends Component
{
    public ?string $output = null;

    public function render(): View
    {
        return view('livewire.components.theme-list', [
            'themes' => DataResolverService::resolve('themes'),
        ]);
    }
}
