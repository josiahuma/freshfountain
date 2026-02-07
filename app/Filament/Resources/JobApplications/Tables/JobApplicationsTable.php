<?php

namespace App\Filament\Resources\JobApplications\Tables;

use App\Filament\Resources\JobApplications\Schemas\JobApplicationForm;
use Filament\Actions\Action; // ✅ CHANGE: use Filament\Actions\Action (v4 safe)
use App\Filament\Resources\JobApplications\JobApplicationResource;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class JobApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('jobListing'))
            ->columns([
                TextColumn::make('jobListing.title')
                    ->label('Job')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('full_name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->toggleable(),

                SelectColumn::make('status')
                    ->label('Status')
                    ->options(JobApplicationForm::statusOptions())
                    ->sortable()
                    ->selectablePlaceholder(false),

                TextColumn::make('submitted_at')
                    ->label('Submitted')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->headerActions([
                Action::make('exportCsv')
                    ->label('Export CSV')
                    ->action(function () use ($table) {
                        // ✅ Export using the same base query (with filters/sorts is harder in v4 without extra hooks)
                        // This exports all applications; next step we can export filtered if you want.
                        $query = \App\Models\JobApplication::query()->with('jobListing');

                        $filename = 'job-applications-' . now()->format('Y-m-d_His') . '.csv';

                        return response()->streamDownload(function () use ($query) {
                            $out = fopen('php://output', 'w');

                            fputcsv($out, [
                                'Job',
                                'Full name',
                                'Email',
                                'Phone',
                                'Status',
                                'Submitted at',
                            ]);

                            $query->chunk(500, function ($rows) use ($out) {
                                foreach ($rows as $row) {
                                    fputcsv($out, [
                                        $row->job?->title,
                                        $row->full_name,
                                        $row->email,
                                        $row->phone,
                                        $row->status,
                                        optional($row->submitted_at)->format('Y-m-d H:i:s'),
                                    ]);
                                }
                            });

                            fclose($out);
                        }, $filename, [
                            'Content-Type' => 'text/csv; charset=UTF-8',
                        ]);
                    }),
            ])
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->url(fn ($record) => JobApplicationResource::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab(false),

                Action::make('edit')
                    ->label('Update')
                    ->url(fn ($record) => JobApplicationResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab(false),
            ])
            ->bulkActions([]);
    }
}
