<?php

namespace App\Filament\Resources\CalendarEvents\Schemas;

use App\Models\CalendarEvent;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class CalendarEventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('source')
                    ->default(CalendarEvent::SOURCE_INTERNAL),

                Section::make('Event Details')
                    ->description(
                        'Enter the main information visitors will see for this event.'
                    )
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        TextInput::make('title')
                            ->label('Event title')
                            ->placeholder('For example: Refresh Monthly Service')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        RichEditor::make('description')
                            ->label('Event description')
                            ->placeholder(
                                'Add useful information about the event...'
                            )
                            ->toolbarButtons([
                                ['bold', 'italic', 'underline'],
                                ['bulletList', 'orderedList'],
                                ['link'],
                                ['undo', 'redo'],
                            ])
                            ->columnSpanFull(),

                        TextInput::make('location')
                            ->label('Venue or location')
                            ->placeholder(
                                'For example: Fresh Fountain Christian Network'
                            )
                            ->prefixIcon('heroicon-o-map-pin')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('external_url')
                            ->label('External link')
                            ->placeholder('https://...')
                            ->helperText(
                                'Optional. Add a livestream, registration, Zoom or information link.'
                            )
                            ->prefixIcon('heroicon-o-link')
                            ->url()
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),

                Section::make('Date and Time')
                    ->description('Choose when the event begins and ends.')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Toggle::make('is_all_day')
                            ->label('All-day event')
                            ->helperText(
                                'Enable this when the event does not require a specific time.'
                            )
                            ->default(false)
                            ->live()
                            ->columnSpanFull(),

                        DateTimePicker::make('starts_at')
                            ->label('Starts')
                            ->required()
                            ->native(false)
                            ->seconds(false)
                            ->minutesStep(5)
                            ->displayFormat(
                                fn (Get $get): string => $get('is_all_day')
                                    ? 'd M Y'
                                    : 'd M Y, H:i'
                            )
                            ->prefixIcon('heroicon-o-calendar')
                            ->columnSpan([
                                'default' => 2,
                                'md' => 1,
                            ]),

                        DateTimePicker::make('ends_at')
                            ->label('Ends')
                            ->native(false)
                            ->seconds(false)
                            ->minutesStep(5)
                            ->displayFormat(
                                fn (Get $get): string => $get('is_all_day')
                                    ? 'd M Y'
                                    : 'd M Y, H:i'
                            )
                            ->afterOrEqual('starts_at')
                            ->validationMessages([
                                'after_or_equal' =>
                                    'The end date and time must be after the start date and time.',
                            ])
                            ->prefixIcon('heroicon-o-calendar')
                            ->columnSpan([
                                'default' => 2,
                                'md' => 1,
                            ]),
                    ])
                    ->columns(2),

                Section::make('Category and Appearance')
                    ->description(
                        'Categorise the event and choose how it appears on the calendar.'
                    )
                    ->icon('heroicon-o-paint-brush')
                    ->schema([
                        Select::make('category')
                            ->label('Event category')
                            ->options(CalendarEvent::categoryOptions())
                            ->default('general')
                            ->required()
                            ->native(false)
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(
                                function (?string $state, Set $set): void {
                                    $category = $state ?: 'general';

                                    $set(
                                        'colour',
                                        CalendarEvent::categoryColours()[$category]
                                            ?? CalendarEvent::categoryColours()['general']
                                    );
                                }
                            ),

                        ColorPicker::make('colour')
                            ->label('Calendar colour')
                            ->default(
                                CalendarEvent::categoryColours()['general']
                            )
                            ->required(),

                        FileUpload::make('image')
                            ->label('Event image')
                            ->helperText(
                                'Optional. Recommended ratio: 16:9. Maximum file size: 5 MB.'
                            )
                            ->image()
                            ->imageEditor()
                            ->imageCropAspectRatio('16:9')
                            ->disk('public')
                            ->directory('calendar-events')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->acceptedFileTypes([
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Publishing')
                    ->description(
                        'Control whether and where the event appears publicly.'
                    )
                    ->icon('heroicon-o-eye')
                    ->schema([
                        Toggle::make('is_published')
                            ->label('Published')
                            ->helperText(
                                'Only published events will appear on the website.'
                            )
                            ->default(true),

                        Toggle::make('is_featured')
                            ->label('Featured event')
                            ->helperText(
                                'Featured events can receive prominent homepage placement.'
                            )
                            ->default(false),

                        TextInput::make('sort_order')
                            ->label('Manual sort order')
                            ->helperText(
                                'Lower numbers can be displayed before higher numbers.'
                            )
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 3,
                    ]),

                Section::make('Recurring Event')
                    ->description(
                        'Repeat this event daily, weekly, monthly or yearly until the selected end date.'
                    )
                    ->icon('heroicon-o-arrow-path')
                    ->collapsed()
                    ->schema([
                        Select::make('recurrence_type')
                            ->label('Repeats')
                            ->options(CalendarEvent::recurrenceOptions())
                            ->default(CalendarEvent::RECURRENCE_NONE)
                            ->required()
                            ->native(false)
                            ->live(),

                        DatePicker::make('recurrence_until')
                            ->label('Repeat until')
                            ->native(false)
                            ->minDate(
                                fn (Get $get): mixed => $get('starts_at')
                            )
                            ->visible(
                                fn (Get $get): bool =>
                                    filled($get('recurrence_type'))
                                    && $get('recurrence_type')
                                        !== CalendarEvent::RECURRENCE_NONE
                            )
                            ->required(
                                fn (Get $get): bool =>
                                    filled($get('recurrence_type'))
                                    && $get('recurrence_type')
                                        !== CalendarEvent::RECURRENCE_NONE
                            ),
                    ])
                    ->columns(2),
            ]);
    }
}