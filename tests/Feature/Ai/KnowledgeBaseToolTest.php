<?php

use App\Ai\Agents\ChatAgent;
use App\Ai\Tools\KnowledgeBaseTool;
use App\Models\Visitor;
use App\Services\AiChatContextManagerService;
use Laravel\Ai\Tools\Request;

function knowledgeTool(): KnowledgeBaseTool
{
    return new KnowledgeBaseTool;
}

function knowledgeTree(): array
{
    return require resource_path('data/knowledge.php');
}

function agentInstructions(): string
{
    $agent = new ChatAgent(new AiChatContextManagerService);
    $agent->setVisitor(Visitor::factory()->create());

    return $agent->instructions();
}

function knowledgeIndexKeys(): array
{
    $parts = json_decode(agentInstructions(), true, flags: JSON_THROW_ON_ERROR);
    $index = $parts['knowledge_index'];

    preg_match_all('/(?:saleh|system)(?:\.[a-z_]+)+/', $index, $matches);

    return array_values(array_unique($matches[0]));
}

function knowledgeLeafKeys(?array $node = null, string $prefix = ''): array
{
    $node ??= knowledgeTree();
    $keys = [];

    foreach ($node as $key => $value) {
        $path = $prefix === '' ? $key : "{$prefix}.{$key}";
        $keys = array_merge($keys, is_array($value) ? knowledgeLeafKeys($value, $path) : [$path]);
    }

    return $keys;
}

it('returns a knowledge section for its key', function () {
    expect(knowledgeTool()->handle(new Request(['key' => 'saleh.about'])))
        ->toContain('backend-focused');
});

it('returns a nested project section for its key', function () {
    expect(knowledgeTool()->handle(new Request(['key' => 'saleh.projects.rapix'])))
        ->toContain('dependency injection')
        ->toContain('1. Rapix');
});

it('returns the whole subtree when it is small enough to send at once', function () {
    expect(knowledgeTool()->handle(new Request(['key' => 'system'])))
        ->toContain('theme set dracula')
        ->toContain('6-digit verification code');
});

it('tolerates case and stray whitespace in the key', function () {
    expect(knowledgeTool()->handle(new Request(['key' => '  Saleh.Location '])))
        ->toContain('Malaysia');
});

it('lists the available keys when the key is unknown', function (string $key) {
    expect(knowledgeTool()->handle(new Request(['key' => $key])))
        ->toContain('saleh.projects.blog_system - 3. Blog System')
        ->toContain('system.theme - Changing the theme');
})->with(['nope', 'saleh.nope', 'projects']);

it('lists the keys instead of flooding the context with a huge section', function () {
    $result = knowledgeTool()->handle(new Request(['key' => 'saleh']));

    expect($result)
        ->toContain('large section')
        ->toContain('saleh.about - About Saleh Ahmed AlDhaheri')
        ->toContain('saleh.projects.rapix - 1. Rapix')
        ->not->toContain('I am primarily a backend-focused');
});

it('lists the keys when none is given', function () {
    expect(knowledgeTool()->handle(new Request(['key' => ''])))
        ->toContain('No key was given')
        ->toContain('system.form - The contact form');
});

it('indexes every knowledge key exactly once', function () {
    $indexed = knowledgeIndexKeys();
    $leaves = knowledgeLeafKeys();

    sort($indexed);
    sort($leaves);

    expect($indexed)->toBe($leaves);
});

it('resolves every indexed key to real content', function () {
    $unreachable = array_filter(
        knowledgeIndexKeys(),
        fn (string $key) => trim((string) data_get(knowledgeTree(), $key)) === ''
    );

    expect($unreachable)->toBeEmpty();
});

it('keeps the knowledge base out of the agent instructions', function () {
    $instructions = agentInstructions();

    expect($instructions)
        ->toContain('knowledge_index')
        ->not->toContain('Asia Pacific University')
        ->not->toContain('PhpSpreadsheet');
});
