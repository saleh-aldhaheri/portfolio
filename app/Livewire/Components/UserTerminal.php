<?php

namespace App\Livewire\Components;

use Illuminate\View\View;
use Livewire\Component;

class UserTerminal extends Component
{
    public ?string $command = null;

    public array $history = [];

    public ?int $historyIndex = null;

    public bool $showBanner = true;

    public function render(): View
    {
        $component = $this->resolveDisplayComponent();

        return view('livewire.components.user-terminal', compact('component'));
    }

    public function runCommand(): void
    {
        $command = strtolower(trim($this->command ?? ''));
        $this->command = '';
        $this->historyIndex = null;

        if ($command === '') {
            return;
        }

        if ($command === 'banner') {
            $this->showBanner = true;

            return;
        }

        $this->showBanner = false;
        $this->history = array_values(array_diff($this->history, [$command]));
        $this->history[] = $command;
    }

    public function historyUp(): void
    {
        if (empty($this->history)) {
            return;
        }

        $this->historyIndex = $this->historyIndex === null
            ? count($this->history) - 1
            : max(0, $this->historyIndex - 1);

        $this->command = $this->history[$this->historyIndex];
    }

    public function historyDown(): void
    {
        if ($this->historyIndex === null) {
            return;
        }

        if ($this->historyIndex < count($this->history) - 1) {
            $this->historyIndex++;
            $this->command = $this->history[$this->historyIndex];
        } else {
            $this->historyIndex = null;
            $this->command = '';
        }
    }

    private function resolveDisplayComponent(): array
    {
        $history = $this->history;
        $command = ($this->showBanner || empty($history)) ? 'banner' : end($history);

        $resolved = resolveComponents($command);

        if (empty($resolved)) {
            $resolved = resolveComponents('error', ['command' => $command]);
        }

        $resolved['command'] = $command;
        $resolved['key'] = 'component-'.$command;

        return $resolved;
    }
}
