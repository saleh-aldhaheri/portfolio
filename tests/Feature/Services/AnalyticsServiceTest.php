<?php

use App\Models\AnalyticsLog;
use App\Models\ContactMessage;
use App\Models\Message;
use App\Models\Visitor;
use App\Services\AnalyticsService;

beforeEach(function () {
    Cache::flush();

    $this->log = function (Visitor $visitor, string $event, ?DateTimeInterface $createdAt = null): AnalyticsLog {
        $log = AnalyticsLog::create(['visitor_id' => $visitor->id, 'event' => $event]);

        if ($createdAt) {
            $log->created_at = $createdAt;
            $log->save();
        }

        return $log;
    };
});

describe('overview', function () {
    it('counts visitors, forms, events and messages inside the period', function () {
        $visitor = Visitor::factory()->create();
        ContactMessage::factory()->create(['visitor_id' => $visitor->id]);
        ($this->log)($visitor, 'open-github');
        Message::create(['visitor_id' => $visitor->id, 'role' => 'visitor', 'message' => 'hello']);

        expect(AnalyticsService::overview())->toBe([
            'visitors' => 1,
            'contact_messages' => 1,
            'events' => 1,
            'messages' => 1,
        ]);
    });

    it('ignores records created before the period', function () {
        $visitor = Visitor::factory()->create();

        $old = Visitor::factory()->create();
        $old->created_at = now()->subYear();
        $old->save();

        ($this->log)($visitor, 'open-github');
        ($this->log)($visitor, 'open-github', now()->subYear());

        $overview = AnalyticsService::overview('today');

        expect($overview['visitors'])->toBe(1)
            ->and($overview['events'])->toBe(1);
    });

    it('falls back to the default period for an unknown period', function () {
        Visitor::factory()->create();

        expect(AnalyticsService::overview('not-a-period'))
            ->toBe(AnalyticsService::overview());
    });

    it('serves repeated calls from the cache', function () {
        $visitor = Visitor::factory()->create();
        ($this->log)($visitor, 'open-github');

        $first = AnalyticsService::overview();
        ($this->log)($visitor, 'open-github');
        $second = AnalyticsService::overview();

        expect($second)->toBe($first)
            ->and($second['events'])->toBe(1);
    });
});

describe('grouped metrics', function () {
    it('totals visitors per country ordered by total', function () {
        Visitor::factory()->create(['country' => 'Oman']);
        Visitor::factory()->create(['country' => 'Oman']);
        Visitor::factory()->create(['country' => 'Yemen']);
        Visitor::factory()->create(['country' => null]);

        expect(AnalyticsService::countries())->toBe([
            ['name' => 'Oman', 'total' => 2],
            ['name' => 'Yemen', 'total' => 1],
        ]);
    });

    it('totals visitors per city ordered by total', function () {
        Visitor::factory()->create(['city' => 'Muscat']);
        Visitor::factory()->create(['city' => 'Muscat']);
        Visitor::factory()->create(['city' => 'Sanaa']);

        expect(AnalyticsService::cities())->toBe([
            ['name' => 'Muscat', 'total' => 2],
            ['name' => 'Sanaa', 'total' => 1],
        ]);
    });

    it('totals messages per visitor country', function () {
        $oman = Visitor::factory()->create(['country' => 'Oman']);
        $yemen = Visitor::factory()->create(['country' => 'Yemen']);

        Message::create(['visitor_id' => $oman->id, 'role' => 'visitor', 'message' => 'one']);
        Message::create(['visitor_id' => $oman->id, 'role' => 'assistant', 'message' => 'two']);
        Message::create(['visitor_id' => $yemen->id, 'role' => 'visitor', 'message' => 'three']);

        expect(AnalyticsService::messagesByCountry())->toBe([
            ['name' => 'Oman', 'total' => 2],
            ['name' => 'Yemen', 'total' => 1],
        ]);
    });

    it('totals each tracked event', function () {
        $visitor = Visitor::factory()->create();
        ($this->log)($visitor, 'open-github');
        ($this->log)($visitor, 'open-github');
        ($this->log)($visitor, 'open-resume');

        expect(AnalyticsService::topEvents())->toBe([
            ['event' => 'open-github', 'total' => 2],
            ['event' => 'open-resume', 'total' => 1],
        ]);
    });

    it('totals events per visitor', function () {
        $visitor = Visitor::factory()->create();
        ($this->log)($visitor, 'open-github');
        ($this->log)($visitor, 'open-resume');

        $rows = AnalyticsService::eventsPerUser();

        expect($rows)->toHaveCount(1)
            ->and($rows[0]['uuid'])->toBe($visitor->uuid)
            ->and($rows[0]['name'])->toBe($visitor->name)
            ->and($rows[0]['events'])->toBe(2);
    });

    it('totals contact forms per visitor', function () {
        $visitor = Visitor::factory()->create();
        ContactMessage::factory()->create(['visitor_id' => $visitor->id]);
        ContactMessage::factory()->create(['visitor_id' => $visitor->id]);

        $rows = AnalyticsService::formsPerUser();

        expect($rows)->toHaveCount(1)
            ->and($rows[0]['uuid'])->toBe($visitor->uuid)
            ->and($rows[0]['messages'])->toBe(2);
    });
});

describe('time spent', function () {
    it('derives the duration from the span of a visitor events', function () {
        $visitor = Visitor::factory()->create();
        ($this->log)($visitor, 'open-github', now()->subMinutes(2)->subSeconds(30));
        ($this->log)($visitor, 'open-resume');

        $rows = AnalyticsService::timeSpent();

        expect($rows)->toHaveCount(1)
            ->and($rows[0]['uuid'])->toBe($visitor->uuid)
            ->and($rows[0]['duration_seconds'])->toBe(150)
            ->and($rows[0]['duration'])->toBe('2m 30s');
    });
});

describe('engagement', function () {
    it('summarises the dashboard counters', function () {
        $visitor = Visitor::factory()->create();
        ContactMessage::factory()->create(['visitor_id' => $visitor->id]);
        ($this->log)($visitor, 'open-resume');
        ($this->log)($visitor, 'open-github');
        ($this->log)($visitor, 'open-linkedin');
        ($this->log)($visitor, 'open-linked');

        expect(AnalyticsService::engagement())->toBe([
            ['label' => 'Resume opened', 'value' => 1],
            ['label' => 'GitHub clicked', 'value' => 1],
            ['label' => 'LinkedIn clicked', 'value' => 2],
            ['label' => 'Email clicked', 'value' => 0],
            ['label' => 'Forms submitted', 'value' => 1],
        ]);
    });
});

describe('filter options', function () {
    it('exposes the relative periods', function () {
        expect(AnalyticsService::filterOptions())
            ->toBe(AnalyticsService::PERIODS)
            ->and(array_keys(AnalyticsService::filterOptions()))
            ->toBe(['today', 'week', 'month', 'year']);
    });
});
