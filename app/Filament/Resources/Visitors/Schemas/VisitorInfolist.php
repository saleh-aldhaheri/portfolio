<?php

namespace App\Filament\Resources\Visitors\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VisitorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identity')
                    ->description('Collected when the visitor interacted with the site')
                    ->schema([
                        TextEntry::make('name')
                            ->placeholder('-'),
                        TextEntry::make('email')
                            ->label('Email address')
                            ->copyable()
                            ->placeholder('-'),
                        TextEntry::make('phone')
                            ->placeholder('-'),
                        TextEntry::make('company')
                            ->placeholder('-'),
                    ])
                    ->columns(3),
                Section::make('Location & Device')
                    ->schema([
                        TextEntry::make('uuid')
                            ->label('UUID')
                            ->copyable()
                            ->placeholder('-'),
                        TextEntry::make('ip_address')
                            ->copyable()
                            ->placeholder('-'),
                        TextEntry::make('city')
                            ->placeholder('-'),
                        TextEntry::make('country')
                            ->placeholder('-'),
                        TextEntry::make('os')
                            ->placeholder('-'),
                        TextEntry::make('browser')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
                Section::make('Messages')
                    ->description('Contact messages sent by this visitor')
                    ->schema([
                        RepeatableEntry::make('contactMessages')
                            ->schema([
                                TextEntry::make('name')
                                    ->placeholder('-'),
                                TextEntry::make('email')
                                    ->placeholder('-'),
                                TextEntry::make('created_at')
                                    ->since(),
                                TextEntry::make('message')
                                    ->prose()
                                    ->columnSpanFull(),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                    ]),
                Section::make('Timestamps')
                    ->schema([
                        TextEntry::make('created_at')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->dateTime(),
                    ]),
            ]);
    }
}
