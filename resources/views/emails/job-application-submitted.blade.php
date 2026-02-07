@php
  $job = $application->jobListing;
@endphp

<p><strong>New job application received</strong></p>

<p>
  <strong>Job:</strong> {{ $job?->title }} <br>
  <strong>Applicant:</strong> {{ $application->full_name }} <br>
  <strong>Email:</strong> {{ $application->email }} <br>
  <strong>Phone:</strong> {{ $application->phone ?? 'N/A' }} <br>
  <strong>Submitted:</strong> {{ $application->submitted_at?->format('d M Y, H:i') }}
</p>

<p>
  Please login to the admin dashboard to review this application.
</p>
