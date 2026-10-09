<?php

use App\Ai\Agents\ChatAgent;
use App\Models\Visitor;

beforeEach(function () {
    $this->agent = app(ChatAgent::class);
    $this->agent->setVisitor(Visitor::factory()->create());

});

describe('ChatAgent instructions', function () {
    it('tells the assistant to reintroduce itself instead of refusing', function () {
        $instructions = $this->agent->instructions();

        expect($instructions)
            ->toContain("you are Saleh's assistant")
            ->toContain('how to use his portfolio')
            ->toContain('they are already on it');
    });

    it('no longer mandates the refusal phrase', function () {
        expect($this->agent->instructions())
            ->not->toContain('politely answer "this is not my job."')
            ->not->toContain('or the ./Contact section of the page');
    });
});

describe('knowledge navigation', function () {
    it('describes going to a section as typing into the input field at the top', function () {
        $terminal = knowledgeTree()['system']['terminal'];

        expect($terminal)
            ->toContain('input field at the top')
            ->toContain('types help into the input field');
    });

    it('does not claim a ./Contact page section exists', function () {
        expect(knowledgeTree()['system']['form'])
            ->not->toContain('./Contact section');
    });
});
