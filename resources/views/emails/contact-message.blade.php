<div style="margin:0; padding:0; background:#f1f5f9;">
  <div style="max-width:720px; margin:0 auto; padding:28px 16px; font-family:Arial, Helvetica, sans-serif; color:#0f172a;">

    {{-- Header --}}
    <div style="background:#0ea5e9; border-radius:16px 16px 0 0; padding:22px 22px;">
      <div style="font-size:14px; letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.9); font-weight:700;">
        Gimscare • Website
      </div>
      <div style="margin-top:10px; font-size:22px; font-weight:800; color:#ffffff;">
        New contact form message
      </div>
    </div>

    {{-- Body --}}
    <div style="background:#ffffff; border-radius:0 0 16px 16px; padding:22px; box-shadow:0 18px 50px rgba(15,23,42,.10);">

      <div style="border:1px solid #e2e8f0; border-radius:14px; overflow:hidden;">
        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
          <tr>
            <td style="padding:12px; background:#f8fafc; font-weight:700;">Name</td>
            <td style="padding:12px;">{{ $name }}</td>
          </tr>
          <tr>
            <td style="padding:12px; background:#f8fafc; font-weight:700;">Email</td>
            <td style="padding:12px;">
              <a href="mailto:{{ $email }}" style="color:#0284c7; text-decoration:none; font-weight:700;">
                {{ $email }}
              </a>
            </td>
          </tr>
          <tr>
            <td style="padding:12px; background:#f8fafc; font-weight:700;">Phone</td>
            <td style="padding:12px;">{{ $phone ?? 'N/A' }}</td>
          </tr>
          <tr>
            <td style="padding:12px; background:#f8fafc; font-weight:700;">Submitted</td>
            <td style="padding:12px;">{{ $submittedAt }}</td>
          </tr>
          <tr>
            <td style="padding:12px; background:#f8fafc; font-weight:700;">IP Address</td>
            <td style="padding:12px;">{{ $ip }}</td>
          </tr>
        </table>
      </div>

      <div style="margin-top:18px;">
        <div style="font-weight:800; margin-bottom:6px;">Message</div>
        <div style="white-space:pre-line; color:#334155;">
          {{ $messageText }}
        </div>
      </div>

    </div>

    <div style="margin-top:14px; font-size:12px; color:#64748b;">
      This email was generated automatically from the Gimscare website contact form.
    </div>

  </div>
</div>