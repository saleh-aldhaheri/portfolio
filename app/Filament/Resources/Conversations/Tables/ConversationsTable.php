<?php

namespace App\Filament\Resources\Conversations\Tables;

use App\Models\Visitor;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ConversationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('latestMessage.created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('email')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('country')
                    ->placeholder('-'),
                TextColumn::make('city')
                    ->placeholder('-'),
                TextColumn::make('messages_count')
                    ->label('Messages')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('latestMessage.message')
                    ->label('Last message')
                    ->limit(70)
                    ->wrap()
                    ->placeholder('-'),
                TextColumn::make('latestMessage.created_at')
                    ->label('Last message at')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('country')
                    ->options(fn (): array => Visitor::query()
                        ->whereNotNull('country')
                        ->orderBy('country')
                        ->pluck('country', 'country')
                        ->all())
                    ->searchable(),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
