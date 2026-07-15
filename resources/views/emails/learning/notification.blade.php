<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>{{ $heading }}</title>
</head>

<body style="margin:0; padding:0; background:#f1f5f9; color:#0f172a; font-family:Arial, Helvetica, sans-serif;">
<table
    role="presentation"
    width="100%"
    cellpadding="0"
    cellspacing="0"
    style="background:#f1f5f9;"
>
    <tr>
        <td align="center" style="padding:32px 16px;">
            <table
                role="presentation"
                width="100%"
                cellpadding="0"
                cellspacing="0"
                style="max-width:640px; overflow:hidden; border:1px solid #e2e8f0; border-radius:24px; background:#ffffff;"
            >
                <tr>
                    <td style="padding:36px 32px; background:#020617; color:#ffffff;">
                        <p style="margin:0; color:#93c5fd; font-size:12px; font-weight:800; letter-spacing:2px; text-transform:uppercase;">
                            {{ $eyebrow }}
                        </p>

                        <h1 style="margin:14px 0 0; font-size:30px; line-height:1.2;">
                            {{ $heading }}
                        </h1>
                    </td>
                </tr>

                <tr>
                    <td style="padding:32px;">
                        <p style="margin:0; font-size:16px; line-height:1.7;">
                            Hello {{ \Illuminate\Support\Str::before($recipientName, ' ') }},
                        </p>

                        <p style="margin:18px 0 0; color:#475569; font-size:16px; line-height:1.7;">
                            {{ $intro }}
                        </p>

                        @if(count($details))
                            <table
                                role="presentation"
                                width="100%"
                                cellpadding="0"
                                cellspacing="0"
                                style="margin-top:26px; border:1px solid #e2e8f0; border-radius:16px; background:#f8fafc;"
                            >
                                @foreach($details as $label => $value)
                                    <tr>
                                        <td style="padding:13px 16px; border-bottom:1px solid #e2e8f0; color:#64748b; font-size:13px; font-weight:700;">
                                            {{ $label }}
                                        </td>

                                        <td
                                            align="right"
                                            style="padding:13px 16px; border-bottom:1px solid #e2e8f0; color:#0f172a; font-size:13px; font-weight:800;"
                                        >
                                            {{ $value }}
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        @endif

                        <table
                            role="presentation"
                            cellpadding="0"
                            cellspacing="0"
                            style="margin-top:28px;"
                        >
                            <tr>
                                <td
                                    align="center"
                                    style="border-radius:12px; background:#0756b5;"
                                >
                                    <a
                                        href="{{ $actionUrl }}"
                                        style="display:inline-block; padding:14px 24px; color:#ffffff; font-size:15px; font-weight:800; text-decoration:none;"
                                    >
                                        {{ $actionLabel }} →
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <p style="margin:26px 0 0; color:#475569; font-size:14px; line-height:1.7;">
                            {{ $closing }}
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:22px 32px; background:#f8fafc; color:#64748b; font-size:12px; line-height:1.6;">
                        Fresh Fountain Christian Network · Fresh Learning
                        <br>
                        This message was sent automatically following activity in your learning account.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>