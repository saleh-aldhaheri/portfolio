<?php

namespace App\Filament\Resources\Conversations\Schemas;

use App\Models\Visitor;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ConversationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Visitor')
                    ->description('Basic information collected from this visitor')
                    ->schema([
                        TextEntry::make('name')
                            ->placeholder('-'),
                        TextEntry::make('email')
                            ->copyable()
                            ->placeholder('-'),
                        TextEntry::make('phone')
                            ->placeholder('-'),
                        TextEntry::make('company')
                            ->placeholder('-'),
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
                Section::make('Conversation statistics')
                    ->schema([
                        TextEntry::make('messages_count')
                            ->label('Total messages')
                            ->state(fn (Visitor $record): int => $record->messages()->count())
                            ->numeric(),
                        TextEntry::make('first_message_at')
                            ->label('First message')
                            ->placeholder('-')
                            ->dateTime()
                            ->state(fn (Visitor $record): ?string => $record->messages()->min('created_at')),
                        TextEntry::make('last_message_at')
                            ->label('Last message')
                            ->placeholder('-')
                            ->dateTime()
                            ->state(fn (Visitor $record): ?string => $record->messages()->max('created_at')),
                    ])
                    ->columns(3),
                Section::make('Conversation')
                    ->description('Scroll to the top of the conversation to load older messages')
                    ->schema([
                        Livewire::make(
                            'components.conversation-viewer',
                            fn (Visitor $record): array => ['visitorId' => $record->id]
                        )
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
