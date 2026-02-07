<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Job Application #{{ $app->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #0f172a; }
        .title { font-size: 18px; font-weight: 700; margin-bottom: 6px; }
        .muted { color: #64748b; }
        .section { margin-top: 16px; padding: 12px 14px; border: 1px solid #e2e8f0; border-radius: 10px; }
        .section h2 { margin: 0 0 10px; font-size: 13px; font-weight: 700; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 7px 10px; border-top: 1px solid #e2e8f0; vertical-align: top; }
        .kv td:first-child { width: 34%; color: #334155; font-weight: 700; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; background: #ecfdf5; color: #065f46; font-weight: 700; font-size: 11px; }
        .break { word-break: break-word; }
        .small { font-size: 11px; }
    </style>
</head>
<body>

@php
    $a = $answers ?? [];

    $yesNo = function ($v) {
        if (is_bool($v)) return $v ? 'Yes' : 'No';
        $vv = strtolower((string) $v);
        if (in_array($vv, ['1','yes','true','y','on'], true)) return 'Yes';
        if (in_array($vv, ['0','no','false','n','off'], true)) return 'No';
        return (string) $v ?: '-';
    };

    $prettyEnum = function ($v) {
        if (is_null($v) || $v === '') return '-';
        return ucwords(str_replace('_', ' ', (string) $v));
    };
@endphp

<div class="title">Job Application</div>
<div class="muted small">
    Exported: {{ now()->format('d M Y, H:i') }}
</div>

{{-- Applicant --}}
<div class="section">
    <h2>Applicant</h2>
    <table class="kv">
        <tr>
            <td>Job</td>
            <td class="break">{{ optional($app->jobListing)->title ?? '-' }}</td>
        </tr>
        <tr>
            <td>Status</td>
            <td><span class="badge">{{ strtoupper($app->status ?? 'NEW') }}</span></td>
        </tr>
        <tr>
            <td>Full name</td>
            <td class="break">{{ $app->full_name ?? '-' }}</td>
        </tr>
        <tr>
            <td>Email</td>
            <td class="break">{{ $app->email ?? '-' }}</td>
        </tr>
        <tr>
            <td>Phone</td>
            <td class="break">{{ $app->phone ?? '-' }}</td>
        </tr>
        <tr>
            <td>Submitted at</td>
            <td>{{ optional($app->submitted_at)->format('d M Y, H:i') ?? '-' }}</td>
        </tr>
    </table>
</div>

{{-- Personal details --}}
<div class="section">
    <h2>Personal details</h2>
    <table class="kv">
        <tr><td>Date of birth</td><td>{{ data_get($a,'dob') ?: '-' }}</td></tr>
        <tr><td>NI number</td><td>{{ data_get($a,'ni_number') ?: '-' }}</td></tr>
        <tr><td>Address</td><td class="break">{{ data_get($a,'address') ?: '-' }}</td></tr>
    </table>
</div>

{{-- General information --}}
<div class="section">
    <h2>General information</h2>
    <table class="kv">
        <tr><td>Right to work in the UK?</td><td>{{ $yesNo(data_get($a,'right_to_work')) }}</td></tr>
        <tr><td>DBS status</td><td>{{ $prettyEnum(data_get($a,'dbs_status')) }}</td></tr>
        <tr><td>Care experience</td><td>{{ $prettyEnum(data_get($a,'care_experience')) }}</td></tr>
        <tr><td>Preferred role</td><td>{{ $prettyEnum(data_get($a,'preferred_role')) }}</td></tr>
        <tr><td>Availability</td><td>{{ $prettyEnum(data_get($a,'availability')) }}</td></tr>
        <tr><td>Start date</td><td>{{ data_get($a,'start_date') ?: '-' }}</td></tr>
        <tr><td>Why do you want this role?</td><td class="break">{{ data_get($a,'why_role') ?: '-' }}</td></tr>
    </table>
</div>

{{-- Reference --}}
<div class="section">
    <h2>Reference</h2>
    <table class="kv">
        <tr><td>Reference name</td><td class="break">{{ data_get($a,'ref1_name') ?: '-' }}</td></tr>
        <tr><td>Relationship</td><td class="break">{{ data_get($a,'ref1_relationship') ?: '-' }}</td></tr>
        <tr><td>Phone</td><td class="break">{{ data_get($a,'ref1_phone') ?: '-' }}</td></tr>
        <tr><td>Email</td><td class="break">{{ data_get($a,'ref1_email') ?: '-' }}</td></tr>
    </table>
</div>

{{-- Declaration --}}
<div class="section">
    <h2>Declaration</h2>
    <table class="kv">
        <tr><td>Truth declaration</td><td>{{ $yesNo(data_get($a,'declare_truth')) }}</td></tr>
        <tr><td>Safeguarding consent</td><td>{{ $yesNo(data_get($a,'declare_safeguarding')) }}</td></tr>
        <tr><td>Signature</td><td class="break">{{ data_get($a,'signature') ?: '-' }}</td></tr>
    </table>
</div>

</body>
</html>
