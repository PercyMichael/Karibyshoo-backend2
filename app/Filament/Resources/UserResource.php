<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\Organisation;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRules;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\RelationManagers\GuestsRelationManager;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->unique()
                    ->required(),
                Forms\Components\Select::make('role')
                    ->options(["admin" => "Admin", "staff" => "Staff"])
                    ->required(),
                // Forms\Components\Select::make('organisation_id')
                //     ->label('Organisation')
                //     ->options(function () {
                //         // Get the authenticated user's organisations
                //         return Organisation::whereHas('users', function (Builder $query) {
                //             $query->where('user_id', auth()->id()); // Replace with appropriate user ID check
                //         })->pluck('name', 'id');
                //     })
                //     ->required(),
                Forms\Components\TextInput::make('password')
                    ->required(fn($get) => !$get('id')) // Only required during creation
                    ->password()
                    ->minLength(6)
                    ->same('password_confirmation')
                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                    ->visible(fn($livewire) => $livewire instanceof CreateUser)
                    ->rule(PasswordRules::min(6)->letters()->numbers())
                    ->label('Password'),

                Forms\Components\TextInput::make('password_confirmation')
                    ->required(fn($get) => !$get('id')) // Only required during creation
                    ->password()
                    ->label('Confirm Password')
                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                    ->visible(fn($livewire) => $livewire instanceof CreateUser)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->searchable(),
                Tables\Columns\TextColumn::make('organisation.name')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
            GuestsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
