<?php

namespace App\Filament\Resources\JobApplications\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JobApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Application Status')
                ->schema([
                    Select::make('status')
                        ->label('Status')
                        ->options(self::statusOptions())
                        ->required()
                        ->native(false),

                    // Keep answers read-only here. We show them nicely in the View page.
                    Textarea::make('answers')
                        ->label('Answers (raw JSON)')
                        ->rows(10)
                        ->disabled()
                        ->dehydrated(false),
                ]),
        ]);
    }

    public static function statusOptions(): array
    {
        return [
            'new' => 'New',
            'reviewed' => 'Reviewed',
            'shortlisted' => 'Shortlisted',
            'rejected' => 'Rejected',
        ];
    }
}
