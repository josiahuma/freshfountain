<?php

namespace App\Filament\Resources\Courses\Resources\Lessons\Resources\Quizzes\Pages;

use App\Filament\Resources\Courses\Resources\Lessons\Resources\Quizzes\QuizResource;
use Filament\Resources\Pages\CreateRecord;

class CreateQuiz extends CreateRecord
{
    protected static string $resource =
        QuizResource::class;


    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Quiz created successfully';
    }
}