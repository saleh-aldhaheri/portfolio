<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactMessageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Message')
                    ->description('Submitted via the contact form')
                    ->schema([
                        TextEntry::make('name')
                            ->placeholder('-'),
                        TextEntry::make('email')
                            ->copyable()
                            ->placeholder('-'),
                        TextEntry::make('message')
                            ->prose()
                            ->columnSpanFull(),
                    ]),
                Section::make('Visitor')
                    ->description('Who submitted the message')
                    ->schema([
                        TextEntry::make('visitor.uuid')
                            ->label('UUID')
                            ->copyable()
                            ->placeholder('-'),
                        TextEntry::make('visitor.ip_address')
                            ->label('IP address')
                            ->copyable()
                            ->placeholder('-'),
                        TextEntry::make('visitor.city')
                            ->placeholder('-'),
                        TextEntry::make('visitor.country')
                            ->placeholder('-'),
                        TextEntry::make('visitor.os')
                            ->placeholder('-'),
                        TextEntry::make('visitor.browser')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
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
