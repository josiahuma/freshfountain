<?php

namespace App\Filament\Resources\SmsLogs\Pages;

use App\Filament\Resources\SmsLogs\SmsLogResource;
use Filament\Resources\Pages\ListRecords;

class ListSmsLogs extends ListRecords
{
    protected static string $resource = SmsLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getSubheading(): ?string
    {
        return 'Read-only audit trail of SMS messages sent from Fresh Fountain CRM.';
    }
}
