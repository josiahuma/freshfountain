<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>{{ $certificate->certificate_number }}</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 6mm 9mm;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            width: auto;
            height: auto;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        body {
            color: #071936;
            background: #ffffff;
            font-family: DejaVu Sans, sans-serif;
        }

        /*
        |--------------------------------------------------------------------------
        | Entire certificate
        |--------------------------------------------------------------------------
        |
        | No fixed page height and no top-level absolute positioning.
        | This prevents Dompdf from separating the frame and content.
        |
        */

        .certificate {
            position: relative;

            /*
            * Exact printable A4 landscape footprint.
            * Do not change these back to percentages.
            */
            width: 279mm;
            height: 197mm;
            min-height: 197mm;
            max-height: 197mm;

            margin: 5mm auto 0;
            padding: 0;

            overflow: hidden;
            background: #ffffff;
            border: 5px solid #b88713;

            page-break-inside: avoid;
            page-break-before: avoid;
            page-break-after: avoid;
        }

        .certificate-middle {
            position: absolute;
            top: 2px;
            right: 2px;
            bottom: 2px;
            left: 2px;

            margin: 0;
            padding: 0;

            overflow: hidden;
            border: 1.5px solid #e0c56f;
        }

        .certificate-inner {
            position: absolute;
            top: 3px;
            right: 3px;
            bottom: 3px;
            left: 3px;

            margin: 0;
            padding: 12mm 10mm 5mm;

            overflow: hidden;
            border: 1px solid #b88713;
            text-align: center;
        }

        /*
        |--------------------------------------------------------------------------
        | Decorative corners
        |--------------------------------------------------------------------------
        */

        .corner-top-left {
            position: absolute;
            top: 0;
            left: 0;
            width: 28mm;
            height: 28mm;
            background: #071936;
        }

        .corner-top-left-angle {
            position: absolute;
            top: 0;
            left: 28mm;
            width: 0;
            height: 0;
            border-top: 28mm solid #071936;
            border-right: 20mm solid transparent;
        }

        .corner-bottom-right {
            position: absolute;
            right: 0;
            bottom: 0;
            width: 28mm;
            height: 28mm;
            background: #071936;
        }

        .corner-bottom-right-angle {
            position: absolute;
            right: 28mm;
            bottom: 0;
            width: 0;
            height: 0;
            border-bottom: 28mm solid #071936;
            border-left: 20mm solid transparent;
        }

        .gold-top-line {
            position: absolute;
            top: 8mm;
            left: 8mm;
            width: 30mm;
            border-top: 2px solid #b88713;
        }

        .gold-left-line {
            position: absolute;
            top: 8mm;
            left: 8mm;
            height: 29mm;
            border-left: 2px solid #b88713;
        }

        .gold-bottom-line {
            position: absolute;
            right: 8mm;
            bottom: 8mm;
            width: 30mm;
            border-top: 2px solid #b88713;
        }

        .gold-right-line {
            position: absolute;
            right: 8mm;
            bottom: 8mm;
            height: 29mm;
            border-left: 2px solid #b88713;
        }

        /*
        |--------------------------------------------------------------------------
        | Header
        |--------------------------------------------------------------------------
        */

        .logo-row {
            height: 12mm;
            margin-bottom: 1mm;
        }

        .logo {
            display: inline-block;
            width: auto;
            max-width: 34mm;
            height: 11mm;
        }

        .organisation {
            margin-top: 1mm;
            color: #071936;
            font-size: 12pt;
            font-weight: bold;
            letter-spacing: 2.4px;
            text-transform: uppercase;
        }

        .certificate-title {
            margin-top: 5mm;
            color: #b88713;
            font-family: DejaVu Serif, serif;
            font-size: 28pt;
            font-weight: bold;
            line-height: 1;
            letter-spacing: 2.5px;
            text-transform: uppercase;
        }

        .presented-text {
            margin-top: 6mm;
            color: #4b5563;
            font-size: 10pt;
        }

        .student-name {
            display: inline-block;
            min-width: 145mm;
            max-width: 195mm;
            margin-top: 3mm;
            padding: 0 8mm 2.5mm;
            border-bottom: 1px solid #b88713;
            color: #071936;
            font-family: DejaVu Serif, serif;
            font-size: 29pt;
            font-weight: bold;
            line-height: 1.1;
        }

        .completion-text {
            margin-top: 4mm;
            color: #4b5563;
            font-size: 10pt;
        }

        .course-title {
            margin-top: 3mm;
            color: #0756b5;
            font-family: DejaVu Serif, serif;
            font-size: 22pt;
            font-weight: bold;
            line-height: 1.15;
        }

        .recognition-text {
            margin-top: 4mm;
            color: #4b5563;
            font-size: 8.5pt;
        }

        /*
        |--------------------------------------------------------------------------
        | Bottom details
        |--------------------------------------------------------------------------
        */

        .details-table {
            width: 80%;
            margin: 7mm auto 0;
            table-layout: fixed;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        .details-table td {
            width: 33.333%;
            padding: 0 7mm;
            vertical-align: bottom;
            text-align: center;
        }

        .details-table td + td {
            border-left: 1px solid #b88713;
        }

        .detail-value {
            color: #071936;
            font-family: DejaVu Serif, serif;
            font-size: 10pt;
            font-weight: bold;
            line-height: 1.25;
        }

        .detail-label {
            margin-top: 1.5mm;
            color: #697386;
            font-size: 6.8pt;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .signature-line {
            width: 48mm;
            margin: 0 auto 1.5mm;
            border-top: 1px solid #071936;
        }

        .qr {
            display: block;
            width: 19mm;
            height: 19mm;
            margin: 0 auto 1mm;
        }

        .certificate-number {
            white-space: nowrap;
        }

        .verification {
            margin: 3mm auto 0;

            color: #697386;
            font-size: 6.5pt;
            line-height: 1.45;
            text-align: center;
        }

        .verification-code {
            white-space: nowrap;
        }

        .footer {
            position: absolute;
            right: 45mm;
            bottom: 3mm;
            left: 45mm;

            margin: 0;
            padding: 0 5mm;

            color: #b88713;
            background: #ffffff;

            font-size: 7.5pt;
            font-weight: bold;
            line-height: 1.2;
            text-align: center;
        }
    </style>
</head>

<body>

<div class="certificate">
    <div class="certificate-middle">
        <div class="certificate-inner">

            {{-- Decorative elements --}}
            <div class="corner-top-left"></div>
            <div class="corner-top-left-angle"></div>

            <div class="corner-bottom-right"></div>
            <div class="corner-bottom-right-angle"></div>

            <div class="gold-top-line"></div>
            <div class="gold-left-line"></div>
            <div class="gold-bottom-line"></div>
            <div class="gold-right-line"></div>

            {{-- Main certificate --}}
            <div class="logo-row">
                @if($logoDataUri)
                    <img
                        src="{{ $logoDataUri }}"
                        alt="Fresh Fountain Christian Network"
                        class="logo"
                    >
                @endif
            </div>

            <div class="organisation">
                Fresh Fountain Christian Network
            </div>

            <div class="certificate-title">
                Certificate of Completion
            </div>

            <div class="presented-text">
                This certificate is proudly presented to
            </div>

            <div class="student-name">
                {{ $certificate->student_name }}
            </div>

            <div class="completion-text">
                for successfully completing all required lessons and assessments in
            </div>

            <div class="course-title">
                {{ $certificate->course_title }}
            </div>

            <div class="recognition-text">
                Awarded in recognition of commitment, participation and successful course completion.
            </div>

            <table class="details-table">
                <tr>
                    <td>
                        <div class="detail-value">
                            {{ $certificate->issued_at->format('j F Y') }}
                        </div>

                        <div class="detail-label">
                            Date issued
                        </div>
                    </td>

                    <td>
                        <div class="signature-line"></div>

                        <div class="detail-value">
                            Course Administrator
                        </div>

                        <div class="detail-label">
                            Authorised signature
                        </div>
                    </td>

                    <td>
                        <img
                            src="{{ $qrCodeDataUri }}"
                            alt="Certificate verification QR code"
                            class="qr"
                        >

                        <div class="detail-value certificate-number">
                            {{ $certificate->certificate_number }}
                        </div>

                        <div class="detail-label">
                            Certificate number
                        </div>
                    </td>
                </tr>
            </table>

            <div class="verification">
                Scan the QR code to verify this certificate.<br>

                <span class="verification-code">
                    Verification code:
                    {{ $certificate->verification_code }}
                </span>
            </div>

            <div class="footer">
                Fresh Fountain Christian Network · Fresh Learning
            </div>
        </div>
    </div>
</div>

</body>
</html>