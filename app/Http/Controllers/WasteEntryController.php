<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WasteEntry;
use Carbon\Carbon;

class WasteEntryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'campus_id'         => 'required|exists:campuses,id',
            'building_id'       => 'required|exists:buildings,id',
            // Validation rules are based on form input names, which are the base names
            'biodegradable'     => 'required|numeric|min:0',
            'recyclable'        => 'required|numeric|min:0',
            'residual'          => 'required|numeric|min:0',
            'infectious'        => 'required|numeric|min:0',
        ]);

        WasteEntry::create([
            'date'             => now()->toDateString(),
            'building_id'      => $validated['building_id'],
            // ADJUSTMENT: Map form fields to database columns with  suffix
            'biodegradable' => $validated['biodegradable'],
            'recyclable'    => $validated['recyclable'],
            'residual'      => $validated['residual'],
            'infectious'    => $validated['infectious'],
        ]);

        return response()->json(['success' => true]);
    }

}