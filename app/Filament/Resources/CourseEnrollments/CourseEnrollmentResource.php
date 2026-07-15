<?php

namespace App\Filament\Resources\CourseEnrollments;

use App\Filament\Resources\CourseEnrollments\Pages\ListCourseEnrollments;
use App\Filament\Resources\CourseEnrollments\Pages\ViewCourseEnrollment;
use App\Filament\Resources\CourseEnrollments\Schemas\CourseEnrollmentInfolist;
use App\Filament\Resources\CourseEnrollments\Tables\CourseEnrollmentsTable;
use App\Models\CourseEnrollment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class CourseEnrollmentResource extends Resource
{
    protected static ?string $model = CourseEnrollment::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedChartBarSquare;

    protected static string|UnitEnum|null $navigationGroup =
        'Learning Management';

    protected static ?string $navigationLabel =
        'Student Progress';

    protected static ?string $modelLabel =
        'Student Progress';

    protected static ?string $pluralModelLabel =
        'Student Progress';

    protected static ?string $recordTitleAttribute =
        'display_title';

    protected static ?int $navigationSort = 2;

    public static function infolist(Schema $schema): Schema
    {
        return CourseEnrollmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CourseEnrollmentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCourseEnrollments::route('/'),
            'view' => ViewCourseEnrollment::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'user',
                'course',
                'lastLesson',
            ])
            ->withCount([
                'lessonCompletions',
                'quizAttempts',
                'quizAttempts as passed_quiz_attempts_count' =>
                    fn (Builder $query): Builder =>
                        $query->where('passed', true),
            ])
            ->withAvg(
                'quizAttempts',
                'percentage'
            );
    }

    public static function getNavigationBadge(): ?string
    {
        $count = CourseEnrollment::query()
            ->where(
                'status',
                CourseEnrollment::STATUS_ACTIVE
            )
            ->count();

        return $count > 0
            ? (string) $count
            : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'info';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'user.name',
            'user.email',
            'course.title',
        ];
    }

    public static function getGlobalSearchResultTitle(
        \Illuminate\Database\Eloquent\Model $record
    ): string {
        /** @var CourseEnrollment $record */
        return $record->display_title;
    }

    public static function getGlobalSearchResultDetails(
        \Illuminate\Database\Eloquent\Model $record
    ): array {
        /** @var CourseEnrollment $record */
        return [
            'Progress' =>
                "{$record->progress_percentage}%",

            'Status' =>
                ucfirst($record->status),
        ];
    }
}