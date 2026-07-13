<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>
        Fresh Fountain Calendar – {{ $monthLabel }}
    </title>

    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #eef3f8;
            color: #10213d;
            font-family: DejaVu Sans, Arial, sans-serif;
        }

        body.pdf-mode {
            background: #ffffff;
        }

        .screen-toolbar {
            max-width: 1120px;
            margin: 18px auto;
            padding: 14px 18px;
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
            margin-left: 6px;
            padding: 10px 14px;
            border: 0;
            border-radius: 9px;
            background: #0756b9;
            color: #ffffff;
            font-size: 13px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
        }

        .button.secondary {
            border: 1px solid #cbd6e2;
            background: #ffffff;
            color: #16345d;
        }

        .calendar-sheet {
            width: 100%;
            max-width: 1120px;
            margin: 0 auto 24px;
            padding: 18px;
            border: 1px solid #d7e0ea;
            border-radius: 18px;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(15, 33, 61, 0.08);
        }

        .pdf-mode .calendar-sheet {
            max-width: none;
            margin: 0;
            padding: 0;
            border: 0;
            border-radius: 0;
            box-shadow: none;
        }

        .brand-line {
            height: 6px;
            margin-bottom: 13px;
            border-radius: 4px;
            background: linear-gradient(
                90deg,
                #0d9fd3,
                #1558bb,
                #6743df
            );
        }

        .header-table {
            width: 100%;
            margin-bottom: 13px;
            border-collapse: collapse;
        }

        .logo-cell {
            width: 190px;
            vertical-align: middle;
        }

        .logo {
            max-width: 160px;
            max-height: 62px;
        }

        .brand-fallback {
            color: #10213d;
            font-size: 18px;
            font-weight: bold;
            line-height: 1.2;
        }

        .title-cell {
            text-align: center;
            vertical-align: middle;
        }

        .calendar-title {
            margin: 0;
            color: #0d4faa;
            font-size: 31px;
            font-weight: bold;
            line-height: 1.1;
        }

        .calendar-subtitle {
            margin-top: 5px;
            color: #65758b;
            font-size: 10px;
            letter-spacing: 1.4px;
            text-transform: uppercase;
        }

        .count-cell {
            width: 190px;
            text-align: right;
            vertical-align: middle;
        }

        .event-count {
            display: inline-block;
            padding: 7px 10px;
            border: 1px solid #cfe0f3;
            border-radius: 8px;
            background: #edf6ff;
            color: #0d4faa;
            font-size: 10px;
            font-weight: bold;
        }

        .month-grid {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }

        .month-grid th {
            height: 27px;
            border: 1px solid #c9d6e6;
            background: #123b78;
            color: #ffffff;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 0.3px;
            text-align: center;
            text-transform: uppercase;
        }

        .month-grid td {
            height: 85px;
            padding: 5px;
            border: 1px solid #c9d6e6;
            vertical-align: top;
            background: #ffffff;
        }

        .month-grid td.outside-month {
            background: #f3f6f9;
            color: #a5afbc;
        }

        .month-grid td.today {
            background: #edf6ff;
        }

        .day-number {
            margin-bottom: 4px;
            color: #183150;
            font-size: 11px;
            font-weight: bold;
            text-align: right;
        }

        .outside-month .day-number {
            color: #aeb7c2;
        }

        .event {
            margin-bottom: 3px;
            padding: 4px 5px;
            border-left: 4px solid #1d4ed8;
            border-radius: 4px;
            background: #eef5ff;
            page-break-inside: avoid;
        }

        .event-time {
            margin-bottom: 1px;
            color: #4a5d73;
            font-size: 7.5px;
            font-weight: bold;
            line-height: 1.2;
        }

        .event-title {
            color: #132a47;
            font-size: 8px;
            font-weight: bold;
            line-height: 1.2;
            overflow-wrap: break-word;
        }

        .event-location {
            margin-top: 1px;
            color: #64748b;
            font-size: 6.5px;
            line-height: 1.15;
        }

        .more-events {
            margin-top: 3px;
            color: #53657b;
            font-size: 7px;
            font-weight: bold;
        }

        .footer-table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }

        .footer-table td {
            color: #66778d;
            font-size: 8px;
            vertical-align: middle;
        }

        .footer-right {
            text-align: right;
        }

        .legend-item {
            display: inline-block;
            margin-right: 9px;
            white-space: nowrap;
        }

        .legend-dot {
            display: inline-block;
            width: 7px;
            height: 7px;
            margin-right: 3px;
            border-radius: 50%;
            vertical-align: middle;
        }

        @media print {
            html,
            body {
                background: #ffffff;
            }

            .screen-toolbar {
                display: none !important;
            }

            .calendar-sheet {
                max-width: none;
                margin: 0;
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
                        Back to calendar
                    </a>

                    <a
                        href="{{ route('calendar.print', [
                            'month' => $previousMonth,
                        ]) }}"
                        class="button secondary"
                    >
                        Previous month
                    </a>

                    <a
                        href="{{ route('calendar.print', [
                            'month' => $nextMonth,
                        ]) }}"
                        class="button secondary"
                    >
                        Next month
                    </a>
                </td>

                <td class="toolbar-right">
                    <button
                        type="button"
                        class="button secondary"
                        onclick="window.print()"
                    >
                        Print / Save PDF
                    </button>

                    <a
                        href="{{ route('calendar.pdf', [
                            'month' => $month->format('Y-m'),
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

