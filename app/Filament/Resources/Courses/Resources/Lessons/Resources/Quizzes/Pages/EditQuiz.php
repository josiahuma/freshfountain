<?php

namespace App\Filament\Resources\Courses\Resources\Lessons\Resources\Quizzes\Pages;

use App\Filament\Resources\Courses\Resources\Lessons\Resources\Quizzes\QuizResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditQuiz extends EditRecord
{
    protected static string $resource =
        QuizResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Quiz updated successfully';
    }
}