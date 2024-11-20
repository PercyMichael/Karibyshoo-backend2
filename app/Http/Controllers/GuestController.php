<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;
use Carbon\Carbon; // Don't forget to import Carbon


class GuestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Initialize counters
        $left = 0;
        $stillIn = 0;

        // Get the month and day from the JSON request body
        $month = $request->input('month');
        $day = $request->input('day');
        $recorded_by = $request->input('recorded_by'); // Get the user ID from the request
        $organisation_id = $request->input('organisation_id'); // Get organisation ID from the request
        $search = $request->input('search'); // Get the search term from the request


        // Prepare the query to get check-ins
        $query = Guest::with('organisation'); // Load the organisation relationship

        // Filter by user ID if provided
        if ($recorded_by) {
            $query->where('recorded_by', $recorded_by);
        }

        // Filter by organisation ID if provided
        if ($organisation_id) {
            $query->whereHas('organisation', function ($q) use ($organisation_id) {
                $q->where('id', $organisation_id);
            });
        }

        // If month and day are provided, filter by those parameters
        if ($month && $day) {
            $query->whereMonth('created_at', $month)
                ->whereDay('created_at', $day);
        } else {
            // If no month and day are provided, get today's check-ins
            $today = Carbon::today();
            $query->whereDate('created_at', $today);
        }


        // Execute the query
        $Guests = $query->get();

        // Count check-ins based on 'left' field
        foreach ($Guests as $Guest) {
            if (is_null($Guest->left)) {
                $stillIn++;
            } else {
                $left++;
            }
        }

        return response()->json([
            'count' => $Guests->count(),
            'left' => $left,
            'stillIn' => $stillIn,  // Count of check-ins still present
            'Guests' => $Guests  // The check-in records with organisation info
        ], 200);
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'regex:/^[a-zA-Z\s]+$/'],
                'gender' => ['required', 'in:Male,Female'],
                'phone' => 'required|string',
                'email' => 'required|email',
                'nin' => ['required', 'regex:/^[a-zA-Z0-9]+$/'],
                'nationality' => ['required', 'regex:/^[a-zA-Z]+$/'],
                'reason' => 'required|string',
                'temperature' => 'required|numeric',
                'organisation_id' => 'required|exists:organisations,id',
                'user_id' => 'required|exists:users,id',
            ]);

            // Create Guest
            $Guest = Guest::create($request->all());
            return response()->json(['message' => 'Guest created successfully', 'data' => $Guest], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Guest $Guest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Guest $Guest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)

    {
        // Validate the ID to ensure it's an integer
        if (!is_numeric($id) || intval($id) != $id) {
            return response()->json(['message' => 'Invalid check-in ID.'], 400); // Bad request if ID is not a valid integer
        }

        // Find the check-in by ID or return a 404
        $Guest = Guest::find($id);
        if (!$Guest) {
            return response()->json(['message' => 'Check-in not found.'], 404);
        }

        // Update the 'left' field with the current server timestamp
        $Guest->left = now(); // This will automatically use the current date and time
        $Guest->save();

        // Return a 200 OK response with the updated check-in
        return response()->json([
            'message' => 'Check-in updated successfully!',
            'Guest' => $Guest
        ], 200);
    }


    public function search(Request $request)
    {
        $searchTerm = $request->input('search');
        $organisationId = $request->input('organisation_id');
        $recordedBy = $request->input('recorded_by');

        // Get the latest check-in for each unique phone by first grouping by phone
        $Guests = Guest::with('organisation') // Ensure organisation relationship is loaded
            ->where('name', 'like', "%{$searchTerm}%")
            ->where('organisation_id', $organisationId)
            ->where('recorded_by', $recordedBy)
            ->orderBy('created_at', 'desc') // Sort by latest entries
            ->get()
            ->unique('phone') // Filter unique check-ins by phone
            ->values(); // Reset the collection indices


        $left = 0;
        $stillIn = $Guests->whereNull('left')->count();

        return response()->json([
            'message' => "Searched for '{$searchTerm}'",
            'count' => $Guests->count(),
            'left' => $left,
            'stillIn' => $stillIn,
            'Guests' => $Guests
        ], 200);
    }






    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Guest $Guest)
    {
        //
    }
}
