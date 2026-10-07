<?php

namespace App\Ai\Agents;

use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;
use Laravel\Ai\Providers\Tools\ProviderTool;
use Stringable;

class ChatAgent implements Agent, Conversational, HasTools
{
    use Promptable;

    /**
     * The latest messages sent by the visitor, used to build the prompt.
     *
     * @var list<string>
     */
    protected array $latestUserMessages = [];

    public function __construct(private Visitor $visitor) {}

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        $knowledge = require resource_path('data/knowledge.php');

        $parts = [
            'identity' => <<<'IDENTITY'
                You are Saleh Ahmed AlDhaheri's AI assistant, embedded in his personal portfolio website. You represent Saleh and act on his behalf.

                Your roles, in order of importance:
                1. Help each visitor understand Saleh better - his personality, skills, experience, projects, education, and career direction. Only ever use the KNOWLEDGE section of this prompt to talk about him. Never invent facts about Saleh.
                2. Guide visitors on how to use this website - the terminal commands, sections, themes, and how to reach Saleh. The KNOWLEDGE section also explains how the application itself works.
                3. Encourage engagement. Whenever a visitor wants to reach Saleh, get in touch with him, or share anything with him, warmly guide them to the Contact section of the site - point them to the "contact" command in the terminal or the ./Contact section, where there is a simple form they can fill out. Let them know that submitting the contact form is the best way for Saleh to reply to them and stay in touch. Never collect, ask for, or store any of the visitor's personal details yourself - no name, email, phone, or company. If a visitor offers such details in the chat, thank them and suggest adding them through the contact form instead.
                IDENTITY,
            'restrictions' => <<<'RESTRICTIONS'
                STRICT RESTRICTIONS:
                1. Scope: answer ONLY within this context. Respond only to questions about Saleh or about this website. For anything else - general knowledge, programming help, trivia, other people, current events, or any unrelated topic - politely answer "this is not my job." and redirect them back to Saleh or the site.
                2. Conciseness: keep every response short, direct, and precise. Never exceed 150 words per response. No filler and no long explanations.
                3. Plain text only: the chat renders as plain text, so NEVER output markdown or any formatting structure - no tables, no headings, no code blocks, no bold/italic markers, no lists with special syntax, no backticks, no emojis. Use simple sentences and plain punctuation only.
                4. No data collection: NEVER ask the visitor for personal information such as their name, email, phone number, or company - no matter the context. NEVER store anything they share. The only place for that information is the Contact form on the site. If a visitor wants to contact Saleh or leave their details, guide them to the "contact" command or the ./Contact section and encourage them to submit the form.
                5. Context priority: always stick to the context of this conversation. The LATEST USER MESSAGE is the single most important input - respond to it first and give it priority over everything else.
                RESTRICTIONS,
            'knowledge' => implode("\n\n", array_merge($knowledge['personal'], $knowledge['application'] ?? [])),
            'conversation_context' => $this->getContext(),
            'latest_user_message' => $this->latestUserMessage(),
        ];

        return json_encode($parts, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Remember the latest messages sent by the visitor so they are highlighted in the prompt.
     *
     * @param  list<string>  $messages
     */
    public function rememberLatestMessages(array $messages): void
    {
        $this->latestUserMessages = array_values($messages);
    }

    private function latestUserMessage(): string
    {
        return $this->latestUserMessages === []
            ? 'No incoming message is pending in this turn. Base your reply on the conversation context.'
            : (string) end($this->latestUserMessages);
    }

    /**
     * Get the list of messages comprising the conversation so far.
     *
     * @return Message[]
     */
    public function messages(): iterable
    {
        return [];
    }

    /**
     * The agent exposes no tools - personal details are collected only via the site's contact form.
     *
     * @return list<Agent|Tool|ProviderTool>
     */
    public function tools(): iterable
    {
        return [];
    }

    private function generateContextKey(): string
    {
        return 'AiAgentContextChat:'.$this->visitor->id;
    }

    public function setContext(array $messages): void
    {
        $key = $this->generateContextKey();

        $context = Cache::get($key);

        if (! $context) {
            $context = [
                'expires_at' => Carbon::now()->addMinutes(30)->timestamp,
                'messages' => [],
            ];
        }

        if (Carbon::now()->timestamp > $context['expires_at']) {
            $context = [
                'expires_at' => Carbon::now()->addMinutes(30)->timestamp,
                'messages' => [],
            ];
        }

        if (! array_is_list($messages)) {
            $messages = [$messages];
        }

        $context['messages'] = array_merge($context['messages'], $messages);

        $remaining = $context['expires_at'] - Carbon::now()->timestamp;

        Cache::put($key, $context, $remaining);
    }

    public function getContextSummary(): array
    {
        $context = Cache::get($this->generateContextKey());

        if (! $context) {
            return [];
        }

        return collect($context['messages'])
            ->pipe(function ($messages) use ($context) {
                return [
                    'expires_at' => $context['expires_at'],
                    'total_words' => $messages->sum(
                        fn ($message) => str_word_count($message['text'])
                    ),
                    'total_messages' => $messages->count(),
                    'messages' => $messages->values()->toArray(),
                ];
            });
    }

    public function getContext(): array
    {
        return Cache::get($this->generateContextKey()) ?? [];
    }
}
