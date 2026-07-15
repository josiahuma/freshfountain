<?php

namespace App\Filament\Resources\Courses\Resources\Lessons\Resources\Quizzes\Schemas;

use Closure;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class QuizForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Quiz Details')
                    ->description(
                        'Configure the assessment students will take after this lesson.'
                    )
                    ->icon(
                        'heroicon-o-question-mark-circle'
                    )
                    ->schema([
                        TextInput::make('title')
                            ->label('Quiz title')
                            ->placeholder(
                                'For example: Membership Lesson Quiz'
                            )
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Instructions')
                            ->placeholder(
                                'Explain what students should do before submitting the quiz.'
                            )
                            ->rows(3)
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ]),

                Section::make('Assessment Settings')
                    ->description(
                        'Set the pass mark, attempts and result behaviour.'
                    )
                    ->icon(
                        'heroicon-o-adjustments-horizontal'
                    )
                    ->schema([
                        TextInput::make('pass_percentage')
                            ->label('Pass mark')
                            ->numeric()
                            ->required()
                            ->default(80)
                            ->minValue(1)
                            ->maxValue(100)
                            ->suffix('%'),

                        TextInput::make('maximum_attempts')
                            ->label('Maximum attempts')
                            ->numeric()
                            ->minValue(1)
                            ->placeholder('Unlimited')
                            ->helperText(
                                'Leave blank to allow unlimited attempts.'
                            ),

                        TextInput::make('sort_order')
                            ->label('Sort order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        Toggle::make('is_required')
                            ->label('Required to complete lesson')
                            ->helperText(
                                'Students must pass this quiz before the lesson can be completed.'
                            )
                            ->default(true),

                        Toggle::make('shuffle_questions')
                            ->label('Shuffle questions')
                            ->helperText(
                                'Display questions in a different order for each attempt.'
                            )
                            ->default(false),

                        Toggle::make('shuffle_answers')
                            ->label('Shuffle answers')
                            ->helperText(
                                'Display answer choices in a different order.'
                            )
                            ->default(false),

                        Toggle::make('show_correct_answers')
                            ->label('Show correct answers')
                            ->helperText(
                                'Reveal correct answers after the student submits.'
                            )
                            ->default(true),

                        Toggle::make('is_published')
                            ->label('Published')
                            ->helperText(
                                'Only published quizzes can be taken by students.'
                            )
                            ->default(true),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ]),

                Section::make('Quiz Questions')
                    ->description(
                        'Add questions and provide at least two answer choices for each question.'
                    )
                    ->icon('heroicon-o-list-bullet')
                    ->schema([
                        Repeater::make('questions')
                            ->label('Questions')
                            ->relationship()
                            ->orderColumn('sort_order')
                            ->minItems(1)
                            ->defaultItems(1)
                            ->addActionLabel(
                                'Add Question'
                            )
                            ->reorderable()
                            ->collapsible()
                            ->cloneable()
                            ->itemLabel(
                                fn (array $state): ?string =>
                                    filled($state['question'] ?? null)
                                        ? $state['question']
                                        : 'New Question'
                            )
                            ->schema([
                                Textarea::make('question')
                                    ->label('Question')
                                    ->placeholder(
                                        'Enter the question here...'
                                    )
                                    ->required()
                                    ->rows(2)
                                    ->columnSpanFull(),

                                Textarea::make('explanation')
                                    ->label(
                                        'Answer explanation'
                                    )
                                    ->placeholder(
                                        'Optional explanation shown after submission.'
                                    )
                                    ->rows(2)
                                    ->columnSpanFull(),

                                TextInput::make('points')
                                    ->label('Points')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->minValue(1),

                                Toggle::make('is_published')
                                    ->label('Published')
                                    ->default(true),

                                Repeater::make('answers')
                                    ->label('Answer Choices')
                                    ->relationship()
                                    ->orderColumn(
                                        'sort_order'
                                    )
                                    ->minItems(2)
                                    ->defaultItems(2)
                                    ->addActionLabel(
                                        'Add Answer'
                                    )
                                    ->reorderable()
                                    ->collapsible()
                                    ->cloneable()
                                    ->itemLabel(
                                        fn (
                                            array $state
                                        ): ?string =>
                                            filled(
                                                $state['answer']
                                                    ?? null
                                            )
                                                ? $state['answer']
                                                : 'New Answer'
                                    )
                                    ->rules([
                                        function (): Closure {
                                            return function (
                                                string $attribute,
                                                mixed $value,
                                                Closure $fail
                                            ): void {
                                                if (! is_array($value)) {
                                                    return;
                                                }

                                                $correctCount = collect($value)
                                                    ->filter(
                                                        fn (array $answer): bool =>
                                                            (bool) (
                                                                $answer['is_correct']
                                                                ?? false
                                                            )
                                                    )
                                                    ->count();

                                                if ($correctCount < 1) {
                                                    $fail(
                                                        'Each question must have at least one correct answer.'
                                                    );
                                                }
                                            };
                                        },
                                    ])
                                    ->schema([
                                        TextInput::make(
                                            'answer'
                                        )
                                            ->label(
                                                'Answer'
                                            )
                                            ->required()
                                            ->maxLength(
                                                1000
                                            ),

                                        Toggle::make(
                                            'is_correct'
                                        )
                                            ->label(
                                                'Correct answer'
                                            )
                                            ->helperText(
                                                'Select every answer that should be accepted as correct.'
                                            )
                                            ->default(
                                                false
                                            ),
                                    ])
                                    ->columns([
                                        'default' => 1,
                                        'md' => 2,
                                    ])
                                    ->columnSpanFull(),
                            ])
                            ->columns([
                                'default' => 1,
                                'md' => 2,
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}