<?php

namespace App\Filament\Resources\ContactMessages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('visitor.name')
                    ->label('Name')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('visitor.email')
                    ->label('Email')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('message')
                    ->limit(60)
                    ->wrap(),
                TextColumn::make('created_at')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('visitor_id')
                    ->label('Visitor')
                    ->relationship('visitor', 'email')
                    ->searchable(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
