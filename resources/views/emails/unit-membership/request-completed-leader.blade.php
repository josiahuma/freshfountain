<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Unit request completed
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
                            font-size: 24px;
                        ">
                            Unit request completed
                        </h1>

                        <p>
                            Hello {{ $recipientName }},
                        </p>

                        <p>
                            {{ $request->display_name }}’s onboarding
                            request for
                            <strong>
                                {{ $request->churchUnit->name }}
                            </strong>
                            is now complete.
                        </p>

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
                                View completed request
                            </a>
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>