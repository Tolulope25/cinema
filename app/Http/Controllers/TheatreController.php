<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Theatre; // Assuming this is the correct model

class TheatreController extends Controller
{
    public function createTheatre()
    {
        return view('admin.create-theatre');
    }

    public function storeTheatre(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:theatres', // Ensure consistency here
            'rows_count' => 'required|integer|min:1|max:26', // Max 26 for A-Z
            'seats_per_row' => 'required|integer|min:1',
            'capacity' => 'required|integer',
            'screen_type' => 'required|in:2D,3D,IMAX,4DX',
            'is_active' => 'boolean',
        ]);

        // Create a new theatre
        $theatre = Theatre::create($validated);

        // Redirect to a theater index page or any other page (or back with a success message)
        if (!$theatre) {
            return redirect('admin.create-theatre')->with('error', 'There was an error creating the Theatre');
        } else {
            return redirect('admin.create-theatre')->with('success', 'Theatre created successfully');
        }
    }

    // Show a specific theatre with seat map
    public function show(Theatre $theatre)
    {
        // Load the seat map and other details
        return view('theaters.show', [
            'theatre' => $theatre,
            'capacity' => $theatre->capacity,
            'seat_map' => $theatre->seat_map // Ensure this attribute exists in your model
        ]);
    }

    // Update an existing theatre
    public function editTheatre(Request $request, Theatre $theatre) // Correct type hinting
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:theatres,name,' . $theatre->id,
            'rows_count' => 'required|integer|min:1|max:26',
            'seats_per_row' => 'required|integer|min:1',
            'capacity' => 'required|integer',
            'screen_type' => 'required|in:2D,3D,IMAX,4DX',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);

        // Update theatre with new data
        $theatre->update($validated); // Use the retrieved instance

        return redirect()->route('admin.create-theatre')->with('success', 'Theatre updated successfully.');
    }

    // Delete a theatre from the database
    public function destroy(Theatre $theatre)
    {
        $theatre->delete();

        // Redirect to an index page with a success message
        return redirect()->route('theaters.index')->with('success', 'Theatre deleted successfully.');
    }

    // Display seat map for a specific theatre
    public function getSeatMap(Theatre $theatre)
    {
        return view('theaters.seat_map', [
            'theatre' => $theatre,
            'seat_map' => $theatre->seat_map,
            'capacity' => $theatre->capacity
        ]);
    }
}
