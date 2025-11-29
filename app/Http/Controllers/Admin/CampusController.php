<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Campus;
use App\Models\Building;

class CampusController extends Controller
{
    public function store(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'campus_name' => 'required|string|max:255',
            'buildings' => 'required|array|min:1',
            'buildings.*' => 'required|string|max:255',
            'campus_map' => 'nullable|image|max:2048',
        ]);

        // Handle map upload
        $mapPath = null;
        if ($request->hasFile('campus_map')) {
            $mapPath = $request->file('campus_map')->store('maps', 'public');
        }

        // Create campus
        $campus = Campus::create([
            'name' => $validated['campus_name'],
            'map' => $mapPath, // adjusted to match your column
        ]);

        // Create buildings
        foreach ($validated['buildings'] as $buildingName) {
            Building::create([
                'name' => $buildingName,
                'campus_id' => $campus->id,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Campus and buildings added successfully.',
            'campus' => $campus,
        ]);
    }
}
