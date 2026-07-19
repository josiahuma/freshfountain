<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceServiceType;
use App\Support\Access\BackendAccess;
use App\Support\Access\BackendPermissions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AttendanceEntryController extends Controller
{
    public function create(Request $request): View
    {
        $this->authorizeEntry($request);

        return view('attendance.entry', [
            'serviceTypes' => AttendanceServiceType::query()->active()->ordered()->get(),
            'recent' => Attendance::query()->latest('service_date')->latest('id')->limit(5)->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeEntry($request);

        $validated = $request->validate([
            'service_type_id' => [
                'required',
                'integer',
                Rule::exists('attendance_service_types', 'id')->where('is_active', true),
            ],
            'service_date' => ['required', 'date'],
            'men' => ['required', 'integer', 'min:0', 'max:1000000'],
            'women' => ['required', 'integer', 'min:0', 'max:1000000'],
            'children' => ['required', 'integer', 'min:0', 'max:1000000'],
            'visitors' => ['required', 'integer', 'min:0', 'max:1000000'],
            'online' => ['required', 'integer', 'min:0', 'max:1000000'],
            'notes' => ['nullable', 'string', 'max:3000'],
        ]);

        $serviceType = AttendanceServiceType::query()->findOrFail($validated['service_type_id']);

        $duplicate = Attendance::query()
            ->whereDate('service_date', $validated['service_date'])
            ->where(function ($query) use ($serviceType): void {
                $query->where('service_type_id', $serviceType->id)
                    ->orWhereRaw('LOWER(TRIM(service_name)) = ?', [mb_strtolower($serviceType->name)]);
            })
            ->exists();

        if ($duplicate) {
            return back()
                ->withInput()
                ->withErrors([
                    'service_type_id' => 'Attendance already exists for this service and date. Ask an administrator to edit the existing record.',
                ]);
        }

        $attendance = Attendance::query()->create([
            ...$validated,
            'service_name' => $serviceType->name,
        ]);

        return redirect()
            ->route('attendance.entry.create')
            ->with('attendance_saved', [
                'service' => $attendance->service_name,
                'date' => $attendance->service_date->format('d M Y'),
                'total' => $attendance->total,
            ]);
    }

    private function authorizeEntry(Request $request): void
    {
        $user = $request->user();

        abort_unless(
            $user && (
                BackendAccess::isSuperAdmin($user)
                || $user->can(BackendPermissions::ATTENDANCE_ENTRY)
                || $user->can(BackendPermissions::manage('attendance'))
            ),
            403
        );
    }
}
