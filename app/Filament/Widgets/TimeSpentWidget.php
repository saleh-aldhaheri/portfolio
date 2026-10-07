<?php

namespace App\Filament\Widgets;

use App\Services\AnalyticsService;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget;

class TimeSpentWidget extends TableWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Time spent per visitor')
            ->poll('300s')
            ->columns([
                TextColumn::make('uuid')
                    ->label('Visitor'),
                TextColumn::make('first_activity')
                    ->label('First activity'),
                TextColumn::make('last_activity')
                    ->label('Last activity'),
                TextColumn::make('duration')
                    ->label('Time spent'),
            ])
            ->records(fn (): array => AnalyticsService::timeSpent($this->pageFilters['period'] ?? 'all'));
    }
}
