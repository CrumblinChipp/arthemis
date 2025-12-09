<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WasteEntry;

class WasteEntryController extends Controller
{
    public function store(Request $request)
    {
    $validated = $request->validate([

        'name' => 'required|string|max:255',
        'campus_id' => 'required|exists:campuses,id',
        'building_id' => 'required|exists:buildings,id',
        'biodegradable_kg' => 'required|numeric|min:0',
        'recyclable_kg' => 'required|numeric|min:0',
        'residual_kg' => 'required|numeric|min:0',
        'infectious_kg' => 'required|numeric|min:0',
    ]);

    WasteEntry::create([
        'user_id' => 1,
        'date' => now()->toDateString(),
        'building_id' => $validated['building_id'],
        
        'biodegradable' => $validated['biodegradable_kg'], 
        'recyclable' => $validated['recyclable_kg'],
        'residual' => $validated['residual_kg'],
        'infectious' => $validated['infectious_kg'],
    ]);

        return response()->json(['success' => true, 'data' => $request->all()]);
    }

}