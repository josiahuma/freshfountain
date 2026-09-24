@php
    $user = auth()->user();
    $maxAttendance = max(1, (int) $recentAttendances->max('total'));
@endphp

<x-filament-widgets::widget>
    <style>
        .ff-dashboard, .ff-dashboard * { box-sizing: border-box; }
        .ff-dashboard { display: grid; gap: 22px; width: 100%; color: #0f172a; }
        .ff-welcome { display:flex; justify-content:space-between; align-items:center; gap:24px; padding:28px 30px; border-radius:22px; background:linear-gradient(135deg,#0f172a 0%,#172554 48%,#0755bd 100%); color:#fff; box-shadow:0 18px 45px rgba(15,23,42,.16); }
        .ff-eyebrow { display:inline-flex; padding:6px 10px; border-radius:999px; background:rgba(255,255,255,.13); font-size:11px; font-weight:800; letter-spacing:.08em; text-transform:uppercase; }
        .ff-welcome h2 { margin:12px 0 5px; font-size:27px; line-height:1.15; font-weight:800; }
        .ff-welcome p { margin:0; color:rgba(255,255,255,.78); font-size:14px; }
        .ff-welcome-date { min-width:170px; padding:14px 18px; border:1px solid rgba(255,255,255,.16); border-radius:16px; background:rgba(255,255,255,.09); text-align:right; }
        .ff-welcome-date span, .ff-welcome-date strong { display:block; }
        .ff-welcome-date span { font-size:12px; color:rgba(255,255,255,.7); }
        .ff-welcome-date strong { margin-top:3px; font-size:15px; }
        .ff-kpis { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:16px; }
        .ff-kpi { position:relative; display:block; min-height:160px; padding:20px; overflow:hidden; text-decoration:none !important; color:#0f172a !important; background:#fff; border:1px solid #e2e8f0; border-radius:18px; box-shadow:0 5px 18px rgba(15,23,42,.045); transition:.18s ease; }
        .ff-kpi:hover { transform:translateY(-2px); border-color:#bfdbfe; box-shadow:0 12px 26px rgba(37,99,235,.10); }
        .ff-kpi-icon { display:flex; align-items:center; justify-content:center; width:36px; height:36px; margin-bottom:13px; border-radius:11px; font-size:17px; font-weight:900; }
        .ff-blue { background:#dbeafe; color:#1d4ed8; } .ff-indigo { background:#e0e7ff; color:#4338ca; } .ff-violet { background:#ede9fe; color:#6d28d9; } .ff-sky { background:#e0f2fe; color:#0369a1; }
        .ff-kpi-label { color:#64748b; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.04em; }
        .ff-kpi-value { margin:3px 0 2px; font-size:31px; line-height:1.1; font-weight:850; }
        .ff-kpi-note { color:#64748b; font-size:12px; line-height:1.4; }
        .ff-main-grid { display:grid; grid-template-columns:minmax(0,1.2fr) minmax(340px,.8fr); gap:18px; align-items:stretch; }
        .ff-panel { background:#fff; border:1px solid #e2e8f0; border-radius:20px; padding:22px; box-shadow:0 5px 18px rgba(15,23,42,.04); }
        .ff-panel-head { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:19px; }
        .ff-section-kicker { color:#2563eb; font-size:10px; font-weight:850; text-transform:uppercase; letter-spacing:.12em; }
        .ff-panel-head h3 { margin:3px 0 2px; font-size:18px; font-weight:800; }
        .ff-panel-head p { margin:0; color:#64748b; font-size:12px; }
        .ff-panel-head a { flex:none; color:#2563eb; font-size:12px; font-weight:750; text-decoration:none; }
        .ff-attendance-summary { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:10px; margin-bottom:20px; }
        .ff-attendance-summary > div { padding:14px; background:#f8fafc; border:1px solid #eef2f7; border-radius:14px; }
        .ff-attendance-summary span, .ff-attendance-summary strong, .ff-attendance-summary small { display:block; }
        .ff-attendance-summary span { color:#64748b; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.04em; }
        .ff-attendance-summary strong { margin:4px 0 3px; font-size:23px; }
        .ff-attendance-summary small { color:#64748b; font-size:10px; line-height:1.35; }
        .ff-positive { color:#15803d; } .ff-negative { color:#b91c1c; }
        .ff-bars { display:grid; gap:9px; }
        .ff-bar-row { display:grid; grid-template-columns:74px 1fr; gap:10px; align-items:center; }
        .ff-bar-label { display:flex; justify-content:space-between; gap:6px; font-size:10px; color:#64748b; }
        .ff-bar-label strong { color:#334155; }
        .ff-bar-track { height:8px; overflow:hidden; border-radius:999px; background:#eef2f7; }
        .ff-bar-fill { height:100%; border-radius:999px; background:linear-gradient(90deg,#2563eb,#60a5fa); }
        .ff-events { display:grid; gap:0; }
        .ff-event { display:flex; gap:13px; align-items:center; padding:12px 0; border-top:1px solid #eef2f7; }
        .ff-event:first-child { border-top:0; padding-top:2px; }
        .ff-event-date { flex:0 0 48px; width:48px; overflow:hidden; text-align:center; border:1px solid #dbeafe; border-radius:11px; background:#eff6ff; }
        .ff-event-date span { display:block; padding:3px; color:#fff; background:#2563eb; font-size:8px; font-weight:850; letter-spacing:.08em; }
        .ff-event-date strong { display:block; padding:5px 2px 6px; color:#1e3a8a; font-size:18px; }
        .ff-event-info { min-width:0; }
        .ff-event-info strong, .ff-event-info span, .ff-event-info small { display:block; }
        .ff-event-info strong { overflow:hidden; color:#0f172a; font-size:13px; text-overflow:ellipsis; white-space:nowrap; }
        .ff-event-info span { margin-top:2px; color:#475569; font-size:11px; }
        .ff-event-info small { margin-top:2px; color:#94a3b8; font-size:10px; }
        .ff-empty { padding:24px; color:#64748b; background:#f8fafc; border:1px dashed #cbd5e1; border-radius:14px; text-align:center; font-size:12px; }
        .ff-glance-panel { padding-bottom:20px; }
        .ff-glance { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px; }
        .ff-glance a { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:15px 16px; text-decoration:none !important; color:#334155 !important; background:#f8fafc; border:1px solid #eef2f7; border-radius:14px; }
        .ff-glance a:hover { background:#eff6ff; border-color:#bfdbfe; }
        .ff-glance span { font-size:11px; font-weight:650; }
        .ff-glance strong { color:#0f172a; font-size:20px; }
        @media (max-width:1100px) { .ff-kpis { grid-template-columns:repeat(2,minmax(0,1fr)); } .ff-main-grid { grid-template-columns:1fr; } .ff-glance { grid-template-columns:repeat(2,minmax(0,1fr)); } }
        @media (max-width:700px) { .ff-welcome { align-items:flex-start; padding:22px; flex-direction:column; } .ff-welcome h2 { font-size:23px; } .ff-welcome-date { width:100%; text-align:left; } .ff-kpis { grid-template-columns:1fr; } .ff-kpi { min-height:auto; } .ff-panel { padding:17px; } .ff-panel-head { flex-direction:column; } .ff-attendance-summary { grid-template-columns:1fr; } .ff-glance { grid-template-columns:1fr; } }
    </style>
    

    <div class="ff-dashboard">
        <section class="ff-welcome">
            <div>
                <div class="ff-eyebrow">Fresh Fountain Church CRM</div>
                <h2>Welcome back, {{ $user?->name ?? 'Admin' }}</h2>
                <p>Here is the latest picture of church membership, attendance and upcoming activity.</p>
            </div>
            <div class="ff-welcome-date">
                <span>{{ now()->format('l') }}</span>
                <strong>{{ now()->format('j F Y') }}</strong>
            </div>
        </section>
    
        <section class="ff-kpis">
            @if($canViewMembers)
                <a href="{{ $membersUrl }}" class="ff-kpi">
                    <div class="ff-kpi-icon ff-blue">👥</div>
                    <div class="ff-kpi-label">Active Members</div>
                    <div class="ff-kpi-value">{{ number_format($activeMembers) }}</div>
                    <div class="ff-kpi-note">Current active membership</div>
                </a>
    
                <a href="{{ $membersUrl }}" class="ff-kpi">
                    <div class="ff-kpi-icon ff-indigo">＋</div>
                    <div class="ff-kpi-label">New Members</div>
                    <div class="ff-kpi-value">{{ number_format($newMembers) }}</div>
                    <div class="ff-kpi-note">Joined in the last 30 days</div>
                </a>
            @endif
    
            @if($canViewAttendance)
                <a href="{{ $attendanceUrl }}" class="ff-kpi">
                    <div class="ff-kpi-icon ff-violet">🙌</div>
                    <div class="ff-kpi-label">Latest Attendance</div>
                    <div class="ff-kpi-value">{{ number_format((int) ($latestAttendance?->total ?? 0)) }}</div>
                    <div class="ff-kpi-note">
                        @if($latestAttendance)
                            {{ $latestAttendance->service_name }} · {{ $latestAttendance->service_date->format('j M') }}
                        @else
                            No attendance recorded yet
                        @endif
                    </div>
                </a>
            @endif
    
            @if($canViewCalendar)
                <a href="{{ $calendarUrl }}" class="ff-kpi">
                    <div class="ff-kpi-icon ff-sky">📅</div>
                    <div class="ff-kpi-label">Upcoming Events</div>
                    <div class="ff-kpi-value">{{ number_format((int) $upcomingEventCount) }}</div>
                    <div class="ff-kpi-note">Occurrences in the next 30 days</div>
                </a>
            @endif
        </section>
    
        @if($canViewAttendance || $canViewCalendar)
            <section class="ff-main-grid">
                @if($canViewAttendance)
                    <div class="ff-panel">
                        <div class="ff-panel-head">
                            <div>
                                <div class="ff-section-kicker">Attendance</div>
                                <h3>Recent Service Attendance</h3>
                                <p>Latest recorded services at a glance.</p>
                            </div>
                            <a href="{{ $attendanceUrl }}">View attendance →</a>
                        </div>
    
                        @if($latestAttendance)
                            <div class="ff-attendance-summary">
                                <div>
                                    <span>Latest service</span>
                                    <strong>{{ number_format((int) $latestAttendance->total) }}</strong>
                                    <small>{{ $latestAttendance->service_name }} · {{ $latestAttendance->service_date->format('j F Y') }}</small>
                                </div>
                                <div>
                                    <span>Previous service</span>
                                    <strong>{{ number_format((int) ($previousAttendance?->total ?? 0)) }}</strong>
                                    <small>
                                        {{ $previousAttendance ? $previousAttendance->service_name.' · '.$previousAttendance->service_date->format('j F Y') : 'No previous record' }}
                                    </small>
                                </div>
                                <div>
                                    <span>Change</span>
                                    <strong class="{{ ($attendanceChange ?? 0) >= 0 ? 'ff-positive' : 'ff-negative' }}">
                                        @if($attendanceChange !== null)
                                            {{ $attendanceChange >= 0 ? '+' : '' }}{{ number_format($attendanceChange, 1) }}%
                                        @else
                                            —
                                        @endif
                                    </strong>
                                    <small>Compared with previous service</small>
                                </div>
                            </div>
    
                            <div class="ff-bars">
                                @foreach($recentAttendances->reverse()->values() as $attendance)
                                    @php $barWidth = max(6, ((int) $attendance->total / $maxAttendance) * 100); @endphp
                                    <div class="ff-bar-row">
                                        <div class="ff-bar-label">
                                            <span>{{ $attendance->service_date->format('j M') }}</span>
                                            <strong>{{ number_format((int) $attendance->total) }}</strong>
                                        </div>
                                        <div class="ff-bar-track">
                                            <div class="ff-bar-fill" style="width: {{ $barWidth }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="ff-empty">No attendance records have been added yet.</div>
                        @endif
                    </div>
                @endif
    
                @if($canViewCalendar)
                    <div class="ff-panel">
                        <div class="ff-panel-head">
                            <div>
                                <div class="ff-section-kicker">Calendar</div>
                                <h3>Upcoming Events</h3>
                                <p>Your next church events and services.</p>
                            </div>
                            <a href="{{ $calendarUrl }}">View calendar →</a>
                        </div>
    
                        <div class="ff-events">
                            @forelse($upcomingEvents as $event)
                                @php
                                    $start = \Carbon\CarbonImmutable::parse($event['start']);
                                    $details = $event['extendedProps'] ?? [];
                                @endphp
                                <div class="ff-event">
                                    <div class="ff-event-date">
                                        <span>{{ strtoupper($start->format('M')) }}</span>
                                        <strong>{{ $start->format('d') }}</strong>
                                    </div>
                                    <div class="ff-event-info">
                                        <strong>{{ $event['title'] }}</strong>
                                        <span>
                                            {{ $start->format('l, j F') }}
                                            @if(!($event['allDay'] ?? false))
                                                · {{ $start->format('g:i A') }}
                                            @endif
                                        </span>
                                        @if(filled($details['location'] ?? null))
                                            <small>📍 {{ $details['location'] }}</small>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="ff-empty">There are no upcoming calendar events in the next 60 days.</div>
                            @endforelse
                        </div>
                    </div>
                @endif
            </section>
        @endif
    
        @if($canViewUnits || $canViewLeaders || $canViewAttendance)
            <section class="ff-panel ff-glance-panel">
                <div class="ff-panel-head">
                    <div>
                        <div class="ff-section-kicker">Church CRM</div>
                        <h3>Church at a Glance</h3>
                        <p>Useful operational totals from the current month.</p>
                    </div>
                </div>
    
                <div class="ff-glance">
                    @if($canViewUnits)
                        <a href="{{ $unitsUrl }}">
                            <span>Active Church Units</span>
                            <strong>{{ number_format($activeUnits) }}</strong>
                        </a>
                    @endif
                    @if($canViewLeaders)
                        <a href="{{ $leadersUrl }}">
                            <span>Active Leaders</span>
                            <strong>{{ number_format($activeLeaders) }}</strong>
                        </a>
                    @endif
                    @if($canViewAttendance)
                        <a href="{{ $attendanceUrl }}">
                            <span>Visitors This Month</span>
                            <strong>{{ number_format($visitorsThisMonth) }}</strong>
                        </a>
                        <a href="{{ $attendanceUrl }}">
                            <span>Total Attendance This Month</span>
                            <strong>{{ number_format($totalAttendanceThisMonth) }}</strong>
                        </a>
                    @endif
                </div>
            </section>
        @endif
    </div>
</x-filament-widgets::widget>
