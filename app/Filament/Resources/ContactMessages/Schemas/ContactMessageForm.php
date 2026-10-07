<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ContactMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
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
