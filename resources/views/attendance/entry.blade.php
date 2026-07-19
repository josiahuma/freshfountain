<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Record Attendance</title>
    <style>
        *{box-sizing:border-box}body{margin:0;background:#f4f7fb;color:#14213d;font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif}.page{max-width:860px;margin:0 auto;padding:20px}.top{display:flex;justify-content:space-between;align-items:center;gap:16px;margin-bottom:18px}.brand{font-weight:900;font-size:18px}.back{color:#174ea6;text-decoration:none;font-weight:800}.hero{background:linear-gradient(135deg,#102a43,#0f6cbf);color:white;border-radius:24px;padding:28px;box-shadow:0 18px 50px rgba(16,42,67,.2)}.hero h1{margin:0;font-size:32px}.hero p{margin:8px 0 0;color:rgba(255,255,255,.85)}.card{background:white;border:1px solid #dfe7f1;border-radius:24px;padding:24px;margin-top:18px;box-shadow:0 14px 40px rgba(15,23,42,.08)}.grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}.numbers{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:14px;margin-top:18px}label{display:block;font-size:14px;font-weight:850;margin-bottom:7px}input,select,textarea{width:100%;border:1px solid #cbd5e1;border-radius:14px;padding:14px 15px;font:inherit;background:white}input[type=number]{font-size:25px;font-weight:900;text-align:center;padding:17px 8px}input:focus,select:focus,textarea:focus{outline:3px solid rgba(37,99,235,.18);border-color:#2563eb}.total{margin-top:18px;border-radius:18px;background:#eaf4ff;padding:18px;text-align:center}.total strong{display:block;font-size:40px;color:#0f5ca8}.submit{width:100%;margin-top:20px;border:0;border-radius:16px;background:#0f6cbf;color:white;padding:17px;font-size:17px;font-weight:900;cursor:pointer}.success{background:#eafaf0;border:1px solid #a7e0ba;color:#135c2f;border-radius:18px;padding:18px;margin-top:18px}.error{color:#b42318;font-size:13px;margin-top:6px}.recent{display:grid;gap:10px}.recent-row{display:flex;justify-content:space-between;gap:15px;padding:13px 0;border-bottom:1px solid #edf2f7}.muted{color:#64748b}.logout{display:inline}.logout button{border:0;background:none;color:#64748b;font-weight:800;cursor:pointer}@media(max-width:700px){.page{padding:14px}.hero{padding:23px}.hero h1{font-size:27px}.card{padding:18px}.grid{grid-template-columns:1fr}.numbers{grid-template-columns:repeat(2,minmax(0,1fr))}.numbers>div:last-child{grid-column:1/-1}.top{align-items:flex-start}.top-actions{display:grid;text-align:right;gap:4px}}
    </style>
</head>
<body>
<div class="page">
    <div class="top">
        <div class="brand">Fresh Fountain Church</div>
        <div class="top-actions">
            <a class="back" href="{{ url('/hub') }}">Dashboard</a>
            <form class="logout" method="POST" action="{{ route('filament.admin.auth.logout') }}">@csrf<button type="submit">Sign out</button></form>
        </div>
    </div>

    <section class="hero">
        <h1>Record attendance</h1>
        <p>Enter service totals only. No attendance figure is linked to an individual member.</p>
    </section>

    @if(session('attendance_saved'))
        <div class="success">
            <strong>Attendance saved successfully.</strong><br>
            {{ session('attendance_saved.service') }} · {{ session('attendance_saved.date') }} · Total {{ number_format(session('attendance_saved.total')) }}
        </div>
    @endif

    <form class="card" method="POST" action="{{ route('attendance.entry.store') }}" id="attendance-form">
        @csrf
        <div class="grid">
            <div>
                <label for="service_type_id">Service or event</label>
                <select id="service_type_id" name="service_type_id" required>
                    <option value="">Select a service or event</option>
                    @foreach($serviceTypes as $serviceType)
                        <option value="{{ $serviceType->id }}" @selected((string) old('service_type_id') === (string) $serviceType->id)>
                            {{ $serviceType->name }}
                        </option>
                    @endforeach
                </select>
                @error('service_type_id')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div>
                <label for="service_date">Service date</label>
                <input id="service_date" type="date" name="service_date" value="{{ old('service_date', now()->toDateString()) }}" required>
                @error('service_date')<div class="error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="numbers">
            @foreach(['men'=>'Men','women'=>'Women','children'=>'Children','visitors'=>'Visitors','online'=>'Online'] as $field=>$label)
                <div>
                    <label for="{{ $field }}">{{ $label }}</label>
                    <input class="attendance-number" id="{{ $field }}" type="number" inputmode="numeric" min="0" max="1000000" name="{{ $field }}" value="{{ old($field, 0) }}" required>
                    @error($field)<div class="error">{{ $message }}</div>@enderror
                </div>
            @endforeach
        </div>

        <div class="total"><span>Calculated total</span><strong id="attendance-total">0</strong></div>

        <div style="margin-top:18px">
            <label for="notes">Notes (optional)</label>
            <textarea id="notes" name="notes" rows="3" maxlength="3000">{{ old('notes') }}</textarea>
            @error('notes')<div class="error">{{ $message }}</div>@enderror
        </div>

        <button class="submit" type="submit">Save attendance</button>
    </form>

    @if($recent->isNotEmpty())
        <section class="card">
            <h2 style="margin-top:0">Recent attendance</h2>
            <div class="recent">
                @foreach($recent as $attendance)
                    <div class="recent-row">
                        <div><strong>{{ $attendance->service_name }}</strong><div class="muted">{{ $attendance->service_date->format('d M Y') }}</div></div>
                        <strong>{{ number_format($attendance->total) }}</strong>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</div>
<script>
const fields=[...document.querySelectorAll('.attendance-number')];
function updateTotal(){document.getElementById('attendance-total').textContent=fields.reduce((sum,input)=>sum+(parseInt(input.value||'0',10)||0),0).toLocaleString();}
fields.forEach(field=>field.addEventListener('input',updateTotal));updateTotal();
</script>
</body>
</html>
