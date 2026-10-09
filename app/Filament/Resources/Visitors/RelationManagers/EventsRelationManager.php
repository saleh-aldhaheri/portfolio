<?php

namespace App\Filament\Resources\Visitors\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class EventsRelationManager extends RelationManager
{
    protected static string $relationship = 'logs';

    protected static ?string $title = 'Events';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->select('event', DB::raw('MIN(id) as id'), DB::raw('COUNT(*) as total'), DB::raw('MAX(created_at) as last_at'))
                ->groupBy('event'))
            ->defaultSort('total', 'desc')
            ->defaultKeySort(false)
            ->columns([
                TextColumn::make('event')
                    ->label('Event'),
                TextColumn::make('total')
                    ->label('Times')
                    ->badge()
                    ->sortable(),
                TextColumn::make('last_at')
                    ->label('Last event')
                    ->since(),
            ]);
    }
}
