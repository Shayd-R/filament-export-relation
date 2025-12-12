<?php

namespace Shayd\FilamentSmartExport\Tests\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Shayd\FilamentSmartExport\Actions\SmartExportBulkAction;
use Shayd\FilamentSmartExport\Actions\SmartExportHeaderAction;
use Shayd\FilamentSmartExport\Tests\Models\User;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('created_at'),
            ])
            ->headerActions([
                SmartExportHeaderAction::make(),
            ])
            ->bulkActions([
                SmartExportBulkAction::make(),
            ]);
    }
}
