<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        New unit membership request
    </title>
</head>

<body style="
    margin: 0;
    padding: 0;
    background: #f4f4f5;
    font-family: Arial, Helvetica, sans-serif;
    color: #18181b;
">
<table
    role="presentation"
    width="100%"
    cellspacing="0"
    cellpadding="0"
    style="
        width: 100%;
        background: #f4f4f5;
        padding: 30px 15px;
    "
>
    <tr>
        <td align="center">
            <table
                role="presentation"
                width="620"
                cellspacing="0"
                cellpadding="0"
                style="
                    width: 100%;
                    max-width: 620px;
                    background: #ffffff;
                    border-radius: 12px;
                    overflow: hidden;
                "
            >
                <tr>
                    <td style="
                        background: #111827;
                        color: #ffffff;
                        padding: 28px 32px;
                    ">
                        <h1 style="
                            margin: 0;
                            font-size: 24px;
                        ">
                            New unit membership request
                        </h1>
                    </td>
                </tr>

                <tr>
                    <td style="
                        padding: 32px;
                        font-size: 16px;
                        line-height: 1.7;
                    ">
                        <p style="margin-top: 0;">
                            Hello {{ $recipientName }},
                        </p>

                        <p>
                            A new request has been submitted to join
                            <strong>
                                {{ $request->churchUnit->name }}
                            </strong>.
                        </p>

                        <table
                            role="presentation"
                            width="100%"
                            cellspacing="0"
                            cellpadding="0"
                            style="
                                margin: 24px 0;
                                background: #f9fafb;
                                border: 1px solid #e5e7eb;
                                border-radius: 8px;
                            "
                        >
                            <tr>
                                <td style="padding: 18px;">
                                    <strong>
                                        Applicant:
                                    </strong>

                                    {{ $request->display_name }}
                                    <br>

                                    <strong>
                                        Email:
                                    </strong>

                                    {{ $request->email ?: 'Not provided' }}
                                    <br>

                                    <strong>
                                        Mobile:
                                    </strong>

                                    {{ $request->mobile_number ?: 'Not provided' }}
                                    <br>

                                    <strong>
                                        Reference:
                                    </strong>

                                    {{ $request->submission_reference }}
                                    <br>

                                    <strong>
                                        Status:
                                    </strong>

                                    {{ ucfirst($request->status) }}
                                </td>
                            </tr>
                        </table>

                        @if (filled($request->message))
                            <p>
                                <strong>
                                    Applicant’s message:
                                </strong>
                            </p>

                            <p style="
                                background: #f9fafb;
                                border-left: 4px solid #f59e0b;
                                padding: 14px 18px;
                            ">
                                {{ $request->message }}
                            </p>
                        @endif

                        <p style="
                            margin: 28px 0;
                            text-align: center;
                        ">
                            <a
                                href="{{ $adminUrl }}"
                                style="
                                    display: inline-block;
                                    background: #f59e0b;
                                    color: #111827;
                                    text-decoration: none;
                                    font-weight: bold;
                                    padding: 13px 22px;
                                    border-radius: 7px;
                                "
                            >
                                View request in CRM
                            </a>
                        </p>

                        <p style="margin-bottom: 0;">
                            Fresh Fountain CRM
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>