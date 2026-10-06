<?php

namespace App\Filament\Resources\Visitors\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class VisitorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('uuid')
                    ->label('UUID')
                    ->required(),
                TextInput::make('ip_address'),
                TextInput::make('city'),
                TextInput::make('country'),
                TextInput::make('os'),
                TextInput::make('browser'),
                TextInput::make('language'),
                Textarea::make('referrer')
                    ->columnSpanFull(),
                TextInput::make('user_id')
                    ->numeric(),
                TextInput::make('name'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('company'),
                TextInput::make('occupation'),
            ]);
    }
}
