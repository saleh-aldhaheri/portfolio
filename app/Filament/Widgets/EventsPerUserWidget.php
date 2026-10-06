<?php

namespace App\Filament\Widgets;

use App\Services\AnalyticsService;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget;

class EventsPerUserWidget extends TableWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Events per visitor')
            ->poll('300s')
            ->columns([
                TextColumn::make('uuid')
                    ->label('Visitor'),
                TextColumn::make('name')
                    ->placeholder('-'),
                TextColumn::make('email')
                    ->placeholder('-'),
                TextColumn::make('events')
                    ->label('Events')
                    ->numeric(),
            ])
            ->records(fn (): array => AnalyticsService::eventsPerUser($this->pageFilters['period'] ?? 'all'));
    }
}
