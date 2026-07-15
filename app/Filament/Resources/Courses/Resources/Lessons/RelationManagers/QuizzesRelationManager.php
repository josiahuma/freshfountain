<?php

namespace App\Filament\Resources\Courses\Resources\Lessons\RelationManagers;

use App\Filament\Resources\Courses\Resources\Lessons\Resources\Quizzes\QuizResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class QuizzesRelationManager extends RelationManager
{
    protected static string $relationship = 'quizzes';

    protected static ?string $relatedResource =
        QuizResource::class;

    protected static ?string $title =
        'Lesson Quiz';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Lesson Quiz')
            ->description(
                'Create and manage the assessment attached to this lesson.'
            )
            ->headerActions([
                CreateAction::make()
                    ->label('Create Quiz')
                    ->icon(
                        'heroicon-o-plus'
                    )
                    ->visible(
                        fn (): bool =>
                            $this
                                ->getOwnerRecord()
                                ->quizzes()
                                ->doesntExist()
                    ),
            ]);
    }
}