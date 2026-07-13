@extends('layouts.site')

@section('content')

<section class="relative overflow-hidden bg-slate-950">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-950 via-slate-950 to-violet-950"></div>

    <div class="absolute -left-32 top-0 h-96 w-96 rounded-full bg-blue-500/20 blur-3xl"></div>

    <div class="absolute -right-32 bottom-0 h-96 w-96 rounded-full bg-violet-500/20 blur-3xl"></div>

    <div class="relative mx-auto max-w-[1400px] px-4 py-20 text-center sm:px-6 lg:px-8 lg:py-28">
        <p class="text-sm font-extrabold uppercase tracking-[0.24em] text-blue-300">
            What’s happening
        </p>

        <h1 class="mt-4 text-4xl font-extrabold tracking-tight text-white md:text-6xl">
            Church Calendar
        </h1>

        <p class="mx-auto mt-5 max-w-2xl text-lg leading-relaxed text-slate-300">
            Explore upcoming services, prayer meetings, conferences,
            outreach programmes and special gatherings at Fresh Fountain.
        </p>
    </div>
</section>

<section class="bg-slate-50 py-10 md:py-16">
    <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">

        {{-- Calendar actions --}}
        <div class="mb-7 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm md:p-7">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-950 md:text-2xl">
                        Find an event
                    </h2>

                    <p class="mt-1 text-sm leading-6 text-slate-600">
                        Browse upcoming Fresh Fountain programmes and download
                        a printable calendar for your family or church group.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a
                        id="calendar-print-grid-link"
                        href="{{ route('calendar.print', [
                            'month' => now()->format('Y-m'),
                            'layout' => 'grid',
                        ]) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-extrabold text-slate-800 transition hover:border-blue-500 hover:text-blue-700"
                    >
                        Print Grid
                    </a>

                    <a
                        id="calendar-print-list-link"
                        href="{{ route('calendar.print', [
                            'month' => now()->format('Y-m'),
                            'layout' => 'list',
                        ]) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-extrabold text-slate-800 transition hover:border-blue-500 hover:text-blue-700"
                    >
                        Print List
                    </a>

                    <a
                        id="calendar-pdf-grid-link"
                        href="{{ route('calendar.pdf', [
                            'month' => now()->format('Y-m'),
                            'layout' => 'grid',
                        ]) }}"
                        class="inline-flex items-center justify-center rounded-xl bg-blue-700 px-4 py-3 text-sm font-extrabold text-white transition hover:bg-blue-800"
                    >
                        Grid PDF
                    </a>

                    <a
                        id="calendar-pdf-list-link"
                        href="{{ route('calendar.pdf', [
                            'month' => now()->format('Y-m'),
                            'layout' => 'list',
                        ]) }}"
                        class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-3 text-sm font-extrabold text-white transition hover:bg-slate-700"
                    >
                        List PDF
                    </a>
                </div>
            </div>
        </div>

        {{-- Loading/error status --}}
        <div
            id="calendar-status"
            class="mb-5 hidden rounded-2xl border px-5 py-4 text-sm font-semibold"
            role="status"
        ></div>

        {{-- Main calendar --}}
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white p-3 shadow-sm md:p-7">
            <div id="church-calendar"></div>
        </div>
    </div>
</section>

{{-- Event details modal --}}
<div
    id="calendar-event-modal"
    class="fixed inset-0 z-[100] hidden items-center justify-center p-4"
    aria-hidden="true"
