<?php

namespace App\Filament\Widgets;

use App\Models\Guest;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class GuestChart extends ChartWidget
{
    protected static ?string $heading = 'Guests Checked In';

    protected function getData(): array
    {
        // Get the admin's organisations
        $user = Auth::user();
        $organisations = $user->organisation; // Admin's organisations

        // Initialize an array for guest counts by month
        $monthlyGuestCounts = array_fill(0, 12, 0);

        // Iterate through each month and count guests
        foreach (range(1, 12) as $month) {
            $monthlyGuestCounts[$month - 1] = Guest::whereIn('organisation_id', $organisations->pluck('id'))
                ->whereMonth('created_at', $month) // Filter guests by the month
                ->count();
        }

        // Return data for the chart
        return [
            'datasets' => [
                [
                    'label' => 'Guests Checked In',
                    'data' => $monthlyGuestCounts, // Data array with counts for each month
                    'backgroundColor' => '#22C55E', // Bar color
                    'borderWidth' => 0,  // No border for each bar
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], // Month labels
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // Bar chart to show guest counts for each month
    }
}
