<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Support\HtmlString;

final class BackendAccessInvitationNotification
{
    public function __construct(
        private readonly string $plainToken,
        private readonly bool $isResend = false,
    ) {
    }

    public function subject(): string
    {
        return $this->isResend
            ? 'Your Fresh Fountain backend access link'
            : 'You have been invited to Fresh Fountain Hub';
    }

    public function html(User $user): string
    {
        $url = route('backend-invitation.show', [
            'token' => $this->plainToken,
        ]);

        $name = e($user->name ?: 'there');
        $safeUrl = e($url);
        $intro = $this->isResend
            ? 'A new secure account setup link has been created for you.'
            : 'You have been granted access to the Fresh Fountain Church administration hub.';

        return (string) new HtmlString(<<<HTML
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$this->subject()}</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f5;font-family:Arial,Helvetica,sans-serif;color:#18181b;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f4f5;padding:32px 12px;">
<tr>
<td align="center">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:620px;background:#ffffff;border-radius:14px;overflow:hidden;box-shadow:0 8px 30px rgba(0,0,0,.08);">
<tr>
<td style="background:#1d4ed8;padding:24px 32px;color:#ffffff;">
    <div style="font-size:22px;font-weight:700;">Fresh Fountain Hub</div>
</td>
</tr>
<tr>
<td style="padding:32px;">
    <p style="margin:0 0 18px;font-size:17px;line-height:1.6;">Hello {$name},</p>
    <p style="margin:0 0 18px;font-size:16px;line-height:1.6;">{$intro}</p>
    <p style="margin:0 0 24px;font-size:16px;line-height:1.6;">Use the button below to choose your password and activate your backend account.</p>
    <p style="margin:0 0 26px;">
        <a href="{$safeUrl}" style="display:inline-block;background:#1d4ed8;color:#ffffff;text-decoration:none;font-weight:700;padding:14px 22px;border-radius:9px;">Set up my account</a>
    </p>
    <p style="margin:0 0 10px;font-size:14px;line-height:1.6;color:#52525b;">This secure link expires in 24 hours.</p>
    <p style="margin:0 0 10px;font-size:14px;line-height:1.6;color:#52525b;">If the button does not work, copy and paste this address into your browser:</p>
    <p style="margin:0 0 20px;font-size:13px;line-height:1.6;word-break:break-all;"><a href="{$safeUrl}" style="color:#1d4ed8;">{$safeUrl}</a></p>
    <p style="margin:0;font-size:14px;line-height:1.6;color:#71717a;">If you were not expecting this invitation, you can safely ignore this email.</p>
</td>
</tr>
</table>
</td>
</tr>
</table>
</body>
</html>
HTML);
    }
}
