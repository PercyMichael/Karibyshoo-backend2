<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Guest;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Import the Log facade

class GenderChart extends ChartWidget
{
    protected static ?string $heading = 'Gender Distribution of Guests';


    protected function getData(): array
    {
        // Get the logged-in admin user
        $user = User::find(Auth::id());

        // Get the organizations the admin belongs to
        $organisations = $user->organisation;


        // Get the total number of male guests across these organizations
        $maleGuests = Guest::whereIn('organisation_id', $organisations->pluck('id'))
            ->where('gender', 'Male') // Filter by male gender
            ->count();

        // Get the total number of female guests across these organizations
        $femaleGuests = Guest::whereIn('organisation_id', $organisations->pluck('id'))
            ->where('gender', 'Female') // Filter by female gender
            ->count();

        // dd($organisations->pluck('id'));
        // dd($organisations, $maleGuests, $femaleGuests);


        return [
            'datasets' => [
                [
                    'label' => 'Guests Checked In',
                    'data' => [$maleGuests, $femaleGuests], // Male and Female counts
                    'backgroundColor' => ['#36A2EB', '#FF6384'], // Color for male and female
                    'borderWidth' => 0,  // No border for each bar
                ],
            ],
            'labels' => ['Male', 'Female'], // Labels for the chart
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // Set the chart type to bar
    }
}
