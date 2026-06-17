<div style="margin:0; padding:0; background:#f1f5f9;">
  <div style="max-width:720px; margin:0 auto; padding:28px 16px; font-family:Arial, Helvetica, sans-serif; color:#0f172a;">

    {{-- Header --}}
    <div style="background:#0ea5e9; border-radius:16px 16px 0 0; padding:22px;">
      <div style="font-size:14px; letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.9); font-weight:700;">
        Gimscare • Website
      </div>
      <div style="margin-top:10px; font-size:22px; font-weight:800; color:#ffffff;">
        Message received ✅
      </div>
    </div>

    {{-- Body --}}
    <div style="background:#ffffff; border-radius:0 0 16px 16px; padding:22px; box-shadow:0 18px 50px rgba(15,23,42,.10);">

      <p style="color:#475569;">
        Hi <strong>{{ $name }}</strong>,
      </p>

      <p style="color:#475569;">
        Thank you for contacting Gimscare. We’ve received your message and a member of our team will respond shortly.
      </p>

      <div style="margin-top:16px; border:1px solid #e2e8f0; border-radius:12px; padding:14px; background:#f8fafc;">
        <div style="font-weight:700; margin-bottom:6px;">Your message:</div>
        <div style="white-space:pre-line; color:#334155;">
          {{ $messageText }}
        </div>
      </div>

      <p style="margin-top:16px; font-size:12px; color:#64748b;">
        This is an automated confirmation email. Please do not reply directly to this message.
      </p>

    </div>

    <div style="margin-top:14px; font-size:12px; color:#64748b;">
      This email was generated automatically from the Gimscare website.
    </div>

  </div>
</div>