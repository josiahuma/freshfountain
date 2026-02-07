<?php

namespace App\Filament\Resources\BlogPosts\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BlogPostInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Overview')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('title')
                            ->label('Title'),

                        TextEntry::make('slug')
                            ->label('Slug'),

                        TextEntry::make('is_published')
                            ->label('Published')
                            ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No'),

                        TextEntry::make('published_at')
                            ->label('Publish date')
                            ->dateTime('M j, Y H:i')
                            ->placeholder('—'),

                        TextEntry::make('created_at')
                            ->label('Created')
                            ->dateTime('M j, Y H:i')
                            ->placeholder('—'),

                        TextEntry::make('updated_at')
                            ->label('Updated')
                            ->dateTime('M j, Y H:i')
                            ->placeholder('—'),
                    ]),

                Section::make('Preview')
                    ->schema([
                        TextEntry::make('excerpt')
                            ->label('Excerpt')
                            ->placeholder('—')
                            ->columnSpanFull(),

                        TextEntry::make('content')
                            ->label('Content')
                            ->html() // because your RichEditor stores HTML
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
