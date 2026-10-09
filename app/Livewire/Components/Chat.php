<?php

namespace App\Livewire\Components;

use App\Actions\SendMessageAction;
use App\Enums\MessageRole;
use App\Models\Message;
use App\Models\Visitor;
use App\Services\VisitorService;
use Illuminate\View\View;
use Livewire\Component;

class Chat extends Component
{
    public ?Visitor $visitor = null;

    public ?string $message = null;

    /**
     * Chat history, oldest first.
     *
     * @var array<int, array{id: int, role: string, message: string}>
     */
    public array $messages = [];

    public bool $hasMoreMessages = true;

    private bool $visitorResolvedOnMount = false;

    public function mount(): void
    {
        $uuid = request()->cookie('visitor');

        $this->visitor = $uuid
            ? Visitor::where('uuid', $uuid)->first()
            : null;

        if ($this->visitor !== null) {
            $this->visitorResolvedOnMount = true;
            $this->refreshMessages();
            $this->refreshHasMoreMessages();
        }
    }

    public function render(): View
    {
        return view('livewire.components.chat');
    }

    public function sendMessage(): void
    {
        $text = trim($this->message ?? '');

        if ($text === '') {
            return;
        }

        $this->visitor = $this->resolveVisitor();

        $sent = (new SendMessageAction)->execute($this->visitor, $text);

        $this->message = null;

        if ($sent !== null) {
            $this->pushMessage([
                'id' => $sent->id,
                'role' => $sent->role instanceof MessageRole ? $sent->role->value : (string) $sent->role,
                'message' => $sent->message,
            ]);
        }

        // Only needed when this page session had no visitor yet: the browser
        // could not subscribe to the Echo channel at mount, so hand it the uuid.
        if (! $this->visitorResolvedOnMount) {
            $this->dispatch('visitor-ready', uuid: $this->visitor->uuid);
            $this->visitorResolvedOnMount = true;
        }

        $this->dispatch('log-activity', 'chat-message-submitted');
        $this->dispatch('chat-scroll', force: true);
    }

    public function receiveMessage(array $payload = []): void
    {
        $message = $payload['message'] ?? null;

        if (! is_array($message) || $this->visitor === null) {
            return;
        }

        if (isset($message['visitor_id']) && (int) $message['visitor_id'] !== $this->visitor->id) {
            return;
        }

        $this->pushMessage([
            'id' => (int) ($message['id'] ?? 0),
            'role' => (string) ($message['role'] ?? MessageRole::ASSISTANT->value),
            'message' => (string) ($message['message'] ?? ''),
        ]);

        $this->dispatch('chat-received', id: (int) ($message['id'] ?? 0));
        $this->dispatch('chat-scroll');
    }

    public function loadEarlierMessages(): void
    {
        $oldestId = $this->messages[0]['id'] ?? null;

        if ($oldestId === null || $this->visitor === null) {
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

    public function receivePendingReply(): void
    {
        if ($this->visitor === null) {
            return;
        }

        $latestMessage = $this->visitor->messages()
            ->latest('id')
            ->first();

        if ($latestMessage !== null && $latestMessage->role === MessageRole::ASSISTANT) {
            $lastVisibleId = $this->messages[count($this->messages) - 1]['id'] ?? 0;

            if ($latestMessage->id > $lastVisibleId) {
                $this->pushMessage($this->formatMessage($latestMessage));
                $this->dispatch('chat-received', id: $latestMessage->id);
                $this->dispatch('chat-scroll');
            }
        }
    }

    private function pushMessage(array $message): void
    {
        if ($message['id'] === 0 || $message['message'] === '') {
            return;
        }

        foreach ($this->messages as $existing) {
            if ($existing['id'] === $message['id']) {
                return;
            }
        }

        $this->messages[] = $message;
    }

    private function refreshMessages(): void
    {
        if ($this->visitor === null) {
            $this->messages = [];

            return;
        }

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

        $this->hasMoreMessages = $this->visitor !== null
            && $oldestId !== null
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

    private function resolveVisitor(): Visitor
    {
        $request = request();

        return app(VisitorService::class)->resolve($request);
    }

    public function getListeners(): array
    {
        if ($this->visitor === null) {
            return [];
        }

        return [
            "echo:visitor.{$this->visitor->uuid},.message.sent" => 'receiveMessage',
        ];
    }
}
