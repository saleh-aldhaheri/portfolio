<?php

namespace App\Filament\Resources\Visitors;

use App\Filament\Resources\Visitors\Pages\ListVisitors;
use App\Filament\Resources\Visitors\Pages\ViewVisitor;
use App\Filament\Resources\Visitors\RelationManagers\ContactMessagesRelationManager;
use App\Filament\Resources\Visitors\RelationManagers\EventsRelationManager;
use App\Filament\Resources\Visitors\Schemas\VisitorForm;
use App\Filament\Resources\Visitors\Schemas\VisitorInfolist;
use App\Filament\Resources\Visitors\Tables\VisitorsTable;
use App\Models\Visitor;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class VisitorResource extends Resource
{
    protected static ?string $model = Visitor::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    public static function form(Schema $schema): Schema
    {
        return VisitorForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VisitorInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VisitorsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ContactMessagesRelationManager::class,
            EventsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVisitors::route('/'),
            'view' => ViewVisitor::route('/{record}'),
        ];
    }
}
