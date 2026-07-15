<?php

namespace App\Filament\Resources\Courses\Resources\Lessons\Resources\Quizzes;

use App\Filament\Resources\Courses\Resources\Lessons\LessonResource;
use App\Filament\Resources\Courses\Resources\Lessons\Resources\Quizzes\Pages\CreateQuiz;
use App\Filament\Resources\Courses\Resources\Lessons\Resources\Quizzes\Pages\EditQuiz;
use App\Filament\Resources\Courses\Resources\Lessons\Resources\Quizzes\Schemas\QuizForm;
use App\Filament\Resources\Courses\Resources\Lessons\Resources\Quizzes\Tables\QuizzesTable;
use App\Models\Quiz;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class QuizResource extends Resource
{
    protected static ?string $model = Quiz::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedQuestionMarkCircle;

    protected static ?string $parentResource =
        LessonResource::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $modelLabel = 'Quiz';

    protected static ?string $pluralModelLabel = 'Quizzes';

    public static function form(Schema $schema): Schema
    {
        return QuizForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuizzesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'create' => CreateQuiz::route('/create'),

            'edit' => EditQuiz::route(
                '/{record}/edit'
            ),
        ];
    }
}