<?php

namespace App\Filament\Widgets;

use App\Services\AnalyticsService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class TopEventsChartWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 1;

    protected ?string $pollingInterval = '300s';

    protected ?string $heading = 'Most repeated actions';

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getData(): array
    {
        $data = AnalyticsService::topEvents($this->pageFilters['period'] ?? 'all');

        if (blank($data)) {
            return [];
        }

        return [
            'labels' => array_column($data, 'event'),
            'datasets' => [
                [
                    'label' => 'Events',
                    'data' => array_column($data, 'total'),
                ],
            ],
        ];
    }
}
