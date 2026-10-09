<?php

namespace App\Filament\Widgets;

use App\Services\AnalyticsService;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AnalyticsOverviewWidget extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 0;

    protected ?string $pollingInterval = '300s';

    protected function getStats(): array
    {
        $overview = AnalyticsService::overview($this->pageFilters['period'] ?? 'month');

        return [
            Stat::make('Visitors', $overview['visitors'])
                ->description('Unique visitors')
                ->icon(Heroicon::OutlinedUsers),
            Stat::make('Messages', $overview['messages'])
                ->description('AI chat messages')
                ->icon(Heroicon::OutlinedChatBubbleLeftRight),
            Stat::make('Contact messages', $overview['contact_messages'])
                ->description('Forms submitted')
                ->icon(Heroicon::OutlinedEnvelope),
            Stat::make('Events', $overview['events'])
                ->description('Tracked actions')
                ->icon(Heroicon::OutlinedBolt),
        ];
    }
}
