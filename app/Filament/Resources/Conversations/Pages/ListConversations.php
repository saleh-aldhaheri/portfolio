<?php

namespace App\Filament\Resources\Conversations\Pages;

use App\Filament\Resources\Conversations\ConversationResource;
use App\Models\Visitor;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListConversations extends ListRecords
{
    protected static string $resource = ConversationResource::class;

    protected function getTableQuery(): Builder
    {
        return Visitor::query()
            ->whereHas('messages')
            ->withCount('messages');
    }
}
