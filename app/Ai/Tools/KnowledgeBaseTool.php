<?php

namespace App\Ai\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Illuminate\Support\Facades\Cache;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class KnowledgeBaseTool implements Tool
{
    /**
     * Above this size a section is returned as a list of its keys instead of its text,
     * so one call can never flood the context with the whole knowledge base.
     */
    private const int MAX_CONTENT_BYTES = 7000;

    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return "Read Saleh's knowledge base one section at a time. It holds everything you may say about Saleh (who he is, his personality, experience, projects, direction, location) and about this website (terminal commands, themes, the chat, the contact form, how to guide visitors). Pass a dot-notation key to fetch only that section, for example \"saleh.about\", \"saleh.projects.rapix\", \"system.terminal\". Always fetch before you answer - never fill gaps from general knowledge.";
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $key = ltrim(strtolower(trim($request->string('key')->toString())), '.');
        $knowledge = $this->knowledge();

        if ($key === '') {
            return $this->keyListing($knowledge, '', 'No key was given. Available keys:');
        }

        $value = data_get($knowledge, $key);

        if ($value === null) {
            return $this->keyListing($knowledge, '', "Nothing is stored under \"{$key}\". Available keys:");
        }

        if (is_string($value)) {
            return trim($value);
        }

        $content = $this->flatten($value);

        if (strlen($content) <= self::MAX_CONTENT_BYTES) {
            return $content;
        }

        return $this->keyListing(
            $value,
            $key,
            "\"{$key}\" is a large section (".strlen($content).' characters). Fetch one of its specific keys instead:'
        );
    }

    /**
     * Get the tool's schema definition.
     *
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'key' => $schema->string()
                ->required()
                ->description('Dot-notation path of the section to read, e.g. "saleh.personality" or "system.theme".'),
        ];
    }

    /**
     * Load the knowledge tree, cached until the file changes.
     *
     * @return array<string, mixed>
     */
    private function knowledge(): array
    {
        $path = resource_path('data/knowledge.php');

        return Cache::remember(
            'knowledge:'.filemtime($path),
            now()->addHour(),
            fn (): array => require $path,
        );
    }

    /**
     * Flatten a section (or subtree) into readable text, one entry per paragraph.
     *
     * @param  array<string, mixed>  $node
     */
    private function flatten(array $node): string
    {
        $parts = [];

        foreach ($node as $value) {
            $parts[] = is_array($value) ? $this->flatten($value) : trim($value);
        }

        return implode("\n\n", $parts);
    }

    /**
     * List every readable key under the given node, each with its first line as a hint.
     *
     * @param  array<string, mixed>  $node
     */
    private function keyListing(array $node, string $prefix, string $intro): string
    {
        $lines = [$intro];

        foreach ($this->leaves($node, $prefix) as $path => $hint) {
            $lines[] = "{$path} - {$hint}";
        }

        return implode("\n", $lines);
    }

    /**
     * @param  array<string, mixed>  $node
     * @return array<string, string>
     */
    private function leaves(array $node, string $prefix): array
    {
        $leaves = [];

        foreach ($node as $key => $value) {
            $path = $prefix === '' ? (string) $key : "{$prefix}.{$key}";

            if (is_array($value)) {
                $leaves += $this->leaves($value, $path);
            } else {
                $firstLine = explode("\n", trim($value))[0];
                $leaves[$path] = strlen($firstLine) > 60 ? substr($firstLine, 0, 57).'...' : $firstLine;
            }
        }

        return $leaves;
    }
}
