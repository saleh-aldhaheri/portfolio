<?php

use App\Services\DataResolverService;

describe('resolve', function () {
    it('returns the data set for a known key', function () {
        $help = DataResolverService::resolve('help');

        expect($help)->toBeArray()
            ->and($help)->not->toBe([])
            ->and(collect($help)->pluck('command')->all())->toContain('about');
    });

    it('returns an empty array for an unknown key', function () {
        expect(DataResolverService::resolve('does-not-exist'))->toBe([]);
    });
});
