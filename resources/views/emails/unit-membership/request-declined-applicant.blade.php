<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Unit membership request update
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
                            font-size: 24px;
                        ">
                            Update on your unit request
                        </h1>

                        <p>
                            Dear {{ $request->first_name }},
                        </p>

                        <p>
                            Thank you for your interest in
                            <strong>
                                {{ $request->churchUnit->name }}
                            </strong>.
                        </p>

                        <p>
                            After reviewing your request, we are
                            unable to progress it at this time.
                            A member of the church team may contact
                            you if further information is required.
                        </p>

                        <p>
                            Reference:
                            <strong>
                                {{ $request->submission_reference }}
                            </strong>
                        </p>

                        <p>
                            We remain grateful for your desire to
                            serve and encourage you to stay connected
                            with the church.
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