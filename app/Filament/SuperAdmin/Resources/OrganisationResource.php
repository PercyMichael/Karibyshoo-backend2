<?php

namespace App\Filament\SuperAdmin\Resources;


use App\Filament\SuperAdmin\Resources\OrganisationResource\Pages;
use App\Filament\SuperAdmin\Resources\OrganisationResource\RelationManagers\GuestsRelationManager;
use App\Models\Organisation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


class OrganisationResource extends Resource
{
    protected static ?string $model = Organisation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),


                // Counting the total number of guests in the organization
                Tables\Columns\TextColumn::make('guests_count')
                    ->label('Total Guests')
                    ->getStateUsing(function ($record) {
                        // Count the number of guests in the current record (user or organization)
                        return $record->guests()->count(); // Count the guests related to the user or organization
                    })
                    ->badge()
                    ->sortable(),


                // Counting the total number of users in the organization
                Tables\Columns\TextColumn::make('users_count')
                    ->label('Total Users')
                    ->getStateUsing(function ($record) {
                        // If counting users in an organization, use the relationship
                        return $record->users()->count(); // Count the users related to the organization
                    })
                    ->sortable(),


                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrganisations::route('/'),
            'create' => Pages\CreateOrganisation::route('/create'),
            'edit' => Pages\EditOrganisation::route('/{record}/edit'),
        ];
    }
}
