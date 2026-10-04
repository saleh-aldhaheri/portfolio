<?php

namespace App\Livewire\Components;

use Illuminate\View\View;
use Livewire\Component;

class UserTerminal extends Component
{
    public string $input = '';

    public string $command = '';

    public array $history = [];

    public ?int $historyIndex = null;

    public bool $showBanner = true;

    public function mount(): void
    {
        // initial value
        $this->command = 'banner';
    }

    public function render(): View
    {
        return view('livewire.components.user-terminal');
    }

    private function validateCommand(string $input): bool
    {
        return array_key_exists($input, config('commands'));
    }

    public function runCommand(): void
    {
        $input = strtolower(trim($this->input));

        $this->historyIndex = null;

        if ($input === '') {
            return;
        }

        $this->command = $this->validateCommand($input) ? $input : 'error';

        $this->history = array_values(array_diff($this->history, [$input]));
        $this->history[] = $input;
    }

    public function historyUp(): void
    {
        if (empty($this->history)) {
            return;
        }

        $this->historyIndex = $this->historyIndex === null
            ? count($this->history) - 1
            : max(0, $this->historyIndex - 1);

        $this->input = $this->history[$this->historyIndex];
    }

    public function historyDown(): void
    {
        if ($this->historyIndex === null) {
            return;
        }

        if ($this->historyIndex < count($this->history) - 1) {
            $this->historyIndex++;
            $this->input = $this->history[$this->historyIndex];
        } else {
            $this->historyIndex = null;
        }
    }
}
