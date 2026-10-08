<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ContactMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('visitor_id')
                    ->label('Visitor')
                    ->relationship('visitor', 'email')
                    ->searchable()
                    ->required(),
                Textarea::make('message')
                    ->required()
                    ->rows(8)
                    ->columnSpanFull(),
            ]);
    }
}
