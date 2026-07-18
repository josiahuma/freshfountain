<?php

namespace App\Filament\Resources\Members\Pages;

use App\Filament\Resources\Members\MemberResource;
use App\Services\Access\MemberBackendAccessService;
use App\Support\Access\BackendAccess;
use App\Support\Access\BackendPermissions;
use Filament\Resources\Pages\CreateRecord;

class CreateMember extends CreateRecord
{
    protected static string $resource = MemberResource::class;

    protected ?bool $backendAccessEnabled = null;

    protected array $backendPermissions = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (BackendAccess::isSuperAdmin()) {
            $this->backendAccessEnabled = (bool) (
                $data['backend_access_enabled'] ?? false
            );
            $this->backendPermissions =
                BackendPermissions::collectFromForm($data);
        }

        unset($data['backend_access_enabled']);
        BackendPermissions::forgetFormFields($data);

        return $data;
    }

    protected function afterCreate(): void
    {
        if (
            BackendAccess::isSuperAdmin()
            && $this->backendAccessEnabled !== null
        ) {
            app(MemberBackendAccessService::class)->sync(
                $this->record,
                $this->backendAccessEnabled,
                $this->backendPermissions
            );
        }
    }
}
