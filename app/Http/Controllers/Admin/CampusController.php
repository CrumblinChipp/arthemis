<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Campus;
use App\Models\Building;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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

        // Return Campus Data
        return response()->json([
            'success' => true,
            'message' => 'Campus and buildings added successfully.',
            'campus' => $campus,
        ]);
    }

    public function editPage($id)
    {
        $campus = Campus::with('buildings')->findOrFail($id);
        return view('admin.edit-campus', compact('campus'));
    }

    public function update(Request $request, Campus $campus)
    {
    $validated = $request->validate([
            'campus_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('campuses', 'name')->ignore($campus->id),
            ],
            'buildings' => 'required|array|min:1',
            'buildings.*' => 'required|string|max:255',
            'campus_map' => 'nullable|image|max:2048',
        ]);

        // 1. Handle Campus Data and Map
        $campus->name = $validated['campus_name'];

    if ($request->hasFile('campus_map')) {
        
        $path = $request->file('campus_map')->store('maps', 'public');
        $campus->map = $path; 
    }
            
        $campus->save();

        // 2. Handle Buildings (CRUD)
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

    public function destroy(Campus $campus)
    {
        try {
            DB::beginTransaction();
            if ($campus->map) {
                Storage::disk('public')->delete($campus->map);
            }
        
            $campus->delete();

            DB::commit();

            return redirect()->route('dashboard')
                            ->with('success', 'Campus "' . $campus->name . '" deleted permanently.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                            ->with('error', 'Error deleting campus: ' . $e->getMessage());
        }
    }
}