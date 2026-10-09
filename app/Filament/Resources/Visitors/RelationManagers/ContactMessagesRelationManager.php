<?php

namespace App\Filament\Resources\Visitors\RelationManagers;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Models\ContactMessage;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContactMessagesRelationManager extends RelationManager
{
    protected static string $relationship = 'contactMessages';

    protected static ?string $title = 'Contact messages';

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->recordUrl(fn (ContactMessage $record): string => ContactMessageResource::getUrl('view', ['record' => $record]))
            ->columns([
                TextColumn::make('created_at')
                    ->label('Sent')
                    ->since()
                    ->sortable(),
                TextColumn::make('message')
                    ->limit(80)
                    ->tooltip(fn (ContactMessage $record): string => $record->message)
                    ->wrap(),
            ]);
    }
}
