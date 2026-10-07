<?php

namespace App\Filament\Widgets;

use App\Services\AnalyticsService;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget;

class FormsPerUserWidget extends TableWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 7;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Forms submitted per visitor')
            ->poll('300s')
            ->columns([
                TextColumn::make('uuid')
                    ->label('Visitor'),
                TextColumn::make('name')
                    ->placeholder('-'),
                TextColumn::make('email')
                    ->placeholder('-'),
                TextColumn::make('messages')
                    ->label('Messages')
                    ->numeric(),
            ])
            ->records(fn (): array => AnalyticsService::formsPerUser($this->pageFilters['period'] ?? 'all'));
    }
}
