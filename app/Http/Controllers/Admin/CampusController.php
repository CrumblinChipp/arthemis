<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Campus;
use App\Models\Building;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
            'map' => $mapPath,
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

    public function editPage($id)
    {
        $campus = Campus::with('buildings')->findOrFail($id);
        // Note: Assuming the view is located at resources/views/admin/edit-campus.blade.php
        return view('admin.edit-campus', compact('campus'));
    }

    public function update(Request $request, Campus $campus) // Using Route Model Binding here is cleaner
    {
        // 1. Validation
        $validated = $request->validate([
            'campus_name' => 'required|string|max:255|unique:campuses,name' . $campus->id,
            'buildings' => 'required|array|min:1',
            'buildings.*' => 'required|string|max:255',
            'campus_map' => 'nullable|image|max:2048',
        ]);

        // 2. Handle Campus Data and Map
        $campus->name = $validated['name'];

        if ($request->hasFile('map')) {
            // Delete old map if it exists
            if ($campus->map) {
                Storage::disk('public')->delete($campus->map);
            }
            // Store new map
            $path = $request->file('map')->store('maps', 'public');
            $campus->map = $path; // <-- CORRECTED: Changed $campus->maps to $campus->map
        }
        
        $campus->save();

        // 3. Handle Buildings (CRUD)
        if ($request->has('buildings')) {
            $submittedBuildings = $validated['buildings'];
            $existingBuildingIds = $campus->buildings->pluck('id')->toArray();
            $currentBuildingIds = [];

            foreach ($submittedBuildings as $buildingIdKey => $buildingName) {
                if (is_numeric($buildingIdKey)) {
                    // Update Existing Building
                    Building::where('id', $buildingIdKey)
                            ->where('campus_id', $campus->id)
                            ->update(['name' => $buildingName]);
                    $currentBuildingIds[] = (int)$buildingIdKey;

                } elseif (str_starts_with($buildingIdKey, 'new_')) {
                    // Create New Building
                    Building::create([
                        'name' => $buildingName,
                        'campus_id' => $campus->id,
                    ]);
                }
            }

            // Delete Removed Buildings
            $buildingsToDelete = array_diff($existingBuildingIds, $currentBuildingIds);
            if (!empty($buildingsToDelete)) {
                Building::destroy($buildingsToDelete);
            }
        } else {
            // If the buildings field is submitted empty, delete all current buildings
            $campus->buildings()->delete();
        }

        return redirect()->back()->with('success', 'Campus updated successfully!');
    }

    /**
     * Remove the specified resource (Campus) from storage, including related data.
     *
     * @param  \App\Models\Campus  $campus (Route Model Binding)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Campus $campus)
    {
        try {
            DB::beginTransaction();

            // 1. Delete associated map file
            if ($campus->map) {
                Storage::disk('public')->delete($campus->map);
            }
            
            // 2. Delete the Campus and related data
            // NOTE: If you have configured onDelete('cascade') in your migrations, 
            // deleting the campus will automatically delete its Buildings and associated Waste Entries.
            // Otherwise, you must uncomment these lines:
            // $campus->buildings()->delete();
            // $campus->wasteEntries()->delete();
            
            $campus->delete();

            DB::commit();

            // Assuming you have an admin index/list route named 'admin.campus.index' or similar
            return redirect()->route('admin.dashboard')
                            ->with('success', 'Campus "' . $campus->name . '" deleted permanently.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Handle errors, possibly log the exception
            return redirect()->back()
                            ->with('error', 'Error deleting campus: ' . $e->getMessage());
        }
    }
}