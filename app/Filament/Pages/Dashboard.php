<?php

namespace App\Filament\Pages;

use App\Services\AnalyticsService;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Schema;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('period')
                    ->label('Period')
                    ->options(AnalyticsService::filterOptions())
                    ->default(AnalyticsService::DEFAULT_PERIOD)
                    ->selectablePlaceholder(false)
                    ->native(false),
            ]);
    }
}
