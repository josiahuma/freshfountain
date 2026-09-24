<?php

namespace App\Filament\Resources\SmsTemplates\Tables;

use App\Models\SmsTemplate;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SmsTemplatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')->searchable()->sortable()->weight('bold'),
                TextColumn::make('category')->formatStateUsing(fn (?string $state) => SmsTemplate::categoryOptions()[$state] ?? ucfirst((string) $state))->badge(),
                TextColumn::make('body')->label('Message')->limit(80)->wrap(),
                IconColumn::make('is_birthday')->label('Birthday')->boolean(),
                IconColumn::make('is_active')->label('Active')->boolean()->sortable(),
                TextColumn::make('updated_at')->label('Updated')->since()->sortable(),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
