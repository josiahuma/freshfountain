<?php

namespace App\Filament\Resources\JobListings\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class JobListingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('location')
                    ->toggleable()
                    ->sortable(),

                TextColumn::make('employment_type')
                    ->label('Type')
                    ->toggleable(),

                TextColumn::make('closing_date')
                    ->date('d M Y')
                    ->label('Closes')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('applications_count')
                    ->counts('applications')
                    ->label('Applicants')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('template')
                    ->options([
                        'adult_carer' => 'Adult Carer',
                        'children_carer' => 'Children Carer',
                    ]),
                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        1 => 'Active',
                        0 => 'Inactive',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
