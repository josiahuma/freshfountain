<?php

namespace App\Mail;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobApplicationSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public JobApplication $application)
    {
        $this->application->load('jobListing');
    }

    public function build()
    {
        $jobTitle = $this->application->jobListing?->title ?? 'Job';

        return $this->subject("New Job Application: {$jobTitle}")
            ->view('emails.job-application-submitted');
    }
}
