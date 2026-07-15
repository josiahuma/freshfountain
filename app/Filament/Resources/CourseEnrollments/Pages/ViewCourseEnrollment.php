<?php

namespace App\Filament\Resources\CourseEnrollments\Pages;

use App\Filament\Resources\CourseEnrollments\CourseEnrollmentResource;
use App\Models\CourseEnrollment;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;
use Filament\Forms\Components\Textarea;

class ViewCourseEnrollment extends ViewRecord
{
    protected static string $resource =
        CourseEnrollmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('recalculate')
                ->label('Recalculate Progress')
                ->icon(
                    Heroicon::OutlinedArrowPath
                )
                ->color('info')
                ->requiresConfirmation()
                ->action(function (): void {
                    /** @var CourseEnrollment $record */
                    $record = $this->record;

                    $record->recalculateProgress();
                    $record->refresh();

                    Notification::make()
                        ->title(
                            'Progress recalculated'
                        )
                        ->success()
                        ->send();

                    $this->redirect(
                        static::getResource()::getUrl(
                            'view',
                            [
                                'record' =>
                                    $record,
                            ]
                        )
                    );
                }),

            Action::make('pause')
                ->label('Pause Enrolment')
                ->icon(
                    Heroicon::OutlinedPauseCircle
                )
                ->color('warning')
                ->visible(
                    fn (): bool =>
                        $this->record->status
                        === CourseEnrollment::STATUS_ACTIVE
                )
                ->modalHeading(
                    'Pause course enrolment'
                )
                ->modalDescription(
                    'The student will immediately lose access to the course and its learning material.'
                )
                ->schema([
                    Textarea::make('pause_reason')
                        ->label('Reason for pausing')
                        ->helperText(
                            'This reason will be displayed to the student.'
                        )
                        ->required()
                        ->rows(4)
                        ->maxLength(1000),
                ])
                ->action(
                    function (array $data): void {
                        $this->record->update([
                            'status' =>
                                CourseEnrollment::STATUS_PAUSED,

                            'pause_reason' =>
                                $data['pause_reason'],

                            'paused_at' => now(),

                            'paused_by' => auth()->id(),
                        ]);

                        Notification::make()
                            ->title('Enrolment paused')
                            ->warning()
                            ->send();

                        $this->redirect(
                            static::getResource()::getUrl(
                                'view',
                                [
                                    'record' => $this->record,
                                ]
                            )
                        );
                    }
                ),

            Action::make('reactivate')
                ->label('Reactivate')
                ->icon(
                    Heroicon::OutlinedPlayCircle
                )
                ->color('success')
                ->visible(
                    fn (): bool =>
                        in_array(
                            $this->record->status,
                            [
                                CourseEnrollment::STATUS_PAUSED,
                                CourseEnrollment::STATUS_CANCELLED,
                            ],
                            true
                        )
                )
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->record->update([
                        'status' =>
                            CourseEnrollment::STATUS_ACTIVE,

                        'pause_reason' => null,
                        'paused_at' => null,
                        'paused_by' => null,
                        'completed_at' => null,
                        'last_activity_at' => now(),
                    ]);

                    $this->record->recalculateProgress();

                    Notification::make()
                        ->title(
                            'Enrolment reactivated'
                        )
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getTitle(): string
    {
        return $this->record->display_title;
    }
}