>
    <button
        type="button"
        id="calendar-modal-backdrop"
        class="absolute inset-0 bg-slate-950/75 backdrop-blur-sm"
        aria-label="Close event details"
    ></button>

    <div
        class="relative z-10 max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-3xl bg-white shadow-2xl"
        role="dialog"
        aria-modal="true"
        aria-labelledby="calendar-modal-title"
    >
        <div
            id="calendar-modal-colour"
            class="h-3 w-full bg-blue-700"
        ></div>

        <div
            id="calendar-modal-image-wrapper"
            class="hidden"
        >
            <img
                id="calendar-modal-image"
                src=""
                alt=""
                class="h-56 w-full object-cover md:h-72"
            >
        </div>

        <div class="p-6 md:p-9">
            <div class="flex items-start justify-between gap-5">
                <div>
                    <span
                        id="calendar-modal-category"
                        class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-extrabold uppercase tracking-wide text-blue-700"
                    ></span>

                    <h2
                        id="calendar-modal-title"
                        class="mt-4 text-3xl font-extrabold leading-tight text-slate-950"
                    ></h2>
                </div>

                <button
                    type="button"
                    id="calendar-modal-close"
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-slate-100 text-2xl text-slate-600 transition hover:bg-slate-200 hover:text-slate-950"
                    aria-label="Close event details"
                >
                    &times;
                </button>
            </div>

            <div class="mt-7 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-extrabold uppercase tracking-wide text-slate-500">
                        Date
                    </p>

                    <p
                        id="calendar-modal-date"
                        class="mt-1 font-bold text-slate-900"
                    ></p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-extrabold uppercase tracking-wide text-slate-500">
                        Time
                    </p>

                    <p
                        id="calendar-modal-time"
                        class="mt-1 font-bold text-slate-900"
                    ></p>
                </div>
            </div>

            <div
                id="calendar-modal-location-wrapper"
                class="mt-4 hidden rounded-2xl bg-slate-50 p-4"
            >
                <p class="text-xs font-extrabold uppercase tracking-wide text-slate-500">
                    Location
                </p>

                <p
                    id="calendar-modal-location"
                    class="mt-1 font-bold text-slate-900"
                ></p>
            </div>

            <div
                id="calendar-modal-description-wrapper"
                class="mt-7 hidden"
            >
                <h3 class="text-sm font-extrabold uppercase tracking-wide text-slate-500">
                    About this event
                </h3>

                <p
                    id="calendar-modal-description"
                    class="mt-3 whitespace-pre-line leading-7 text-slate-700"
                ></p>
            </div>

            <div
                id="calendar-modal-actions"
                class="mt-8 hidden border-t border-slate-200 pt-6"
            >
                <a
                    id="calendar-modal-external-link"
                    href="#"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex w-full items-center justify-center rounded-2xl bg-blue-700 px-6 py-3 font-extrabold text-white transition hover:bg-blue-800 sm:w-auto"
                >
                    More information
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.21/index.global.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const calendarElement = document.getElementById('church-calendar');

        const statusElement = document.getElementById('calendar-status');

        const printGridLink = document.getElementById(
            'calendar-print-grid-link'
        );

        const printListLink = document.getElementById(
            'calendar-print-list-link'
        );

        const pdfGridLink = document.getElementById(
            'calendar-pdf-grid-link'
        );

        const pdfListLink = document.getElementById(
            'calendar-pdf-list-link'
        );

        const modal = document.getElementById('calendar-event-modal');

        const modalBackdrop = document.getElementById(
            'calendar-modal-backdrop'
        );

        const modalClose = document.getElementById(
            'calendar-modal-close'
        );

        const modalTitle = document.getElementById(
            'calendar-modal-title'
        );

        const modalCategory = document.getElementById(
            'calendar-modal-category'
        );

        const modalDate = document.getElementById(
            'calendar-modal-date'
        );

        const modalTime = document.getElementById(
            'calendar-modal-time'
        );

        const modalColour = document.getElementById(
            'calendar-modal-colour'
        );

        const modalLocationWrapper = document.getElementById(
            'calendar-modal-location-wrapper'
        );

        const modalLocation = document.getElementById(
            'calendar-modal-location'
        );

        const modalDescriptionWrapper = document.getElementById(
            'calendar-modal-description-wrapper'
        );

        const modalDescription = document.getElementById(
            'calendar-modal-description'
        );

        const modalImageWrapper = document.getElementById(
            'calendar-modal-image-wrapper'
        );

        const modalImage = document.getElementById(
            'calendar-modal-image'
        );

        const modalActions = document.getElementById(
            'calendar-modal-actions'
        );

        const modalExternalLink = document.getElementById(
            'calendar-modal-external-link'
        );

        function showStatus(message, type = 'info') {
            statusElement.textContent = message;

            statusElement.classList.remove(
                'hidden',
                'border-blue-200',
                'bg-blue-50',
                'text-blue-800',
                'border-red-200',
                'bg-red-50',
                'text-red-800'
            );

            if (type === 'error') {
                statusElement.classList.add(
                    'border-red-200',
                    'bg-red-50',
                    'text-red-800'
                );

                return;
            }

            statusElement.classList.add(
                'border-blue-200',
                'bg-blue-50',
                'text-blue-800'
            );
        }

        function hideStatus() {
            statusElement.classList.add('hidden');
        }

        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modal.setAttribute('aria-hidden', 'true');

            document.body.classList.remove('overflow-hidden');
        }

        function openModal(event) {
            const details = event.extendedProps || {};

            modalTitle.textContent =
                event.title || 'Calendar event';

            modalCategory.textContent =
                details.categoryLabel || 'Event';

            modalDate.textContent =
                details.dateLabel || '';

            modalTime.textContent =
                details.timeLabel || '';

            modalColour.style.backgroundColor =
                details.colour ||
                event.backgroundColor ||
                '#1d4ed8';

            if (details.location) {
                modalLocation.textContent =
                    details.location;

                modalLocationWrapper.classList.remove(
                    'hidden'
                );
            } else {
                modalLocation.textContent = '';

                modalLocationWrapper.classList.add(
                    'hidden'
                );
            }

            if (details.description) {
                modalDescription.textContent =
                    details.description;

                modalDescriptionWrapper.classList.remove(
                    'hidden'
                );
            } else {
                modalDescription.textContent = '';

                modalDescriptionWrapper.classList.add(
                    'hidden'
                );
            }

            if (details.imageUrl) {
                modalImage.src = details.imageUrl;
                modalImage.alt =
                    event.title || 'Calendar event';

                modalImageWrapper.classList.remove(
                    'hidden'
                );
            } else {
                modalImage.removeAttribute('src');
                modalImage.alt = '';

                modalImageWrapper.classList.add(
                    'hidden'
                );
            }

            if (details.externalUrl) {
                modalExternalLink.href =
                    details.externalUrl;

                modalActions.classList.remove(
                    'hidden'
                );
            } else {
                modalExternalLink.href = '#';

                modalActions.classList.add(
                    'hidden'
                );
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.setAttribute('aria-hidden', 'false');

            document.body.classList.add(
                'overflow-hidden'
            );

            modalClose.focus();
        }

        function updateDownloadLinks(calendar) {
            const visibleDate = calendar.getDate();

            const year = visibleDate.getFullYear();

            const month = String(
                visibleDate.getMonth() + 1
            ).padStart(2, '0');

            const value = `${year}-${month}`;

            const printBase = @json(route('calendar.print'));
            const pdfBase = @json(route('calendar.pdf'));

            printGridLink.href =
                printBase
                + '?month='
                + encodeURIComponent(value)
                + '&layout=grid';

            printListLink.href =
                printBase
                + '?month='
                + encodeURIComponent(value)
                + '&layout=list';

            pdfGridLink.href =
                pdfBase
                + '?month='
                + encodeURIComponent(value)
                + '&layout=grid';

            pdfListLink.href =
                pdfBase
                + '?month='
                + encodeURIComponent(value)
                + '&layout=list';
        }

        const isMobile = window.matchMedia(
            '(max-width: 767px)'
        ).matches;

        const calendar = new FullCalendar.Calendar(
            calendarElement,
            {
                initialView: isMobile
                    ? 'listMonth'
                    : 'dayGridMonth',

                firstDay: 1,

                height: 'auto',

                nowIndicator: true,

                navLinks: true,

                dayMaxEvents: false,

                eventDisplay: 'block',

                displayEventTime: true,

                timeZone: 'local',

                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listMonth'
                },

                buttonText: {
                    today: 'Today',
                    month: 'Month',
                    week: 'Week',
                    list: 'List'
                },

                eventTimeFormat: {
                    hour: 'numeric',
                    minute: '2-digit',
                    meridiem: 'short',
                    hour12: true
                },

                events: {
                    url: @json(route('calendar.feed')),

                    method: 'GET',

                    failure: function () {
                        showStatus(
                            'We could not load the church calendar. Please refresh the page and try again.',
                            'error'
                        );
                    }
                },

                loading: function (isLoading) {
                    if (isLoading) {
                        showStatus(
                            'Loading calendar events...'
                        );

                        return;
                    }

                    if (
                        !statusElement.classList.contains(
                            'border-red-200'
                        )
                    ) {
                        hideStatus();
                    }
                },

                datesSet: function () {
                    updateDownloadLinks(calendar);
                },

                eventClick: function (info) {
                    info.jsEvent.preventDefault();

                    openModal(info.event);
                },

                eventDidMount: function (info) {
                    info.el.setAttribute(
                        'title',
                        info.event.title
                    );
                },

                eventContent: function (info) {
                    const details =
                        info.event.extendedProps || {};

                    /*
                    |--------------------------------------------------------------------------
                    | PDF-style list view
                    |--------------------------------------------------------------------------
                    */

                    if (
                        info.view.type === 'listMonth'
                    ) {
                        const card =
                            document.createElement('div');

                        card.className =
                            'ffc-pdf-style-event-card';

                        const colour =
                            details.colour ||
                            info.event.backgroundColor ||
                            '#1d4ed8';

                        card.style.setProperty(
                            '--event-colour',
                            colour
                        );

                        const dateBlock =
                            document.createElement('div');

                        dateBlock.className =
                            'ffc-pdf-style-date';

                        const eventStart =
                            info.event.start;

                        const dayNumber =
                            document.createElement('div');

                        dayNumber.className =
                            'ffc-pdf-style-day';

                        dayNumber.textContent =
                            eventStart
                                ? String(
                                    eventStart.getDate()
                                ).padStart(2, '0')
                                : '';

                        const monthName =
                            document.createElement('div');

                        monthName.className =
                            'ffc-pdf-style-month';

                        monthName.textContent =
                            eventStart
                                ? eventStart
                                    .toLocaleDateString(
                                        'en-GB',
                                        {
                                            month: 'short'
                                        }
                                    )
                                    .toUpperCase()
                                : '';

                        dateBlock.appendChild(
                            dayNumber
                        );

                        dateBlock.appendChild(
                            monthName
                        );

                        const content =
                            document.createElement('div');

                        content.className =
                            'ffc-pdf-style-content';

                        const title =
                            document.createElement('h3');

                        title.className =
                            'ffc-pdf-style-title';

                        title.textContent =
                            info.event.title;

                        const cleanDate =
                            details.dateLabel
                                ? details.dateLabel
                                    .split(' at ')[0]
                                : '';

                        const date =
                            document.createElement('div');

                        date.className =
                            'ffc-pdf-style-date-label';

                        date.textContent =
                            cleanDate;

                        const time =
                            document.createElement('div');

                        time.className =
                            'ffc-pdf-style-time';

                        time.textContent =
                            details.timeLabel || '';

                        content.appendChild(title);
                        content.appendChild(date);
                        content.appendChild(time);

                        if (details.location) {
                            const location =
                                document.createElement(
                                    'div'
                                );

                            location.className =
                                'ffc-pdf-style-location';

                            location.textContent =
                                details.location;

                            content.appendChild(
                                location
                            );
                        }

                        card.appendChild(dateBlock);
                        card.appendChild(content);

                        return {
                            domNodes: [card]
                        };
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Month view
                    |--------------------------------------------------------------------------
                    */

                    if (
                        info.view.type.startsWith(
                            'dayGrid'
                        )
                    ) {
                        const eventDate =
                            info.event.startStr.substring(
                                0,
                                10
                            );

                        const eventsOnSameDate =
                            calendar
                                .getEvents()
                                .filter(
                                    function (
                                        calendarEvent
                                    ) {
                                        return calendarEvent
                                            .startStr
                                            .substring(
                                                0,
                                                10
                                            ) === eventDate;
                                    }
                                );

                        const wrapper =
                            document.createElement(
                                'div'
                            );

                        wrapper.className =
                            'ffc-calendar-event-content';

                        if (
                            eventsOnSameDate.length > 1
                        ) {
                            wrapper.classList.add(
                                'ffc-calendar-event-content--compact'
                            );
                        }

                        if (info.timeText) {
                            const time =
                                document.createElement(
                                    'span'
                                );

                            time.className =
                                'ffc-calendar-event-time';

                            time.textContent =
                                info.timeText;

                            wrapper.appendChild(time);
                        }

                        const title =
                            document.createElement(
                                'span'
                            );

                        title.className =
                            'ffc-calendar-event-title';

                        title.textContent =
                            info.event.title;

                        wrapper.appendChild(title);

                        return {
                            domNodes: [wrapper]
                        };
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Week view
                    |--------------------------------------------------------------------------
                    */

                    const wrapper =
                        document.createElement('div');

                    wrapper.className =
                        'ffc-week-event-content';

                    const title =
                        document.createElement('strong');

                    title.textContent =
                        info.event.title;

                    wrapper.appendChild(title);

                    if (details.location) {
                        const location =
                            document.createElement(
                                'small'
                            );

                        location.textContent =
                            details.location;

                        wrapper.appendChild(location);
                    }

                    return {
                        domNodes: [wrapper]
                    };
                }
            }
        );

        calendar.render();

        modalClose.addEventListener(
            'click',
            closeModal
        );

        modalBackdrop.addEventListener(
            'click',
            closeModal
        );

        document.addEventListener(
            'keydown',
            function (event) {
                if (event.key === 'Escape') {
                    closeModal();
                }
            }
        );

        let mobileState = isMobile;

        window.addEventListener(
            'resize',
            function () {
                const nowMobile =
                    window.matchMedia(
                        '(max-width: 767px)'
                    ).matches;

                if (nowMobile === mobileState) {
                    return;
                }

                mobileState = nowMobile;

                calendar.changeView(
                    nowMobile
                        ? 'listMonth'
                        : 'dayGridMonth'
                );
            }
        );
    });
