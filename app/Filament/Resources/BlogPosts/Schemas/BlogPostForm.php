<?php

namespace App\Filament\Resources\BlogPosts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BlogPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Post Details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set, $get) {
                                // Only auto-fill slug if user hasn't typed one
                                if (! $get('slug')) {
                                    $set('slug', Str::slug($state ?? ''));
                                }
                            }),

                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('Used in URL: /blog/{slug}'),

                        Toggle::make('is_published')
                            ->label('Published')
                            ->default(false)
                            ->live(),

                        DateTimePicker::make('published_at')
                            ->label('Publish date/time (optional)')
                            ->helperText('If empty, it can publish immediately when "Published" is on.')
                            ->seconds(false)
                            ->visible(fn ($get) => (bool) $get('is_published')),

                        FileUpload::make('featured_image')
                            ->label('Featured Image')
                            ->disk('public')
                            ->directory('blog')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            // handle UUID keyed arrays just in case
                            ->dehydrateStateUsing(fn ($state) => is_array($state) ? collect($state)->values()->first() : $state)
                            ->columnSpanFull(),

                        Textarea::make('excerpt')
                            ->label('Excerpt')
                            ->rows(3)
                            ->helperText('Shown in blog cards and previews.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Content')
                    ->schema([
                        RichEditor::make('content')
                            ->label('Post Content')
                            ->required()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'bulletList',
                                'orderedList',
                                'blockquote',
                                'link',
                                'h2',
                                'h3',
                                'undo',
                                'redo',
                            ])
                            ->columnSpanFull(),
                    ]),

                Section::make('SEO (optional)')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextInput::make('seo_title')
                            ->label('SEO Title')
                            ->helperText('If empty, we’ll use the post title.'),

                        Textarea::make('seo_description')
                            ->label('SEO Description')
                            ->rows(3)
                            ->helperText('Used in meta description.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
