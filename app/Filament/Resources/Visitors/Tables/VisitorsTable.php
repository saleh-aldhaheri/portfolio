<?php

namespace App\Filament\Resources\Visitors\Tables;

use App\Models\Visitor;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class VisitorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('uuid')
                    ->label('UUID')
                    ->searchable()
                    ->copyable()
                    ->wrap(),
                TextColumn::make('name')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('city')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('country')
                    ->searchable(),
                TextColumn::make('os')
                    ->searchable(),
                TextColumn::make('browser')
                    ->limit(30)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('contact_messages_count')
                    ->label('Messages')
                    ->counts('contactMessages')
                    ->badge()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('country')
                    ->options(fn (): array => Visitor::query()->distinct()->pluck('country', 'country')->filter()->sort()->all())
                    ->searchable(),
                SelectFilter::make('os')
                    ->options(fn (): array => Visitor::query()->distinct()->pluck('os', 'os')->filter()->sort()->all())
                    ->searchable(),
                SelectFilter::make('language')
                    ->options(fn (): array => Visitor::query()->distinct()->pluck('language', 'language')->filter()->sort()->all())
                    ->searchable(),
                TernaryFilter::make('has_email')
                    ->label('Email status')
                    ->placeholder('All visitors')
                    ->trueLabel('Has email')
                    ->falseLabel('No email')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('email'),
                        false: fn ($query) => $query->whereNull('email'),
                    ),
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
