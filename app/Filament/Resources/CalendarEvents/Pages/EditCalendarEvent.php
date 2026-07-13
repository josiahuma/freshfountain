<?php

namespace App\Filament\Resources\CalendarEvents\Pages;

use App\Filament\Resources\CalendarEvents\CalendarEventResource;
use App\Models\CalendarEvent;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Carbon;

class EditCalendarEvent extends EditRecord
{
    protected static string $resource = CalendarEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = $this->prepareEventData($data);

        $data['source'] = CalendarEvent::SOURCE_INTERNAL;
        $data['eventib_event_id'] = null;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Calendar event updated successfully';
    }

    private function prepareEventData(array $data): array
    {
        $category = $data['category'] ?? 'general';

        $data['category'] = $category;

        if (blank($data['colour'] ?? null)) {
            $data['colour'] =
                CalendarEvent::categoryColours()[$category]
                ?? CalendarEvent::categoryColours()['general'];
        }

        if (
            ! empty($data['is_all_day'])
            && ! empty($data['starts_at'])
        ) {
            $data['starts_at'] = Carbon::parse(
                $data['starts_at']
            )->startOfDay();

            if (! empty($data['ends_at'])) {
                $data['ends_at'] = Carbon::parse(
                    $data['ends_at']
                )->endOfDay();
            }
        }

        if (
            ($data['recurrence_type']
                ?? CalendarEvent::RECURRENCE_NONE)
            === CalendarEvent::RECURRENCE_NONE
        ) {
            $data['recurrence_until'] = null;
        }

        return $data;
    }
}