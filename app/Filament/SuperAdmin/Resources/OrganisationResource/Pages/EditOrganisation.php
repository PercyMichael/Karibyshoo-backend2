<?php

namespace App\Filament\SuperAdmin\Resources\OrganisationResource\Pages;

use App\Filament\SuperAdmin\Resources\OrganisationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrganisation extends EditRecord
{
    protected static string $resource = OrganisationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
