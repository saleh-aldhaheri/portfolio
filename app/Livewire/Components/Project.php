<?php

namespace App\Livewire\Components;

use App\Services\DataResolverService;
use Illuminate\View\View;
use Livewire\Component;

class Project extends Component
{
    public string $category = 'all';

    public function render(): View
    {
        return view('livewire.components.project', [
            'data' => DataResolverService::resolve('projects'),
        ]);
    }

    public function setCategory(string $category): void
    {
        $this->category = $category;
    }
}
