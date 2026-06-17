@php
  $job = $application->jobListing;

  $siteUrl = rtrim(config('app.url'), '/');
  $careersUrl = $siteUrl . '/careers';
  $submitted = $application->submitted_at?->format('d M Y, H:i') ?? '-';
  $phone = $application->phone ?: 'N/A';
@endphp

<div style="margin:0; padding:0; background:#f1f5f9;">
  <div style="max-width:720px; margin:0 auto; padding:28px 16px; font-family:Arial, Helvetica, sans-serif; color:#0f172a;">

    {{-- Header --}}
    <div style="background:#0ea5e9; border-radius:16px 16px 0 0; padding:22px 22px;">
      <div style="font-size:14px; letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.9); font-weight:700;">
        Gimscare • Careers
      </div>
      <div style="margin-top:10px; font-size:22px; line-height:1.25; font-weight:800; color:#ffffff;">
        Application received ✅
      </div>
    </div>

    {{-- Body --}}
    <div style="background:#ffffff; border-radius:0 0 16px 16px; padding:22px; box-shadow:0 18px 50px rgba(15,23,42,.10);">

      <div style="font-size:14px; color:#475569; margin-bottom:16px;">
        Hi <strong style="color:#0f172a;">{{ $application->full_name }}</strong>,<br><br>
        Thank you for applying for the <strong style="color:#0f172a;">{{ $job->title }}</strong> at Gimscare.
        We’ve received your application and our team will review it shortly.
      </div>

      <div style="border:1px solid #e2e8f0; border-radius:14px; overflow:hidden;">
        <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;">
          <tr>
            <td style="padding:12px 14px; background:#f8fafc; width:180px; font-weight:700; color:#0f172a;">Job</td>
            <td style="padding:12px 14px; background:#ffffff; color:#0f172a;">{{ $job?->title ?? '-' }}</td>
          </tr>
          <tr>
            <td style="padding:12px 14px; background:#f8fafc; font-weight:700; color:#0f172a;">Applicant</td>
            <td style="padding:12px 14px; background:#ffffff; color:#0f172a;">{{ $application->full_name }}</td>
          </tr>
          <tr>
            <td style="padding:12px 14px; background:#f8fafc; font-weight:700; color:#0f172a;">Email</td>
            <td style="padding:12px 14px; background:#ffffff;">
              <a href="mailto:{{ $application->email }}" style="color:#0284c7; font-weight:700; text-decoration:none;">
                {{ $application->email }}
              </a>
            </td>
          </tr>
          <tr>
            <td style="padding:12px 14px; background:#f8fafc; font-weight:700; color:#0f172a;">Phone</td>
            <td style="padding:12px 14px; background:#ffffff; color:#0f172a;">{{ $phone }}</td>
          </tr>
          <tr>
            <td style="padding:12px 14px; background:#f8fafc; font-weight:700; color:#0f172a;">Submitted</td>
            <td style="padding:12px 14px; background:#ffffff; color:#0f172a;">{{ $submitted }}</td>
          </tr>
        </table>
      </div>

      {{-- Next steps --}}
      <div style="margin-top:16px; font-size:14px; color:#475569;">
        <div style="font-weight:800; color:#0f172a; margin-bottom:6px;">What happens next?</div>
        <ul style="margin:0; padding-left:18px;">
          <li>Our recruitment team will review your application.</li>
          <li>If shortlisted, we’ll contact you via email or phone.</li>
          <li>Please check your spam/junk folder just in case.</li>
        </ul>
      </div>

      {{-- Button --}}
      <div style="margin-top:20px; text-align:left;">
        <a href="{{ $careersUrl }}" target="_blank" rel="noopener noreferrer"
           style="display:inline-block; background:#0ea5e9; color:#ffffff; font-weight:800; text-decoration:none; padding:12px 16px; border-radius:12px;">
          View Careers Page →
        </a>
      </div>

      <div style="margin-top:12px; font-size:12px; color:#64748b;">
        If the button doesn’t work, copy and paste this link into your browser:<br>
        <a href="{{ $careersUrl }}" target="_blank" rel="noopener noreferrer" style="color:#0284c7; text-decoration:none;">
          {{ $careersUrl }}
        </a>
      </div>

      <div style="margin-top:16px; font-size:12px; color:#64748b;">
        This is an automated confirmation email. Please do not reply directly to this message.
      </div>

    </div>

    {{-- Footer --}}
    <div style="max-width:720px; margin:0 auto; padding:14px 4px; font-family:Arial, Helvetica, sans-serif; color:#64748b; font-size:12px;">
      This email was generated automatically from the Gimscare Careers portal.
    </div>

  </div>
</div>