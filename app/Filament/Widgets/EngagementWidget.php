<?php

namespace App\Filament\Widgets;

use App\Services\AnalyticsService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class EngagementWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    protected ?string $pollingInterval = '300s';

    protected ?string $heading = 'Engagement';

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getData(): array
    {
        $data = AnalyticsService::engagement($this->pageFilters['period'] ?? 'all');

        if (blank($data)) {
            return [];
        }

        return [
            'labels' => array_column($data, 'label'),
            'datasets' => [
                [
                    'label' => 'Count',
                    'data' => array_column($data, 'value'),
                ],
            ],
        ];
    }
}
