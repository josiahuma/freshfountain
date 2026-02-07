<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use Barryvdh\DomPDF\Facade\Pdf;

class JobApplicationPdfController extends Controller
{
    public function show(JobApplication $jobApplication)
    {
        // Optional: ensure only authenticated users can export
        // abort_unless(auth()->check(), 403);

        $jobApplication->load(['jobListing']);

        $answers = $this->answersArray($jobApplication);

        $pdf = Pdf::loadView('admin.job-applications.pdf', [
            'app' => $jobApplication,
            'answers' => $answers,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('job-application-' . $jobApplication->id . '.pdf');
    }

    private function answersArray(JobApplication $record): array
    {
        $state = $record->answers ?? [];

        if (is_string($state)) {
            $decoded = json_decode($state, true);
            return is_array($decoded) ? $decoded : [];
        }

        return is_array($state) ? $state : [];
    }
}
