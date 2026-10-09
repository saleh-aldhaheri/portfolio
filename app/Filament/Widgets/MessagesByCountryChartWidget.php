<?php

namespace App\Filament\Widgets;

use App\Services\AnalyticsService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class MessagesByCountryChartWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 1;

    protected ?string $pollingInterval = '300s';

    protected ?string $heading = 'Messages by country';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $data = AnalyticsService::messagesByCountry($this->pageFilters['period'] ?? 'all');

        if (blank($data)) {
            return [];
        }

        return [
            'labels' => array_column($data, 'name'),
            'datasets' => [
                [
                    'label' => 'Messages',
                    'data' => array_column($data, 'total'),
                ],
            ],
        ];
    }
}
