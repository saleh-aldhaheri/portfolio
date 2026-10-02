<?php

namespace App\Livewire\Components;

use Illuminate\View\View;
use Livewire\Component;

class UserTerminal extends Component
{
    public string $command = 'banner';

    public array $components = [];

    public array $history = [];

    public int $componentId = 0;

    public ?int $historyIndex = null;

    public function mount(): void
    {
        $this->setComponent();
    }

    public function render(): View
    {
        return view('livewire.components.user-terminal');
    }

    public function setComponent(): void
    {
        $command = $this->command;
        $this->command = '';
        $this->historyIndex = null;

        if ($command === '') {
            return;
        }

        if ($command === 'clear') {
            $this->components = [];
            $this->history = array_values(array_diff($this->history, [$command]));
            $this->history[] = $command;

            return;
        }

        $resolved = resolveComponents($command);

        if (empty($resolved)) {
            $resolved = resolveComponents('error', ['command' => $command]);
        }

        if ($command !== 'banner') {
            $this->history = array_values(array_diff($this->history, [$command]));
            $this->history[] = $command;
        }

        $resolved['command'] = $command;
        $resolved['key'] = 'component-'.++$this->componentId;

        if (! empty($this->components) && $this->components[count($this->components) - 1]['command'] === 'banner') {
            $this->components[count($this->components) - 1] = $resolved;
        } else {
            array_unshift($this->components, $resolved);
        }
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
}