</script>

<style>
    /*
    |--------------------------------------------------------------------------
    | FullCalendar base styling
    |--------------------------------------------------------------------------
    */

    #church-calendar {
        --fc-border-color: #e2e8f0;
        --fc-page-bg-color: #ffffff;
        --fc-neutral-bg-color: #f8fafc;
        --fc-today-bg-color: rgba(37, 99, 235, 0.07);
        --fc-button-bg-color: transparent;
        --fc-button-border-color: transparent;
        --fc-button-hover-bg-color: transparent;
        --fc-button-hover-border-color: transparent;
        --fc-button-active-bg-color: transparent;
        --fc-button-active-border-color: transparent;
    }

    #church-calendar .fc-toolbar {
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    #church-calendar .fc-toolbar-title {
        color: #0f172a;
        font-size: 1.75rem;
        font-weight: 800;
    }

    /*
    |--------------------------------------------------------------------------
    | Minimal previous and next chevrons
    |--------------------------------------------------------------------------
    */

    #church-calendar .fc-prev-button,
    #church-calendar .fc-next-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2.2rem;
        height: 2.5rem;
        padding: 0.25rem;
        border: 0;
        border-radius: 0;
        background: transparent !important;
        box-shadow: none !important;
        color: #0756b9 !important;
        font-size: 1.35rem;
        transition:
            color 150ms ease,
            transform 150ms ease;
    }

    #church-calendar .fc-prev-button:hover,
    #church-calendar .fc-next-button:hover {
        background: transparent !important;
        color: #0f3e88 !important;
        transform: scale(1.12);
    }

    #church-calendar .fc-prev-button:focus,
    #church-calendar .fc-next-button:focus {
        background: transparent !important;
        box-shadow: none !important;
        outline: 2px solid rgba(37, 99, 235, 0.25);
        outline-offset: 2px;
    }

    #church-calendar .fc-prev-button .fc-icon,
    #church-calendar .fc-next-button .fc-icon {
        font-size: 1.45rem;
        font-weight: 800;
    }

    /*
    |--------------------------------------------------------------------------
    | Today button
    |--------------------------------------------------------------------------
    */

    #church-calendar .fc-today-button {
        margin-left: 0.65rem;
        border: 1px solid #cbd5e1 !important;
        border-radius: 0.75rem !important;
        background: #ffffff !important;
        color: #334155 !important;
        font-weight: 800;
        box-shadow: none !important;
    }

    #church-calendar .fc-today-button:hover {
        border-color: #0756b9 !important;
        background: #ffffff !important;
        color: #0756b9 !important;
    }

    #church-calendar .fc-today-button:disabled {
        opacity: 0.55;
    }

    /*
    |--------------------------------------------------------------------------
    | Month, Week and List navigation
    |--------------------------------------------------------------------------
    */

    #church-calendar .fc-dayGridMonth-button,
    #church-calendar .fc-timeGridWeek-button,
    #church-calendar .fc-listMonth-button {
        position: relative;
        margin: 0 0.75rem !important;
        padding: 0.6rem 0.15rem 0.7rem !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        color: #64748b !important;
        font-size: 1rem;
        font-weight: 800;
        text-transform: capitalize;
        transition: color 150ms ease;
    }

    #church-calendar .fc-dayGridMonth-button:hover,
    #church-calendar .fc-timeGridWeek-button:hover,
    #church-calendar .fc-listMonth-button:hover {
        background: transparent !important;
        color: #0756b9 !important;
    }

    #church-calendar .fc-dayGridMonth-button.fc-button-active,
    #church-calendar .fc-timeGridWeek-button.fc-button-active,
    #church-calendar .fc-listMonth-button.fc-button-active {
        background: transparent !important;
        color: #0756b9 !important;
    }

    #church-calendar .fc-dayGridMonth-button::after,
    #church-calendar .fc-timeGridWeek-button::after,
    #church-calendar .fc-listMonth-button::after {
        position: absolute;
        right: 0;
        bottom: 0;
        left: 0;
        height: 3px;
        border-radius: 999px;
        background: transparent;
        content: '';
        transition: background 150ms ease;
    }

    #church-calendar
        .fc-dayGridMonth-button.fc-button-active::after,
    #church-calendar
        .fc-timeGridWeek-button.fc-button-active::after,
    #church-calendar
        .fc-listMonth-button.fc-button-active::after {
        background: #0756b9;
    }

    #church-calendar .fc-button:focus {
        box-shadow: none !important;
    }

    /*
    |--------------------------------------------------------------------------
    | Month grid
    |--------------------------------------------------------------------------
    */

    #church-calendar .fc-col-header-cell-cushion,
    #church-calendar .fc-daygrid-day-number {
        color: #334155;
        font-weight: 700;
        padding: 0.65rem;
    }

    #church-calendar .fc-event {
        cursor: pointer;
        border-radius: 0.6rem;
        font-weight: 700;
    }

    #church-calendar .fc-daygrid-event {
        margin: 0.2rem 0.25rem;
        padding: 0;
        white-space: normal;
    }

    #church-calendar .fc-daygrid-event .fc-event-main {
        min-width: 0;
        padding: 0;
        white-space: normal;
    }

    #church-calendar .ffc-calendar-event-content {
        display: flex;
        align-items: flex-start;
        gap: 0.3rem;
        min-width: 0;
        width: 100%;
        padding: 0.38rem 0.48rem;
        line-height: 1.25;
        white-space: normal;
    }

    #church-calendar .ffc-calendar-event-time {
        flex: 0 0 auto;
        font-size: 0.76rem;
        font-weight: 800;
        opacity: 0.95;
        white-space: nowrap;
    }

    #church-calendar .ffc-calendar-event-title {
        display: block;
        flex: 1 1 auto;
        min-width: 0;
        overflow: visible;
        font-size: 0.78rem;
        font-weight: 800;
        line-height: 1.3;
        text-overflow: clip;
        white-space: normal;
        overflow-wrap: anywhere;
        word-break: normal;
    }

    #church-calendar
        .ffc-calendar-event-content--compact
        .ffc-calendar-event-title {
        display: -webkit-box;
        overflow: hidden;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
    }

    #church-calendar .fc-daygrid-day-frame {
        min-height: 7.4rem;
    }

    #church-calendar .fc-daygrid-day-events {
        margin-top: 0.25rem;
    }

    /*
    |--------------------------------------------------------------------------
    | PDF-style List view
    |--------------------------------------------------------------------------
    */

    #church-calendar .fc-list {
        border: 0;
    }

    #church-calendar .fc-list-table {
        display: block;
        border: 0;
    }

    #church-calendar .fc-list-table tbody {
        display: block;
    }

    /*
     * The date appears inside each card, so the standard FullCalendar
     * date headings are removed.
     */
    #church-calendar .fc-list-day {
        display: none;
    }

    #church-calendar .fc-list-event {
        display: block;
        margin: 0 0 1rem;
        border: 0;
        background: transparent;
    }

    #church-calendar .fc-list-event:hover td {
        background: transparent;
    }

    #church-calendar .fc-list-event td {
        display: block;
        width: 100%;
        padding: 0;
        border: 0;
        background: transparent;
    }

    /*
     * Hide FullCalendar's own time and dot columns so the information is
     * not duplicated.
     */
    #church-calendar .fc-list-event-time,
    #church-calendar .fc-list-event-graphic {
        display: none !important;
    }

    #church-calendar .fc-list-event-title {
        display: block;
        width: 100%;
        padding: 0;
    }

    #church-calendar .fc-list-event-title > a {
        display: block;
        width: 100%;
        padding: 0;
        color: inherit;
        text-decoration: none;
    }

    #church-calendar .ffc-pdf-style-event-card {
        display: grid;
        grid-template-columns: 135px minmax(0, 1fr);
        width: 100%;
        min-height: 150px;
        overflow: hidden;
        border: 1px solid #d8e2ed;
        border-left: 8px solid var(--event-colour);
        border-radius: 1rem;
        background: #ffffff;
        transition:
            transform 160ms ease,
            box-shadow 160ms ease;
    }

    #church-calendar .ffc-pdf-style-event-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
    }

    #church-calendar .ffc-pdf-style-date {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        border-right: 1px solid #e3eaf1;
        background: #ffffff;
        text-align: center;
    }

    #church-calendar .ffc-pdf-style-day {
        color: #0756b9;
        font-size: 2.5rem;
        font-weight: 800;
        line-height: 1;
    }

    #church-calendar .ffc-pdf-style-month {
        margin-top: 0.55rem;
        color: #64748b;
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.15em;
    }

    #church-calendar .ffc-pdf-style-content {
        display: flex;
        justify-content: center;
        flex-direction: column;
        min-width: 0;
        padding: 1.5rem 1.75rem;
    }

    #church-calendar .ffc-pdf-style-title {
        margin: 0;
        color: #0f172a;
        font-size: 1.55rem;
        font-weight: 800;
        line-height: 1.25;
        overflow-wrap: anywhere;
    }

    #church-calendar .ffc-pdf-style-date-label {
        margin-top: 0.85rem;
        color: #0756b9;
        font-size: 1rem;
        font-weight: 800;
        line-height: 1.35;
    }

    #church-calendar .ffc-pdf-style-time {
        margin-top: 0.2rem;
        color: #0756b9;
        font-size: 1rem;
        font-weight: 800;
        line-height: 1.35;
    }

    #church-calendar .ffc-pdf-style-location {
        margin-top: 0.65rem;
        color: #475569;
        font-size: 0.92rem;
        font-weight: 700;
        line-height: 1.4;
        overflow-wrap: anywhere;
    }

    /*
    |--------------------------------------------------------------------------
    | Week view
    |--------------------------------------------------------------------------
    */

    #church-calendar .ffc-week-event-content {
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
        white-space: normal;
    }

    #church-calendar .ffc-week-event-content small {
        opacity: 0.85;
    }

    /*
    |--------------------------------------------------------------------------
    | Mobile
    |--------------------------------------------------------------------------
    */

    @media (max-width: 767px) {
        #church-calendar .fc-toolbar {
            align-items: center;
            flex-direction: column;
            gap: 1.1rem;
            margin-bottom: 1.5rem;
        }

        #church-calendar .fc-toolbar-chunk {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
        }

        #church-calendar .fc-toolbar-title {
            font-size: 1.3rem;
            text-align: center;
        }

        #church-calendar .fc-prev-button,
        #church-calendar .fc-next-button {
            min-width: 2rem;
            height: 2.2rem;
        }

        #church-calendar .fc-today-button {
            margin-left: 0.35rem;
        }

        #church-calendar .fc-dayGridMonth-button,
        #church-calendar .fc-timeGridWeek-button,
        #church-calendar .fc-listMonth-button {
            margin: 0 0.65rem !important;
            font-size: 0.95rem;
        }

        /*
        |--------------------------------------------------------------------------
        | Mobile month grid
        |--------------------------------------------------------------------------
        */

        #church-calendar .fc-daygrid-day-frame {
            min-height: 5.5rem;
        }

        #church-calendar .fc-daygrid-event {
            margin: 0.12rem;
            overflow: hidden;
        }

        #church-calendar .ffc-calendar-event-content {
            display: block;
            min-width: 0;
            padding: 0.25rem 0.3rem;
            overflow: hidden;
            white-space: nowrap;
        }

        #church-calendar .ffc-calendar-event-time {
            display: inline;
            margin-right: 0.25rem;
            font-size: 0.68rem;
            white-space: nowrap;
        }

        #church-calendar .ffc-calendar-event-title {
            display: inline;
            min-width: 0;
            overflow: hidden;
            font-size: 0.68rem;
            line-height: 1.2;
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow-wrap: normal;
            word-break: normal;
        }

        #church-calendar
            .ffc-calendar-event-content--compact
            .ffc-calendar-event-title {
            display: inline;
            overflow: hidden;
            -webkit-box-orient: initial;
            -webkit-line-clamp: initial;
        }

        /*
        |--------------------------------------------------------------------------
        | Mobile PDF-style List cards
        |--------------------------------------------------------------------------
        */

        #church-calendar .fc-list-event {
            margin-bottom: 0.85rem;
        }

        #church-calendar .ffc-pdf-style-event-card {
            grid-template-columns: 88px minmax(0, 1fr);
            min-height: 135px;
            border-left-width: 7px;
            border-radius: 0.9rem;
        }

        #church-calendar .ffc-pdf-style-day {
            font-size: 2rem;
        }

        #church-calendar .ffc-pdf-style-month {
            margin-top: 0.4rem;
            font-size: 0.68rem;
        }

        #church-calendar .ffc-pdf-style-content {
            padding: 1.1rem 1rem;
        }

        #church-calendar .ffc-pdf-style-title {
            font-size: 1.25rem;
            line-height: 1.25;
        }

        #church-calendar .ffc-pdf-style-date-label {
            margin-top: 0.7rem;
            font-size: 0.9rem;
        }

        #church-calendar .ffc-pdf-style-time {
            font-size: 0.9rem;
        }

        #church-calendar .ffc-pdf-style-location {
            margin-top: 0.5rem;
            font-size: 0.82rem;
        }
    }

    @media (max-width: 420px) {
        #church-calendar .ffc-pdf-style-event-card {
            grid-template-columns: 74px minmax(0, 1fr);
        }

        #church-calendar .ffc-pdf-style-day {
            font-size: 1.75rem;
        }

        #church-calendar .ffc-pdf-style-content {
            padding: 1rem 0.85rem;
        }

        #church-calendar .ffc-pdf-style-title {
            font-size: 1.1rem;
        }

        #church-calendar .ffc-pdf-style-date-label,
        #church-calendar .ffc-pdf-style-time {
            font-size: 0.84rem;
        }
    }
</style>

@endsection