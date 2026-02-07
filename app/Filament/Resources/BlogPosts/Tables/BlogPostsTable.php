<?php

namespace App\Filament\Resources\BlogPosts\Tables;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class BlogPostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('featured_image')
                    ->label('')
                    ->disk('public')
                    ->circular()
                    ->size(42),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(60),

                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('published_at')
                    ->label('Publish date')
                    ->dateTime('M j, Y H:i')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_published')
                    ->label('Published')
                    ->placeholder('All')
                    ->trueLabel('Published only')
                    ->falseLabel('Drafts only'),
            ])
            ->defaultSort('published_at', 'desc')

            /**
             * ✅ No actions classes (your Filament build doesn't have them)
             * Clicking a row should go to edit page.
             */
            ->recordUrl(fn ($record) => static::getEditUrl($record));
    }

    /**
     * Build the edit URL without using Action classes.
     */
    protected static function getEditUrl($record): string
    {
        // This matches your resource route: /admin/blog-posts/{record}/edit
        return url("/admin/blog-posts/{$record->getKey()}/edit");
    }
}
