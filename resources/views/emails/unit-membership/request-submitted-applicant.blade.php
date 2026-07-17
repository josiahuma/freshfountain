<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Unit membership request received
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
                            line-height: 1.3;
                        ">
                            Request received
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
                            Dear {{ $request->first_name }},
                        </p>

                        <p>
                            Thank you for expressing your interest
                            in joining
                            <strong>
                                {{ $request->churchUnit->name }}
                            </strong>.
                        </p>

                        <p>
                            Your request has been received and a
                            member of the unit leadership team will
                            contact you.
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
                                        Reference:
                                    </strong>

                                    {{ $request->submission_reference }}
                                    <br>

                                    <strong>
                                        Requested unit:
                                    </strong>

                                    {{ $request->churchUnit->name }}
                                    <br>

                                    <strong>
                                        Submitted:
                                    </strong>

                                    {{ $request->submitted_at?->format('d M Y, H:i') }}
                                </td>
                            </tr>
                        </table>

                        <p>
                            Please keep the reference above in case
                            you need to contact us about your
                            request.
                        </p>

                        <p style="margin-bottom: 0;">
                            God bless,<br>
                            <strong>
                                Fresh Fountain
                            </strong>
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>