<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>
        Fresh Fountain Events List – {{ $monthLabel }}
    </title>

    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #edf3f8;
            color: #10213d;
            font-family: DejaVu Sans, Arial, sans-serif;
        }

        body.pdf-mode {
            background: #ffffff;
        }

        .screen-toolbar {
            max-width: 780px;
            margin: 18px auto;
            padding: 14px;
            border: 1px solid #d7e0ea;
            border-radius: 14px;
            background: #ffffff;
            box-shadow: 0 8px 24px rgba(15, 33, 61, 0.08);
        }

        .toolbar-table {
            width: 100%;
            border-collapse: collapse;
        }

        .toolbar-table td {
            vertical-align: middle;
        }

        .toolbar-right {
            text-align: right;
        }

        .button {
            display: inline-block;
            margin: 3px;
            padding: 10px 13px;
            border: 0;
            border-radius: 9px;
            background: #0756b9;
            color: #ffffff;
            font-size: 12px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
        }

        .button.secondary {
            border: 1px solid #cbd6e2;
            background: #ffffff;
            color: #16345d;
        }

        .list-sheet {
            width: calc(100% - 32px);
            max-width: 780px;
            margin: 16px auto 28px;
            padding: 22px;
            border: 1px solid #d7e0ea;
            border-radius: 20px;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(15, 33, 61, 0.08);
        }

        .pdf-mode .list-sheet {
            width: 94%;
            max-width: none;
            margin: 0 auto;
            padding: 0;
            border: 0;
            border-radius: 0;
            box-shadow: none;
        }

        .brand-line {
            height: 6px;
            margin-bottom: 16px;
            border-radius: 4px;
            background: #1558bb;
        }

        .header-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .logo-cell {
            width: 155px;
            vertical-align: middle;
        }

        .logo {
            max-width: 140px;
            max-height: 58px;
        }

        .brand-fallback {
            color: #10213d;
            font-size: 16px;
            font-weight: bold;
            line-height: 1.3;
        }

        .title-cell {
            text-align: right;
            vertical-align: middle;
        }

        .calendar-title {
            margin: 0;
            color: #0d4faa;
            font-size: 30px;
            font-weight: bold;
            line-height: 1.1;
        }

        .calendar-subtitle {
            margin-top: 5px;
            color: #64748b;
            font-size: 10px;
            letter-spacing: 1.3px;
            text-transform: uppercase;
        }

        .summary {
            margin-bottom: 18px;
            padding: 12px 15px;
            border-radius: 10px;
            background: #edf6ff;
            color: #0d4faa;
            font-size: 11px;
            font-weight: bold;
        }

        .event-card {
            width: 100%;
            margin-bottom: 13px;
            border: 1px solid #d8e2ed;
            border-left: 8px solid #1d4ed8;
            border-radius: 13px;
            border-collapse: separate;
            background: #ffffff;
            page-break-inside: avoid;
        }

        .event-card td {
            padding: 17px;
            vertical-align: middle;
        }

        .date-column {
            width: 88px;
            border-right: 1px solid #e3eaf1;
            text-align: center;
        }

        .date-day {
            color: #0d4faa;
            font-size: 34px;
            font-weight: bold;
            line-height: 1;
        }

        .date-month {
            margin-top: 5px;
            color: #64748b;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 1.3px;
        }

        .event-title {
            margin: 0;
            color: #10213d;
            font-size: 20px;
            font-weight: bold;
            line-height: 1.25;
        }

        .event-date-time {
            margin-top: 10px;
            color: #0d4faa;
            font-size: 13px;
            font-weight: bold;
            line-height: 1.45;
        }

        .event-location {
            margin-top: 7px;
            color: #40556f;
            font-size: 11px;
            font-weight: bold;
            line-height: 1.4;
        }

        .empty-state {
            padding: 35px 20px;
            border: 1px dashed #cbd6e2;
            border-radius: 14px;
            background: #f8fafc;
            color: #64748b;
            font-size: 13px;
            text-align: center;
        }

        .footer {
            margin-top: 16px;
            padding-top: 11px;
            border-top: 1px solid #dde5ee;
            color: #68798d;
            font-size: 8px;
            text-align: center;
        }

        @media print {
            html,
            body {
                background: #ffffff;
            }

            .screen-toolbar {
                display: none !important;
            }

            .list-sheet {
                width: 94%;
                max-width: none;
                margin: 0 auto;
                padding: 0;
                border: 0;
                box-shadow: none;
            }
        }
    </style>
</head>

<body class="{{ $isPdf ? 'pdf-mode' : '' }}">

@if (! $isPdf)
    <div class="screen-toolbar">
        <table class="toolbar-table">
            <tr>
                <td>
                    <a
                        href="{{ route('calendar.index') }}"
                        class="button secondary"
                    >
                        Back
                    </a>

                    <a
                        href="{{ route('calendar.print', [
                            'month' => $previousMonth,
                            'layout' => 'list',
                        ]) }}"
                        class="button secondary"
                    >
                        Previous
                    </a>

                    <a
                        href="{{ route('calendar.print', [
                            'month' => $nextMonth,
                            'layout' => 'list',
                        ]) }}"
                        class="button secondary"
                    >
                        Next
                    </a>
                </td>

                <td class="toolbar-right">
                    <button
                        type="button"
                        class="button secondary"
                        onclick="window.print()"
                    >
                        Print
                    </button>

                    <a
                        href="{{ route('calendar.pdf', [
                            'month' => $month->format('Y-m'),
                            'layout' => 'list',
                        ]) }}"
                        class="button"
                    >
                        Download PDF
                    </a>
                </td>
            </tr>
        </table>
    </div>
@endif

<div class="list-sheet">
    <div class="brand-line"></div>

    <table class="header-table">
        <tr>
            <td class="logo-cell">
                @if ($logoDataUri)
                    <img
                        src="{{ $logoDataUri }}"
                        alt="Fresh Fountain Christian Network"
                        class="logo"
                    >
                @else
                    <div class="brand-fallback">
                        Fresh Fountain<br>
                        Christian Network
                    </div>
                @endif
            </td>

            <td class="title-cell">
                <h1 class="calendar-title">
                    {{ $monthLabel }}
                </h1>

                <div class="calendar-subtitle">
                    Church Events List
                </div>
            </td>
        </tr>
    </table>

    <div class="summary">
        {{ $eventCount }}
        {{ \Illuminate\Support\Str::plural('event', $eventCount) }}
        scheduled for {{ $monthLabel }}
    </div>

    @forelse ($events as $event)
        <table
            class="event-card"
            style="border-left-color: {{ $event['colour'] }};"
        >
            <tr>
                <td class="date-column">
                    <div class="date-day">
                        {{ $event['dayNumber'] }}
                    </div>

                    <div class="date-month">
                        {{ $event['monthShort'] }}
                    </div>
                </td>

                <td>
                    <h2 class="event-title">
                        {{ $event['title'] }}
                    </h2>

                    <div class="event-date-time">
                        {{ $event['dateLabel'] }}
                        <br>
                        {{ $event['timeLabel'] }}
                    </div>

                    @if (filled($event['location']))
                        <div class="event-location">
                            {{ $event['location'] }}
                        </div>
                    @endif
                </td>
            </tr>
        </table>
    @empty
        <div class="empty-state">
            There are currently no published events scheduled for
            {{ $monthLabel }}.
        </div>
    @endforelse

    <div class="footer">
        Fresh Fountain Christian Network · Church Events Calendar
    </div>
</div>

</body>
</html>