<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Unit membership approved
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
                "
            >
                <tr>
                    <td style="
                        padding: 32px;
                        font-size: 16px;
                        line-height: 1.7;
                    ">
                        <h1 style="
                            margin-top: 0;
                            color: #15803d;
                            font-size: 25px;
                        ">
                            Your request has been approved
                        </h1>

                        <p>
                            Dear {{ $request->first_name }},
                        </p>

                        <p>
                            We are pleased to confirm that your
                            request to join
                            <strong>
                                {{ $request->churchUnit->name }}
                            </strong>
                            has been approved.
                        </p>

                        @if ($request->assignedLeader)
                            <p>
                                Your assigned unit leader is
                                <strong>
                                    {{ $request->assignedLeader->display_name }}
                                </strong>.
                            </p>
                        @endif

                        @if (filled($request->churchUnit->meeting_day))
                            <p>
                                <strong>
                                    Meeting:
                                </strong>

                                {{ $request->churchUnit->meeting_day }}

                                @if (filled($request->churchUnit->meeting_time))
                                    at
                                    {{ $request->churchUnit->meeting_time }}
                                @endif
                            </p>
                        @endif

                        @if (filled($request->churchUnit->meeting_location))
                            <p>
                                <strong>
                                    Location:
                                </strong>

                                {{ $request->churchUnit->meeting_location }}
                            </p>
                        @endif

                        <p>
                            We look forward to serving alongside you.
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