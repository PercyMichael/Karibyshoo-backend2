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
        $recorded_by = $request->input('user_id'); // Get the user ID from the request
        $organisation_id = $request->input('organisation_id'); // Get organisation ID from the request
        $search = $request->input('search'); // Get the search term from the request


        // Prepare the query to get check-ins
        $query = Guest::with('organisation'); // Load the organisation relationship

        // Filter by user ID if provided
        if ($recorded_by) {
            $query->where('user_id', $recorded_by);
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
        $guests = $query->get();

        // Count check-ins based on 'left' field
        foreach ($guests as $guest) {
            if (is_null($guest->left)) {
                $stillIn++;
            } else {
                $left++;
            }
        }

        return response()->json([
            'count' => $guests->count(),
            'left' => $left,
            'stillIn' => $stillIn,  // Count of check-ins still present
            'guests' => $guests  // The check-in records with organisation info
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
            $guest = Guest::create($request->all());
            return response()->json(['message' => 'Guest created successfully', 'data' => $guest], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Guest $guest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Guest $guest)
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
        $guest = Guest::find($id);
        if (!$guest) {
            return response()->json(['message' => 'Guest not found.'], 404);
        }

        // Update the 'left' field with the current server timestamp
        $guest->left = now(); // This will automatically use the current date and time
        $guest->save();

        // Return a 200 OK response with the updated check-in
        return response()->json([
            'message' => 'Guest updated successfully!',
            'guest' => $guest
        ], 200);
    }


    public function search(Request $request)
    {
        $searchTerm = $request->input('search');
        $organisationId = $request->input('organisation_id');
        $recordedBy = $request->input('user_id');

        // Get the latest check-in for each unique phone by first grouping by phone
        $guests = Guest::with('organisation') // Ensure organisation relationship is loaded
            ->where('name', 'like', "%{$searchTerm}%")
            ->where('organisation_id', $organisationId)
            ->where('user_id', $recordedBy)
            ->orderBy('created_at', 'desc') // Sort by latest entries
            ->get()
            ->unique('phone') // Filter unique check-ins by phone
            ->values(); // Reset the collection indices


        $left = 0;
        $stillIn = $guests->whereNull('left')->count();

        return response()->json([
            'message' => "Searched for '{$searchTerm}'",
            'count' => $guests->count(),
            'left' => $left,
            'stillIn' => $stillIn,
            'guests' => $guests
        ], 200);
    }






    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Guest $guest)
    {
        //
    }
}
