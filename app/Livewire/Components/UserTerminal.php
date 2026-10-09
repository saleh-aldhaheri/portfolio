<?php

namespace App\Livewire\Components;

use App\Services\DataResolverService;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class UserTerminal extends Component
{
    public string $input = '';

    public string $command = '';

    public array $history = [];

    public ?int $historyIndex = null;

    public bool $showBanner = true;

    public ?string $themeOutput = null;

    public function mount(): void
    {
        // initial value
        $this->command = 'banner';
    }

    public function render(): View
    {
        return view('livewire.components.user-terminal');
    }

    private function validateCommand(string $name): bool
    {
        return array_key_exists($name, config('commands'));
    }

    public function runCommand(): void
    {
        $input = strtolower(trim($this->input));

        $this->historyIndex = null;

        if ($input === '') {
            return;
        }

        $parts = explode(' ', $input);
        $name = $parts[0];
        $args = array_slice($parts, 1);

        $this->command = $name === 'theme'
            ? $this->handleTheme($args)
            : ($this->validateCommand($name) ? $name : 'error');

        $this->history = array_values(array_diff($this->history, [$input]));
        $this->history[] = $input;
        $this->dispatch('log-activity', "command-executed: {$input}");
        $this->input = '';
    }

    private function handleTheme(array $args): string
    {
        $sub = $args[0] ?? null;

        if ($sub === 'list') {
            $this->themeOutput = null;

            return 'theme';
        }

        if ($sub === 'set' && isset($args[1])) {
            $theme = $args[1];

            if (in_array($theme, DataResolverService::resolve('themes'), true)) {
                $this->js("window.setTheme('{$theme}')");
                $this->themeOutput = "Theme set to {$theme}";
            } else {
                $this->themeOutput = "Unknown theme: {$theme}";
            }

            return 'theme';
        }

        $this->themeOutput = 'Usage: theme set <name> | theme list';

        return 'theme';
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

    #[On('fill-input')]
    public function setInput(string $input): void
    {
        $this->input = $input;
        $this->runCommand();
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
