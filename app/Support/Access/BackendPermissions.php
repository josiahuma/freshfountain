<?php

namespace App\Support\Access;

final class BackendPermissions
{
    public const MODULES = [
        'website' => 'Website CMS',
        'blog' => 'Blog',
        'careers' => 'Careers',
        'calendar' => 'Calendar',
        'learning' => 'Learning Management',
        'members' => 'Members',
        'church_units' => 'Church Units',
        'leaders' => 'Leaders',
        'unit_requests' => 'Unit Membership Requests',
        'finance' => 'Finance',
        'attendance' => 'Attendance',
        'sms' => 'SMS',
        'reports' => 'Reports',
    ];

    public const GROUPS = [
        'website' => ['label' => 'Website & Recruitment', 'modules' => ['website', 'blog', 'careers']],
        'calendar' => ['label' => 'Church Calendar', 'modules' => ['calendar']],
        'crm' => ['label' => 'Church CRM', 'modules' => ['members', 'church_units', 'leaders', 'unit_requests']],
        'learning' => ['label' => 'Learning Management', 'modules' => ['learning']],
        'finance' => ['label' => 'Finance', 'modules' => ['finance']],
        'attendance' => ['label' => 'Attendance', 'modules' => ['attendance']],
        'future' => ['label' => 'Future Modules', 'modules' => ['sms', 'reports']],
    ];

    public const VIEW_DONOR_IDENTITIES = 'finance.view_donor_identities';
    public const ATTENDANCE_ENTRY = 'attendance.entry';
    public const ATTENDANCE_ANALYTICS = 'attendance.analytics';

    public static function view(string $module): string { return $module.'.view'; }
    public static function manage(string $module): string { return $module.'.manage'; }

    public static function all(): array
    {
        $permissions = [];
        foreach (array_keys(self::MODULES) as $module) {
            $permissions[] = self::view($module);
            $permissions[] = self::manage($module);
        }
        $permissions[] = self::VIEW_DONOR_IDENTITIES;
        $permissions[] = self::ATTENDANCE_ENTRY;
        $permissions[] = self::ATTENDANCE_ANALYTICS;
        return $permissions;
    }

    public static function assignmentOptions(): array
    {
        $options = [];
        foreach (self::MODULES as $module => $label) {
            $options[self::view($module)] = $label.' — View';
            $options[self::manage($module)] = $label.' — Manage';
        }
        $options[self::VIEW_DONOR_IDENTITIES] = 'Finance — View donor identities';
        $options[self::ATTENDANCE_ENTRY] = 'Attendance — Submit figures from usher form';
        $options[self::ATTENDANCE_ANALYTICS] = 'Attendance — View analytics';
        return $options;
    }

    public static function groupOptions(string $group): array
    {
        $definition = self::GROUPS[$group] ?? null;
        if (! $definition) return [];
        $options = [];
        foreach ($definition['modules'] as $module) {
            $label = self::MODULES[$module];
            $options[self::view($module)] = $label.' — View';
            $options[self::manage($module)] = $label.' — Manage';
        }
        if ($group === 'finance') $options[self::VIEW_DONOR_IDENTITIES] = 'View donor identities';
        if ($group === 'attendance') {
            $options[self::ATTENDANCE_ENTRY] = 'Submit figures from usher form';
            $options[self::ATTENDANCE_ANALYTICS] = 'View attendance analytics';
        }
        return $options;
    }

    public static function formField(string $group): string { return 'backend_permissions_'.$group; }

    public static function collectFromForm(array $data): array
    {
        return collect(array_keys(self::GROUPS))
            ->flatMap(fn (string $group): array => $data[self::formField($group)] ?? [])
            ->filter(fn (mixed $permission): bool => is_string($permission) && in_array($permission, self::all(), true))
            ->unique()->values()->all();
    }

    public static function splitForForm(array $permissions): array
    {
        $values = [];
        foreach (array_keys(self::GROUPS) as $group) {
            $allowed = array_keys(self::groupOptions($group));
            $values[self::formField($group)] = array_values(array_intersect($permissions, $allowed));
        }
        return $values;
    }

    public static function forgetFormFields(array &$data): void
    {
        foreach (array_keys(self::GROUPS) as $group) unset($data[self::formField($group)]);
    }
}
