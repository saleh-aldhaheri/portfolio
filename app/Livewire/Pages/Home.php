<?php

namespace App\Livewire\Pages;

use App\Services\VisitorService;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class Home extends Component
{
    public function render(): View
    {
        return view('livewire.pages.home');
    }

    #[On('log-activity')]
    public function logActivity(string $event): void
    {
        $visitor = app(VisitorService::class)->resolve(request());

        $visitor->logs()->create([
            'event' => $event,
        ]);
    }
}
