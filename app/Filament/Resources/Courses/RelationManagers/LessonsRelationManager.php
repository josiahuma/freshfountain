<?php

namespace App\Filament\Resources\Courses\RelationManagers;

use App\Filament\Resources\Courses\Resources\Lessons\LessonResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class LessonsRelationManager extends RelationManager
{
    protected static string $relationship = 'lessons';

    protected static ?string $relatedResource =
        LessonResource::class;

    protected static ?string $title = 'Course Lessons';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Course Lessons')
            ->description(
                'Create, order and manage the lessons in this course.'
            )
            ->headerActions([
                CreateAction::make()
                    ->label('Add Lesson')
                    ->icon('heroicon-o-plus'),
            ]);
    }
}