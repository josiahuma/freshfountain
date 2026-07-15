<?php

namespace App\Filament\Resources\Courses\Resources\Lessons\Schemas;

use App\Models\Lesson;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class LessonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Lesson Details')
                    ->description(
                        'Enter the lesson title, summary and teaching content.'
                    )
                    ->icon('heroicon-o-book-open')
                    ->schema([
                        TextInput::make('title')
                            ->label('Lesson title')
                            ->placeholder(
                                'For example: Understanding Church Membership'
                            )
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
                            ->label('Lesson slug')
                            ->helperText(
                                'Used in the lesson URL. It must be unique within the course.'
                            )
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('summary')
                            ->label('Lesson summary')
                            ->placeholder(
                                'Briefly explain what the student will learn.'
                            )
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),

                        RichEditor::make('content')
                            ->label('Lesson content')
                            ->helperText(
                                'Add teaching notes, Bible references and supporting information.'
                            )
                            ->toolbarButtons([
                                ['bold', 'italic', 'underline'],
                                ['h2', 'h3'],
                                ['bulletList', 'orderedList'],
                                ['blockquote', 'link'],
                                ['undo', 'redo'],
                            ])
                            ->columnSpanFull(),
                    ]),

                Section::make('Lesson Video')
                    ->description(
                        'Add a public or unlisted YouTube or Vimeo video.'
                    )
                    ->icon('heroicon-o-play-circle')
                    ->schema([
                        TextInput::make('video_url')
                            ->label('YouTube or Vimeo URL')
                            ->placeholder(
                                'https://www.youtube.com/watch?v=...'
                            )
                            ->helperText(
                                'Paste the full YouTube or Vimeo video URL.'
                            )
                            ->url()
                            ->maxLength(1000)
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                function (
                                    ?string $state,
                                    Set $set
                                ): void {
                                    if (blank($state)) {
                                        $set(
                                            'video_provider',
                                            null
                                        );

                                        return;
                                    }

                                    $set(
                                        'video_provider',
                                        Lesson::detectVideoProvider(
                                            $state
                                        )
                                    );
                                }
                            )
                            ->columnSpanFull(),

                        Select::make('video_provider')
                            ->label('Video provider')
                            ->options(
                                Lesson::videoProviderOptions()
                            )
                            ->native(false)
                            ->placeholder(
                                'Detected automatically'
                            ),

                        TextInput::make(
                            'video_duration_minutes'
                        )
                            ->label('Video duration')
                            ->numeric()
                            ->minValue(1)
                            ->suffix('minutes'),
                    ])
                    ->columns(2),

                Section::make('Lesson Settings')
                    ->description(
                        'Set the lesson order and student access rules.'
                    )
                    ->icon(
                        'heroicon-o-adjustments-horizontal'
                    )
                    ->schema([
                        TextInput::make('sort_order')
                            ->label('Lesson order')
                            ->helperText(
                                'Lower numbers appear first.'
                            )
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->required(),

                        Toggle::make('is_published')
                            ->label('Published')
                            ->helperText(
                                'Only published lessons are visible to students.'
                            )
                            ->default(true),

                        Toggle::make('is_preview')
                            ->label('Free preview')
                            ->helperText(
                                'Allow visitors to view this lesson before enrolment.'
                            )
                            ->default(false),

                        Toggle::make(
                            'requires_manual_completion'
                        )
                            ->label('Require completion button')
                            ->helperText(
                                'Students must confirm when they have completed the lesson.'
                            )
                            ->default(true),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ]),
            ]);
    }
}