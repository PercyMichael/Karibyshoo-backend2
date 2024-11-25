<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Organisation;
use App\Models\Team;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant;

class RegisterTeam extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Register orgainsation';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                TextInput::make('location')->required(),
                // ...
            ]);
    }

    protected function handleRegistration(array $data): Organisation
    {
        $organisation = Organisation::create($data);

        $organisation->users()->attach(auth()->user());

        return $organisation;
    }
}
