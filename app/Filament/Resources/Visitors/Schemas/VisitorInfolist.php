<?php

namespace App\Filament\Resources\Visitors\Schemas;

use App\Enums\MessageRole;
use App\Models\Visitor;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;

class VisitorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Activity')
                    ->description('What this visitor has done on the site')
                    ->schema([
                        TextEntry::make('contact_messages_count')
                            ->label('Contact messages sent')
                            ->state(fn (Visitor $record): int => $record->contactMessages()->count())
                            ->numeric(),
                        TextEntry::make('ai_messages_count')
                            ->label('Messages sent to the AI')
                            ->state(fn (Visitor $record): int => $record->messages()->where('role', MessageRole::VISITOR)->count())
                            ->numeric(),
                        TextEntry::make('events_count')
                            ->label('Events tracked')
                            ->state(fn (Visitor $record): int => $record->logs()->count())
                            ->numeric(),
                        TextEntry::make('top_event')
                            ->label('Most frequent event')
                            ->placeholder('No events yet')
                            ->state(function (Visitor $record): ?string {
                                $top = $record->logs()
                                    ->select('event', DB::raw('COUNT(*) as total'))
                                    ->groupBy('event')
                                    ->orderByDesc('total')
                                    ->orderBy('event')
                                    ->first();

                                return $top === null ? null : "{$top->event} ({$top->total})";
                            }),
                    ])
                    ->columns(4),
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
                Section::make('Timestamps')
                    ->schema([
                        TextEntry::make('created_at')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->dateTime(),
                    ])
                    ->columns(2),
            ])
            ->columns(1);
    }
}
