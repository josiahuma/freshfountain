<?php

namespace App\Filament\Resources\Members\Pages;

use App\Filament\Resources\Members\MemberResource;
use App\Models\ChurchUnitMember;
use App\Models\Member;
use App\Services\Access\MemberBackendAccessService;
use App\Support\Access\BackendAccess;
use App\Support\Access\BackendPermissions;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditMember extends EditRecord
{
    protected static string $resource =
        MemberResource::class;

    protected array $selectedChurchUnitIds = [];

    protected ?bool $backendAccessEnabled = null;

    protected array $backendPermissions = [];

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),

            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(
        array $data
    ): array {
        /** @var Member $member */
        $member = $this->record;

        $member->loadMissing(
            'churchUnits'
        );

        $data['church_unit_ids'] =
            $member
                ->churchUnits
                ->pluck('id')
                ->map(
                    fn (
                        mixed $id
                    ): int =>
                        (int) $id
                )
                ->values()
                ->all();

        /*
         * Ensure the legacy primary unit is visible
         * in the checkbox list even when an older record
         * has not yet been added to the pivot table.
         */
        if (
            filled(
                $member->church_unit_id
            )
            && ! in_array(
                (int) $member->church_unit_id,
                $data['church_unit_ids'],
                true
            )
        ) {
            $data['church_unit_ids'][] =
                (int) $member->church_unit_id;
        }

        if (BackendAccess::isSuperAdmin()) {
            $data['backend_access_enabled'] =
                (bool) $member->user?->has_backend_access;

            $permissionValues =
                $member->user
                    ?->getDirectPermissions()
                    ->pluck('name')
                    ->values()
                    ->all() ?? [];

            $data = array_merge(
                $data,
                BackendPermissions::splitForForm($permissionValues)
            );
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(
        array $data
    ): array {
        if (BackendAccess::isSuperAdmin()) {
            $this->backendAccessEnabled = (bool) (
                $data['backend_access_enabled'] ?? false
            );
            $this->backendPermissions =
                BackendPermissions::collectFromForm($data);
        }

        unset($data['backend_access_enabled']);
        BackendPermissions::forgetFormFields($data);

        $this->selectedChurchUnitIds =
            collect(
                $data[
                    'church_unit_ids'
                ] ?? []
            )
                ->filter()
                ->map(
                    fn (
                        mixed $id
                    ): int =>
                        (int) $id
                )
                ->unique()
                ->values()
                ->all();

        unset(
            $data['church_unit_ids']
        );

        /*
         * If the selected primary unit was unticked,
         * choose the first remaining selected unit.
         */
        $primaryUnitId = filled(
            $data['church_unit_id']
            ?? null
        )
            ? (int)
                $data['church_unit_id']
            : null;

        if (
            $primaryUnitId
            && ! in_array(
                $primaryUnitId,
                $this
                    ->selectedChurchUnitIds,
                true
            )
        ) {
            $data['church_unit_id'] =
                $this
                    ->selectedChurchUnitIds[0]
                    ?? null;
        }

        /*
         * When a primary unit is selected from the dropdown,
         * automatically ensure it is also included among
         * all unit memberships.
         */
        if (
            filled(
                $data['church_unit_id']
                ?? null
            )
            && ! in_array(
                (int)
                $data['church_unit_id'],
                $this
                    ->selectedChurchUnitIds,
                true
            )
        ) {
            $this
                ->selectedChurchUnitIds[] =
                    (int)
                    $data['church_unit_id'];
        }

        if (
            empty(
                $this
                    ->selectedChurchUnitIds
            )
        ) {
            $data['church_unit_id'] =
                null;

            $data['leader_id'] =
                null;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        /** @var Member $member */
        $member = $this->record;

        DB::transaction(
            function () use (
                $member
            ): void {
                $selectedIds = collect(
                    $this
                        ->selectedChurchUnitIds
                )
                    ->map(
                        fn (
                            mixed $id
                        ): int =>
                            (int) $id
                    )
                    ->unique()
                    ->values();

                /*
                 * Remove memberships that have been unticked.
                 */
                ChurchUnitMember::query()
                    ->where(
                        'member_id',
                        $member->id
                    )
                    ->when(
                        $selectedIds
                            ->isNotEmpty(),
                        fn (
                            $query
                        ) =>
                            $query->whereNotIn(
                                'church_unit_id',
                                $selectedIds
                                    ->all()
                            )
                    )
                    ->when(
                        $selectedIds
                            ->isEmpty(),
                        fn (
                            $query
                        ) =>
                            $query
                    )
                    ->delete();

                /*
                 * Add newly ticked memberships while preserving
                 * any existing unit-specific leader assignments.
                 */
                foreach (
                    $selectedIds
                    as $churchUnitId
                ) {
                    $existing =
                        ChurchUnitMember::query()
                            ->where(
                                'member_id',
                                $member->id
                            )
                            ->where(
                                'church_unit_id',
                                $churchUnitId
                            )
                            ->first();

                    if ($existing) {
                        if (
                            $existing->status
                            !== 'active'
                        ) {
                            $existing->update([
                                'status' =>
                                    'active',

                                'left_at' =>
                                    null,

                                'updated_at' =>
                                    now(),
                            ]);
                        }

                        continue;
                    }

                    ChurchUnitMember::query()
                        ->create([
                            'member_id' =>
                                $member->id,

                            'church_unit_id' =>
                                $churchUnitId,

                            'assigned_leader_id' =>
                                (int)
                                    $churchUnitId
                                === (int)
                                    $member
                                        ->church_unit_id
                                    ? $member
                                        ->leader_id
                                    : null,

                            'status' =>
                                'active',

                            'source' =>
                                'admin',

                            'joined_at' =>
                                $member
                                    ->joined_at
                                ?? today(),

                            'left_at' =>
                                null,
                        ]);
                }

                /*
                 * Keep the primary unit and primary leader coherent.
                 */
                if (
                    filled(
                        $member
                            ->church_unit_id
                    )
                ) {
                    $primaryMembership =
                        ChurchUnitMember::query()
                            ->where(
                                'member_id',
                                $member->id
                            )
                            ->where(
                                'church_unit_id',
                                $member
                                    ->church_unit_id
                            )
                            ->first();

                    if (
                        $primaryMembership
                        && filled(
                            $member
                                ->leader_id
                        )
                    ) {
                        $primaryMembership
                            ->update([
                                'assigned_leader_id' =>
                                    $member
                                        ->leader_id,
                            ]);
                    }
                }

                $member->unsetRelation(
                    'churchUnits'
                );
            }
        );

        if (
            BackendAccess::isSuperAdmin()
            && $this->backendAccessEnabled !== null
        ) {
            app(MemberBackendAccessService::class)->sync(
                $member->fresh('user'),
                $this->backendAccessEnabled,
                $this->backendPermissions
            );
        }
    }
}