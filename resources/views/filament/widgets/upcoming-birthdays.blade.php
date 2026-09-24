<x-filament-widgets::widget>
    <x-filament::section>
        <style>
            .ff-birthdays {
                width: 100%;
                font-size: 14px;
                line-height: 1.45;
                color: #111827;
            }

            .dark .ff-birthdays {
                color: #f9fafb;
            }

            .ff-birthdays__heading {
                display: flex;
                align-items: center;
                gap: 10px;
                font-size: 18px;
                font-weight: 700;
                color: #111827;
            }

            .dark .ff-birthdays__heading {
                color: #f9fafb;
            }

            .ff-birthdays__cake {
                font-size: 22px;
                line-height: 1;
            }

            .ff-birthdays__groups {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 18px;
            }

            .ff-birthdays__group {
                min-width: 0;
                overflow: hidden;
                border: 1px solid #e5e7eb;
                border-radius: 14px;
                background: #ffffff;
            }

            .dark .ff-birthdays__group {
                border-color: rgba(255, 255, 255, 0.10);
                background: rgba(255, 255, 255, 0.03);
            }

            .ff-birthdays__group-title {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                padding: 12px 14px;
                border-bottom: 1px solid #e5e7eb;
                background: #f8fafc;
                color: #475569;
                font-size: 11px;
                font-weight: 800;
                letter-spacing: .08em;
                text-transform: uppercase;
            }

            .dark .ff-birthdays__group-title {
                border-color: rgba(255, 255, 255, 0.10);
                background: rgba(255, 255, 255, 0.05);
                color: #cbd5e1;
            }

            .ff-birthdays__group--today .ff-birthdays__group-title {
                background: #eff6ff;
                color: #1d4ed8;
            }

            .dark .ff-birthdays__group--today .ff-birthdays__group-title {
                background: rgba(37, 99, 235, 0.15);
                color: #93c5fd;
            }

            .ff-birthdays__count {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 24px;
                height: 24px;
                padding: 0 7px;
                border-radius: 999px;
                background: #e2e8f0;
                color: #475569;
                font-size: 11px;
                font-weight: 800;
            }

            .ff-birthdays__group--today .ff-birthdays__count {
                background: #2563eb;
                color: #ffffff;
            }

            .ff-birthdays__list {
                margin: 0;
                padding: 0;
                list-style: none;
            }

            .ff-birthdays__item {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                min-height: 52px;
                padding: 10px 14px;
                border-bottom: 1px solid #f1f5f9;
            }

            .ff-birthdays__item:last-child {
                border-bottom: 0;
            }

            .dark .ff-birthdays__item {
                border-color: rgba(255, 255, 255, 0.06);
            }

            .ff-birthdays__person {
                display: flex;
                align-items: center;
                gap: 10px;
                min-width: 0;
            }

            .ff-birthdays__initial {
                display: flex;
                align-items: center;
                justify-content: center;
                flex: 0 0 34px;
                width: 34px;
                height: 34px;
                border-radius: 999px;
                background: #e0e7ff;
                color: #1d4ed8;
                font-size: 12px;
                font-weight: 800;
            }

            .dark .ff-birthdays__initial {
                background: rgba(59, 130, 246, 0.18);
                color: #bfdbfe;
            }

            .ff-birthdays__name {
                min-width: 0;
                overflow: hidden;
                color: #111827;
                font-weight: 600;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .dark .ff-birthdays__name {
                color: #f9fafb;
            }

            .ff-birthdays__name-link {
                color: inherit;
                font: inherit;
                text-decoration: none;
                transition: color .15s ease;
            }

            .ff-birthdays__name-link:hover,
            .ff-birthdays__name-link:focus-visible {
                color: #2563eb;
                text-decoration: underline;
                text-underline-offset: 2px;
            }

            .dark .ff-birthdays__name-link:hover,
            .dark .ff-birthdays__name-link:focus-visible {
                color: #60a5fa;
            }

            .ff-birthdays__date {
                flex: 0 0 auto;
                color: #64748b;
                font-size: 12px;
                font-weight: 600;
                white-space: nowrap;
            }

            .dark .ff-birthdays__date {
                color: #cbd5e1;
            }

            .ff-birthdays__today-badge {
                display: inline-flex;
                align-items: center;
                border-radius: 999px;
                background: #2563eb;
                padding: 5px 10px;
                color: #ffffff;
                font-size: 11px;
                font-weight: 800;
                white-space: nowrap;
            }

            .ff-birthdays__empty-group {
                padding: 18px 14px;
                color: #94a3b8;
                font-size: 12px;
                text-align: center;
            }

            .ff-birthdays__footer {
                display: flex;
                justify-content: flex-end;
                margin-top: 16px;
                padding-top: 14px;
                border-top: 1px solid #e5e7eb;
            }

            .dark .ff-birthdays__footer {
                border-color: rgba(255, 255, 255, 0.10);
            }

            .ff-birthdays__link {
                display: inline-flex;
                align-items: center;
                gap: 7px;
                color: #2563eb;
                font-size: 13px;
                font-weight: 700;
                text-decoration: none;
            }

            .ff-birthdays__link:hover {
                color: #1d4ed8;
                text-decoration: underline;
            }

            .ff-birthdays__arrow {
                display: inline-block;
                font-size: 16px;
                line-height: 1;
            }

            .ff-birthdays__empty {
                padding: 32px 16px;
                text-align: center;
            }

            .ff-birthdays__empty-icon {
                margin-bottom: 8px;
                font-size: 30px;
            }

            .ff-birthdays__empty-title {
                margin: 0;
                color: #111827;
                font-weight: 700;
            }

            .dark .ff-birthdays__empty-title {
                color: #f9fafb;
            }

            .ff-birthdays__empty-text {
                margin: 5px 0 0;
                color: #64748b;
                font-size: 13px;
            }

            @media (max-width: 1024px) {
                .ff-birthdays__groups {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 640px) {
                .ff-birthdays__item {
                    align-items: flex-start;
                }

                .ff-birthdays__date,
                .ff-birthdays__today-badge {
                    margin-top: 7px;
                }
            }
        </style>

        <x-slot name="heading">
            <div class="ff-birthdays__heading">
                <span class="ff-birthdays__cake" aria-hidden="true">🎂</span>
                <span>Upcoming Birthdays</span>
            </div>
        </x-slot>

        <div class="ff-birthdays">
            @if($hasBirthdays)
                <div class="ff-birthdays__groups">
                    <section class="ff-birthdays__group ff-birthdays__group--today">
                        <div class="ff-birthdays__group-title">
                            <span>Today</span>
                            <span class="ff-birthdays__count">{{ $todayBirthdays->count() }}</span>
                        </div>

                        @if($todayBirthdays->isNotEmpty())
                            <ul class="ff-birthdays__list">
                                @foreach($todayBirthdays as $member)
                                    <li class="ff-birthdays__item">
                                        <div class="ff-birthdays__person">
                                            <span class="ff-birthdays__initial">
                                                {{ strtoupper(substr($member->first_name ?? $member->display_name ?? '?', 0, 1)) }}
                                            </span>
                                            <span class="ff-birthdays__name">
                                                <a
                                                    href="{{ \App\Filament\Resources\Members\MemberResource::getUrl('view', ['record' => $member]) }}"
                                                    class="ff-birthdays__name-link"
                                                    title="View {{ $member->display_name }}"
                                                >
                                                    {{ $member->display_name }}
                                                </a>
                                            </span>
                                        </div>
                                        <span class="ff-birthdays__today-badge">Today</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="ff-birthdays__empty-group">No birthdays today</div>
                        @endif
                    </section>

                    <section class="ff-birthdays__group">
                        <div class="ff-birthdays__group-title">
                            <span>Next 7 Days</span>
                            <span class="ff-birthdays__count">{{ $nextSevenDays->count() }}</span>
                        </div>

                        @if($nextSevenDays->isNotEmpty())
                            <ul class="ff-birthdays__list">
                                @foreach($nextSevenDays as $member)
                                    <li class="ff-birthdays__item">
                                        <div class="ff-birthdays__person">
                                            <span class="ff-birthdays__initial">
                                                {{ strtoupper(substr($member->first_name ?? $member->display_name ?? '?', 0, 1)) }}
                                            </span>
                                            <span class="ff-birthdays__name">
                                                <a
                                                    href="{{ \App\Filament\Resources\Members\MemberResource::getUrl('view', ['record' => $member]) }}"
                                                    class="ff-birthdays__name-link"
                                                    title="View {{ $member->display_name }}"
                                                >
                                                    {{ $member->display_name }}
                                                </a>
                                            </span>
                                        </div>
                                        <span class="ff-birthdays__date">{{ $member->next_birthday->format('j F') }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="ff-birthdays__empty-group">No birthdays in the next 7 days</div>
                        @endif
                    </section>

                    <section class="ff-birthdays__group">
                        <div class="ff-birthdays__group-title">
                            <span>Coming Up</span>
                            <span class="ff-birthdays__count">{{ $comingUp->count() }}</span>
                        </div>

                        @if($comingUp->isNotEmpty())
                            <ul class="ff-birthdays__list">
                                @foreach($comingUp as $member)
                                    <li class="ff-birthdays__item">
                                        <div class="ff-birthdays__person">
                                            <span class="ff-birthdays__initial">
                                                {{ strtoupper(substr($member->first_name ?? $member->display_name ?? '?', 0, 1)) }}
                                            </span>
                                            <span class="ff-birthdays__name">
                                                <a
                                                    href="{{ \App\Filament\Resources\Members\MemberResource::getUrl('view', ['record' => $member]) }}"
                                                    class="ff-birthdays__name-link"
                                                    title="View {{ $member->display_name }}"
                                                >
                                                    {{ $member->display_name }}
                                                </a>
                                            </span>
                                        </div>
                                        <span class="ff-birthdays__date">{{ $member->next_birthday->format('j F') }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="ff-birthdays__empty-group">No additional birthdays in the next 30 days</div>
                        @endif
                    </section>
                </div>

                <div class="ff-birthdays__footer">
                    <a href="{{ $membersUrl }}" class="ff-birthdays__link">
                        <span>View all birthdays</span>
                        <span class="ff-birthdays__arrow" aria-hidden="true">→</span>
                    </a>
                </div>
            @else
                <div class="ff-birthdays__empty">
                    <div class="ff-birthdays__empty-icon" aria-hidden="true">🎂</div>
                    <p class="ff-birthdays__empty-title">No upcoming birthdays</p>
                    <p class="ff-birthdays__empty-text">There are no active member birthdays in the next 30 days.</p>
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
