<?php

namespace App\Livewire\Components;

use App\Enums\MessageRole;
use App\Models\Message;
use App\Models\Visitor;
use Illuminate\View\View;
use Livewire\Component;

class ConversationViewer extends Component
{
    public Visitor $visitor;

    /**
     * Conversation history, oldest first.
     *
     * @var array<int, array{id: int, role: string, message: string}>
     */
    public array $messages = [];

    public bool $hasMoreMessages = false;

    public function mount(?Visitor $record = null, ?int $visitorId = null): void
    {
        $this->visitor = $record ?? Visitor::findOrFail($visitorId);

        $this->refreshMessages();
        $this->refreshHasMoreMessages();
    }

    public function render(): View
    {
        return view('livewire.components.conversation-viewer');
    }

    public function loadEarlierMessages(): void
    {
        $oldestId = $this->messages[0]['id'] ?? null;

        if ($oldestId === null) {
            return;
        }

        $earlier = $this->visitor->messages()
            ->where('id', '<', $oldestId)
            ->orderByDesc('id')
            ->limit(30)
            ->get()
            ->sortBy('id')
            ->values()
            ->map(fn (Message $message): array => $this->formatMessage($message))
            ->all();

        if ($earlier !== []) {
            $this->messages = [...$earlier, ...$this->messages];
        }

        $this->refreshHasMoreMessages();
    }

    private function refreshMessages(): void
    {
        $this->messages = $this->visitor->messages()
            ->orderByDesc('id')
            ->limit(50)
            ->get()
            ->sortBy('id')
            ->values()
            ->map(fn (Message $message): array => $this->formatMessage($message))
            ->all();
    }

    private function refreshHasMoreMessages(): void
    {
        $oldestId = $this->messages[0]['id'] ?? null;

        $this->hasMoreMessages = $oldestId !== null
            && $this->visitor->messages()->where('id', '<', $oldestId)->exists();
    }

    /**
     * @return array{id: int, role: string, message: string}
     */
    private function formatMessage(Message $message): array
    {
        return [
            'id' => $message->id,
            'role' => $message->role instanceof MessageRole ? $message->role->value : (string) $message->role,
            'message' => $message->message,
        ];
    }
}
