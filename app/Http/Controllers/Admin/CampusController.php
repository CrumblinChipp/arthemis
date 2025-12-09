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

    public function update(Request $request, $id)
    {
        $campus = Campus::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'buildings' => 'array',
            'buildings.*' => 'string|max:255',
            'map' => 'nullable|image|max:2048',
        ]);

        $campus->name = $request->name;

        // Update buildings
        if ($request->has('buildings')) {
            foreach ($request->buildings as $buildingId => $buildingName) {
                $building = Building::find($buildingId);
                if ($building) {
                    $building->update(['name' => $buildingName]);
                }
            }
        }

        // Handle map upload
        if ($request->hasFile('map')) {
            $path = $request->file('map')->store('maps', 'public');
            $campus->map = $path;
        }

        $campus->save();

        return redirect()->back()->with('success', 'Campus updated successfully!');
    }

    public function editPage($id)
    {
        $campus = Campus::with('buildings')->findOrFail($id);
        // Note: Assuming the view is located at resources/views/admin/edit-campus.blade.php
        return view('admin.edit-campus', compact('campus'));
    }


}
