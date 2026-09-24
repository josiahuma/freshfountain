<?php

namespace App\Filament\Resources\SmsLogs\Tables;

use App\Models\SmsLog;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SmsLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date / Time')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                TextColumn::make('member.display_name')
                    ->label('Member')
                    ->state(fn (SmsLog $record): string => $record->member?->display_name ?: 'Member unavailable')
                    ->description(fn (SmsLog $record): string => $record->recipient)
                    ->searchable([
                        'recipient',
                    ])
                    ->wrap(),

                TextColumn::make('template.name')
                    ->label('Template')
                    ->placeholder('No template')
                    ->badge()
                    ->sortable(),

                TextColumn::make('message')
                    ->label('Message')
                    ->limit(90)
                    ->tooltip(fn (SmsLog $record): string => $record->message)
                    ->wrap()
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'sent' => 'success',
                        'failed' => 'danger',
                        'skipped' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => ucfirst((string) $state))
                    ->sortable(),

                TextColumn::make('sender.name')
                    ->label('Sent By')
                    ->placeholder('System / unavailable')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('sent_at')
                    ->label('Sent At')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('provider_transaction_id')
                    ->label('Provider Transaction')
                    ->placeholder('—')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('provider_request_id')
                    ->label('Provider Request')
                    ->placeholder('—')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('error_message')
                    ->label('Failure / Provider Error')
                    ->placeholder('—')
                    ->limit(80)
                    ->tooltip(fn (SmsLog $record): ?string => $record->error_message)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'sent' => 'Sent',
                        'failed' => 'Failed',
                        'skipped' => 'Skipped',
                    ]),

                SelectFilter::make('sms_template_id')
                    ->label('Template')
                    ->relationship('template', 'name')
                    ->searchable()
                    ->preload(),

                Filter::make('date_range')
                    ->label('Date range')
                    ->form([
                        DatePicker::make('from')
                            ->label('From'),
                        DatePicker::make('until')
                            ->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date)
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date)
                            );
                    }),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
