<?php

namespace App\Filament\Widgets;

use App\Services\AnalyticsService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class CountriesChartWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 1;

    protected ?string $pollingInterval = '300s';

    protected ?string $heading = 'Visitors by country';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $data = AnalyticsService::countries($this->pageFilters['period'] ?? 'all');

        if (blank($data)) {
            return [];
        }

        return [
            'labels' => array_column($data, 'name'),
            'datasets' => [
                [
                    'label' => 'Visitors',
                    'data' => array_column($data, 'total'),
                ],
            ],
        ];
    }
}
