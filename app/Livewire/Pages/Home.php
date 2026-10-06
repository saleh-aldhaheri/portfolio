<?php

namespace App\Livewire\Pages;

use App\Models\Visitor;
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
        $uuid = request()->cookie('visitor');

        if (! $uuid) {
            return;
        }

        $visitor = Visitor::where('uuid', $uuid)->first();

        $visitor?->logs()->create([
            'event' => $event,
        ]);
    }
}
