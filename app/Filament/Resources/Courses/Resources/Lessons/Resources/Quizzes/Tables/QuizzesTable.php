<?php

namespace App\Filament\Resources\Courses\Resources\Lessons\Resources\Quizzes\Tables;

use App\Models\Quiz;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuizzesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Quiz')
                    ->description(
                        fn (Quiz $record): ?string =>
                            $record->description
                    )
                    ->searchable()
                    ->weight('bold')
                    ->wrap(),

                TextColumn::make('questions_count')
                    ->label('Questions')
                    ->counts('questions')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('attempts_count')
                    ->label('Attempts')
                    ->counts('attempts')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                TextColumn::make('pass_percentage')
                    ->label('Pass mark')
                    ->suffix('%')
                    ->sortable(),

                TextColumn::make('maximum_attempts')
                    ->label('Max attempts')
                    ->formatStateUsing(
                        fn (?int $state): string =>
                            $state
                                ? (string) $state
                                : 'Unlimited'
                    ),

                IconColumn::make('is_required')
                    ->label('Required')
                    ->boolean()
                    ->trueIcon(
                        Heroicon::OutlinedLockClosed
                    )
                    ->falseIcon(
                        Heroicon::OutlinedMinus
                    )
                    ->trueColor('warning')
                    ->falseColor('gray'),

                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Build Quiz'),

                DeleteAction::make(),
            ])
            ->emptyStateHeading(
                'No quiz has been created'
            )
            ->emptyStateDescription(
                'Add an assessment for this lesson.'
            )
            ->emptyStateIcon(
                Heroicon::OutlinedQuestionMarkCircle
            );
    }
}