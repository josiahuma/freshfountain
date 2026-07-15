<?php

namespace App\Filament\Resources\Courses\Schemas;

use App\Models\Course;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Course Details')
                    ->description(
                        'Enter the main information students will see.'
                    )
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        TextInput::make('title')
                            ->label('Course title')
                            ->placeholder('For example: Membership Class')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                function (
                                    ?string $state,
                                    Set $set
                                ): void {
                                    if (blank($state)) {
                                        return;
                                    }

                                    $set(
                                        'slug',
                                        Str::slug($state)
                                    );
                                }
                            )
                            ->columnSpanFull(),

                        TextInput::make('slug')
                            ->label('URL slug')
                            ->placeholder('membership-class')
                            ->helperText(
                                'Used in the public course URL. It must be unique.'
                            )
                            ->required()
                            ->maxLength(255)
                            ->unique(
                                table: Course::class,
                                column: 'slug',
                                ignoreRecord: true
                            )
                            ->columnSpanFull(),

                        Textarea::make('short_description')
                            ->label('Short description')
                            ->placeholder(
                                'A brief introduction shown on course cards.'
                            )
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),

                        RichEditor::make('description')
                            ->label('Full course description')
                            ->toolbarButtons([
                                ['bold', 'italic', 'underline'],
                                ['h2', 'h3'],
                                ['bulletList', 'orderedList'],
                                ['blockquote', 'link'],
                                ['undo', 'redo'],
                            ])
                            ->columnSpanFull(),
                    ]),

                Section::make('Course Presentation')
                    ->description(
                        'Choose the course type, difficulty and cover image.'
                    )
                    ->icon('heroicon-o-photo')
                    ->schema([
                        FileUpload::make('cover_image')
                            ->label('Course cover image')
                            ->image()
                            ->imageEditor()
                            ->imageCropAspectRatio('16:9')
                            ->disk('public')
                            ->directory('courses')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->acceptedFileTypes([
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                            ])
                            ->helperText(
                                'Recommended ratio: 16:9. Maximum size: 5 MB.'
                            )
                            ->columnSpanFull(),

                        Select::make('course_type')
                            ->label('Course type')
                            ->options(Course::typeOptions())
                            ->default(Course::TYPE_GENERAL)
                            ->required()
                            ->native(false),

                        Select::make('difficulty_level')
                            ->label('Difficulty level')
                            ->options(
                                Course::difficultyOptions()
                            )
                            ->default(Course::LEVEL_BEGINNER)
                            ->required()
                            ->native(false),

                        TextInput::make(
                            'estimated_duration_minutes'
                        )
                            ->label('Estimated duration')
                            ->helperText(
                                'Total estimated course duration in minutes.'
                            )
                            ->numeric()
                            ->minValue(1)
                            ->suffix('minutes'),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 3,
                    ]),

                Section::make('Learning Settings')
                    ->description(
                        'Control enrolment, lesson order and certificates.'
                    )
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->schema([
                        Toggle::make('allow_self_enrolment')
                            ->label('Allow self-enrolment')
                            ->helperText(
                                'Members can enrol themselves from the learning portal.'
                            )
                            ->default(true),

                        Toggle::make(
                            'requires_sequential_progress'
                        )
                            ->label('Require lesson order')
                            ->helperText(
                                'Students must complete lessons in sequence.'
                            )
                            ->default(true),

                        Toggle::make('certificate_enabled')
                            ->label('Enable certificate')
                            ->helperText(
                                'A certificate can be issued after completion.'
                            )
                            ->default(false),

                        TextInput::make('sort_order')
                            ->label('Sort order')
                            ->helperText(
                                'Lower numbers appear first.'
                            )
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ]),

                Section::make('Publishing')
                    ->description(
                        'Control whether the course is visible to students.'
                    )
                    ->icon('heroicon-o-eye')
                    ->schema([
                        Toggle::make('is_published')
                            ->label('Published')
                            ->helperText(
                                'Only published courses appear in the learning portal.'
                            )
                            ->default(false)
                            ->live(),

                        Toggle::make('is_featured')
                            ->label('Featured course')
                            ->helperText(
                                'Featured courses may receive prominent placement.'
                            )
                            ->default(false),

                        DateTimePicker::make('published_at')
                            ->label('Publish date and time')
                            ->helperText(
                                'Leave blank to publish immediately when enabled.'
                            )
                            ->native(false)
                            ->seconds(false)
                            ->visible(
                                fn (Get $get): bool =>
                                    (bool) $get('is_published')
                            ),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 3,
                    ]),
            ]);
    }
}