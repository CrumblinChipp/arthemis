<?php
namespace App\Http\Controllers;

use App\Models\WasteEntry;
use Illuminate\Http\Request;

class DataController extends Controller{
    public function showData(Request $request)
    {
        $query = WasteEntry::query()->with('building'); // Eager load building relationship

        // Search by Building Name
        if ($request->search) {
            $query->whereHas('building', function ($q) use ($request) {
                // Assuming the Building model has a 'name' column
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        // Date filter
        if ($request->date) {
            $query->whereDate('date', $request->date);
        }

        // Waste Type Filter (for specific waste types)
        if ($request->waste_type) {
            $column = $request->waste_type . '_kg'; // e.g., 'residual_kg'
            // Filter to only show entries where the selected waste type has a recorded weight > 0
            // This assumes the waste type names in the URL match the column prefix
            if (in_array($request->waste_type, ['residual', 'recyclable', 'biodegradable', 'infectious'])) {
                 $query->where($column, '>', 0);
            }
        }

        // Pagination
        // Use an array of valid per_page values to prevent injection
        $validPerPages = [20, 50, 100];
        $perPage = (int)$request->input('per_page', 20);
        if (!in_array($perPage, $validPerPages)) {
            $perPage = 20; // Default if invalid value passed
        }


        $wastes = $query->orderBy('date', 'desc')->paginate($perPage)->appends($request->query()); 
        // ->appends() keeps the filters when paginating

        return view('sections.data', compact('wastes'));
    }


    // store, update, and destroy methods remain largely the same, 
    // but the validation needs to be adjusted to match the model's fields exactly.

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'building_id' => 'required|exists:buildings,id', // Should be building_id from the model
            'residual_kg' => 'required|numeric|min:0',
            'recyclable_kg' => 'required|numeric|min:0',
            'biodegradable_kg' => 'required|numeric|min:0',
            'infectious_kg' => 'required|numeric|min:0',
            // 'is_read' => 'sometimes|boolean', // Assuming this is set on creation
        ]);

        WasteEntry::create($request->only([
            'date', 'building_id', 'residual_kg', 'recyclable_kg', 
            'biodegradable_kg', 'infectious_kg', 'is_read'
        ]));

        return back()->with('success', 'Waste record added!');
    }


    public function update(Request $request, WasteEntry $waste)
    {
        $request->validate([
            'date' => 'required|date',
            'building_id' => 'required|exists:buildings,id', // Should be building_id
            'residual_kg' => 'required|numeric|min:0',
            'recyclable_kg' => 'required|numeric|min:0',
            'biodegradable_kg' => 'required|numeric|min:0',
            'infectious_kg' => 'required|numeric|min:0',
        ]);
        
        $waste->update($request->only([
            'date', 'building_id', 'residual_kg', 'recyclable_kg', 
            'biodegradable_kg', 'infectious_kg', 'is_read'
        ]));

        return back()->with('success', 'Waste updated!');
    }

    // destroy method is fine as is
    public function destroy(WasteEntry $waste)
    {
        $waste->delete();

        return back()->with('success', 'Waste deleted!');
    }
}