<div class="calendar-sheet">
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
                    Church Events Calendar
                </div>
            </td>

            <td class="count-cell">
                <span class="event-count">
                    {{ $eventCount }}
                    {{ \Illuminate\Support\Str::plural(
                        'event',
                        $eventCount
                    ) }}
                    this month
                </span>
            </td>
        </tr>
    </table>

    <table class="month-grid">
        <thead>
            <tr>
                <th>Monday</th>
                <th>Tuesday</th>
                <th>Wednesday</th>
                <th>Thursday</th>
                <th>Friday</th>
                <th>Saturday</th>
                <th>Sunday</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($weeks as $week)
                <tr>
                    @foreach ($week as $day)
                        <td
                            class="
                                {{ ! $day['isCurrentMonth']
                                    ? 'outside-month'
                                    : '' }}

                                {{ $day['isToday']
                                    ? 'today'
                                    : '' }}
                            "
                        >
                            <div class="day-number">
                                {{ $day['dayNumber'] }}
                            </div>

                            @foreach (
                                $day['events']->take(3)
                                as $event
                            )
                                @php
                                    $details =
                                        $event['extendedProps'];

                                    $colour =
                                        $details['colour']
                                        ?? '#1d4ed8';
                                @endphp

                                <div
                                    class="event"
                                    style="
                                        border-left-color:
                                        {{ $colour }};
                                    "
                                >
                                    <div class="event-time">
                                        {{ $details['timeLabel'] }}
                                    </div>

                                    <div class="event-title">
                                        {{ $event['title'] }}
                                    </div>

                                    @if (
                                        filled(
                                            $details['location']
                                                ?? null
                                        )
                                    )
                                        <div class="event-location">
                                            {{ \Illuminate\Support\Str::limit(
                                                $details['location'],
                                                38
                                            ) }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach

                            @if ($day['events']->count() > 3)
                                <div class="more-events">
                                    +{{ $day['events']->count() - 3 }}
                                    more
                                </div>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="footer-table">
        <tr>
            <td>
                @foreach (
                    \App\Models\CalendarEvent::categoryOptions()
                    as $value => $label
                )
                    @php
                        $colour =
                            \App\Models\CalendarEvent::categoryColours()[
                                $value
                            ] ?? '#1d4ed8';
                    @endphp

                    <span class="legend-item">
                        <span
                            class="legend-dot"
                            style="background: {{ $colour }};"
                        ></span>

                        {{ $label }}
                    </span>
                @endforeach
            </td>

            <td class="footer-right">
                Fresh Fountain Christian Network
            </td>
        </tr>
    </table>
</div>

</body>
</html>