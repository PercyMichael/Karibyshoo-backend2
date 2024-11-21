<?php


namespace App\Filament\Widgets;

use App\Models\Guest;
use App\Models\User;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Retrieve the currently authenticated user
        $user = User::find(Auth::id());

        // Get all the organizations the user belongs to
        $organisations = $user->organisation;

        // Filter organizations owned by admin (where the current user is an admin)
        $adminOrganisations = $organisations->filter(function ($organisation) use ($user) {
            // Check if the authenticated user is an admin of the organization
            return $organisation->users->contains(fn($u) => $u->id === $user->id && $u->role === 'admin');
        });

        // Count the total number of users with 'staff' role in these admin-owned organizations
        $totalStaff = $adminOrganisations->reduce(function ($carry, $organisation) {
            return $carry + $organisation->users->where('role', 'staff')->count();
        }, 0);


        // Get the total number of guests across all these organizations
        $totalGuests = Guest::whereIn('organisation_id', $organisations->pluck('id'))->count();

        return [
            Stat::make('Unique guests', $totalGuests) // Display the total guest count
                ->description('Total guests in all organizations')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
            Stat::make('Organisations', $adminOrganisations->count())
                ->description('7% decrease')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            Stat::make('Staff', $totalStaff)
                ->description('Total staff in admin-owned organisations')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('info'),
        ];
    }
}
