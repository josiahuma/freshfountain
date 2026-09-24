<?php

namespace App\Filament\Resources\Members\Pages;

use App\Filament\Resources\Members\MemberResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMembers extends ListRecords
{
    protected static string $resource = MemberResource::class;
    protected string $view = 'filament.resources.members.pages.list-members';

    public bool $dataProtectionAccepted = false;

    public function mount(): void
    {
        parent::mount();
        $this->dataProtectionAccepted = (bool) session('members_data_protection_acknowledged', false);
    }

    public function acceptDataProtection(): void
    {
        session()->put('members_data_protection_acknowledged', true);
        $this->dataProtectionAccepted = true;
    }

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
