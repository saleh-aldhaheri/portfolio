<?php

namespace App\Livewire\Components;

use Illuminate\View\View;
use Livewire\Component;

class Contact extends Component
{
    public string $name = '';

    public string $email = '';

    public string $message = '';

    public bool $sent = false;

    public function submit(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $this->sent = true;
        $this->reset('name', 'email', 'message');
    }

    public function render(): View
    {
        return view('livewire.components.contact');
    }
}
