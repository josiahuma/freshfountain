<?php

namespace App\Filament\Resources\Courses\Tables;

use App\Models\Course;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CoursesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('cover_image')
                    ->label('Cover')
                    ->disk('public')
                    ->visibility('public')
                    ->square()
                    ->defaultImageUrl(
                        asset('images/course-placeholder.png')
                    ),

                TextColumn::make('title')
                    ->label('Course')
                    ->description(
                        fn (Course $record): ?string =>
                            $record->short_description
                    )
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),

                TextColumn::make('course_type')
                    ->label('Type')
                    ->formatStateUsing(
                        fn (?string $state): string =>
                            Course::typeOptions()[
                                $state ?? Course::TYPE_GENERAL
                            ] ?? ucfirst(
                                str_replace(
                                    '_',
                                    ' ',
                                    $state ?? 'general'
                                )
                            )
                    )
                    ->badge()
                    ->color(
                        fn (?string $state): string =>
                            match ($state) {
                                Course::TYPE_MEMBERSHIP =>
                                    'info',

                                Course::TYPE_BAPTISM =>
                                    'primary',

                                Course::TYPE_NEW_BELIEVERS =>
                                    'success',

                                Course::TYPE_WORKERS =>
                                    'warning',

                                Course::TYPE_LEADERSHIP =>
                                    'danger',

                                default => 'gray',
                            }
                    )
                    ->sortable(),

                TextColumn::make('difficulty_level')
                    ->label('Level')
                    ->formatStateUsing(
                        fn (?string $state): string =>
                            Course::difficultyOptions()[
                                $state
                                    ?? Course::LEVEL_BEGINNER
                            ] ?? ucfirst(
                                $state ?? 'Beginner'
                            )
                    )
                    ->badge()
                    ->color(
                        fn (?string $state): string =>
                            match ($state) {
                                Course::LEVEL_INTERMEDIATE =>
                                    'warning',

                                Course::LEVEL_ADVANCED =>
                                    'danger',

                                default => 'success',
                            }
                    )
                    ->sortable(),

                TextColumn::make('lessons_count')
                    ->label('Lessons')
                    ->counts('lessons')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('enrollments_count')
                    ->label('Students')
                    ->counts('enrollments')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                TextColumn::make(
                    'estimated_duration_minutes'
                )
                    ->label('Duration')
                    ->formatStateUsing(
                        fn (
                            ?int $state,
                            Course $record
                        ): string =>
                            $record
                                ->estimated_duration_label
                                ?? 'Not set'
                    )
                    ->toggleable(),

                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedStar)
                    ->falseIcon(Heroicon::OutlinedMinus)
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->sortable(),

                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),
            ])
            ->filters([
                SelectFilter::make('course_type')
                    ->label('Course type')
                    ->options(Course::typeOptions())
                    ->native(false),

                SelectFilter::make('difficulty_level')
                    ->label('Difficulty')
                    ->options(
                        Course::difficultyOptions()
                    )
                    ->native(false),

                TernaryFilter::make('is_published')
                    ->label('Published status')
                    ->placeholder('All courses')
                    ->trueLabel('Published courses')
                    ->falseLabel('Unpublished courses'),

                TernaryFilter::make('is_featured')
                    ->label('Featured status')
                    ->placeholder('All courses')
                    ->trueLabel('Featured courses')
                    ->falseLabel('Not featured'),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Manage'),

                ReplicateAction::make()
                    ->label('Duplicate')
                    ->icon(
                        Heroicon::OutlinedDocumentDuplicate
                    )
                    ->excludeAttributes([
                        'slug',
                        'published_at',
                    ])
                    ->beforeReplicaSaved(
                        function (Course $replica): void {
                            $replica->title =
                                $replica->title . ' – Copy';

                            $replica->slug = null;

                            $replica->is_published = false;

                            $replica->is_featured = false;

                            $replica->published_at = null;
                        }
                    )
                    ->successNotificationTitle(
                        'Course duplicated as an unpublished copy'
                    ),

                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No courses yet')
            ->emptyStateDescription(
                'Create the first Fresh Fountain learning course.'
            )
            ->emptyStateIcon(
                Heroicon::OutlinedAcademicCap
            );
    }
}