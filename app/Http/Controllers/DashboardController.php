<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campus;
use App\Models\WasteEntry;
use App\Models\Building;
use Carbon\Carbon;
use DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        /* -----------------------------------------------------
         * 1. DETERMINE CAMPUS FILTER
         * --------------------------------------------------- */

        $userCampusId = auth()->check()
            ? auth()->user()->campus_id
            : Campus::first()->id;

        $selectedCampus = $request->input('campus', $userCampusId);

        $campuses = Campus::select('id', 'name')->get();

        $campus = null;
        $buildings = collect();
        if ($selectedCampus) {
            $campus = Campus::with('buildings')->find($selectedCampus);
            if ($campus) {
                $buildings = $campus->buildings;
            }
        }


        /* -----------------------------------------------------
         * 2. GET BUILDINGS OF SELECTED CAMPUS
         * --------------------------------------------------- */

        // Use the $buildings collection from step 1 to avoid redundant query
        $buildingIds = $buildings->pluck('id');


        /* -----------------------------------------------------
         * 3. DATE RANGE FILTER
         * --------------------------------------------------- */

        $range = $request->input('days', 7);
        if (!in_array($range, [7, 30, 90])) {
            $range = 7;
        }

        $startDate = Carbon::now()->subDays($range - 1)->toDateString();


        /* -----------------------------------------------------
         * 4. GET DATES WITHIN RANGE
         * --------------------------------------------------- */

        $dates = WasteEntry::whereIn('building_id', $buildingIds)
            ->where('date', '>=', $startDate)
            ->select('date')
            ->distinct()
            ->orderBy('date')
            ->pluck('date')
            ->toArray();

        if (empty($dates)) {
            $dates = collect(range($range - 1, 0))
                ->map(fn($i) => Carbon::now()->subDays($i)->toDateString())
                ->toArray();
        }


        /* -----------------------------------------------------
         * 5. TOTAL WASTE PER DATE
         * --------------------------------------------------- */

        $totalsPerDate = [];

        foreach ($dates as $d) {
            $totalsPerDate[] = WasteEntry::whereIn('building_id', $buildingIds)
                ->where('date', $d)
                ->selectRaw('SUM(residual + recyclable + biodegradable + infectious) AS total')
                ->value('total') ?? 0;
        }


        /* -----------------------------------------------------
         * 6. SUMMARY STATS
         * --------------------------------------------------- */

        $highestKg = max($totalsPerDate);
        $lowestKg = min($totalsPerDate);
        $avgKg = count($totalsPerDate)
            ? round(array_sum($totalsPerDate) / count($totalsPerDate), 1)
            : 0;

        $highestDate = $dates[array_search($highestKg, $totalsPerDate)] ?? null;
        $lowestDate  = $dates[array_search($lowestKg, $totalsPerDate)] ?? null;


        /* -----------------------------------------------------
         * 7. WASTE COMPOSITION (ALL BUILDINGS)
         * --------------------------------------------------- */

        $composition = WasteEntry::whereIn('building_id', $buildingIds)
            ->where('date', '>=', $startDate)
            ->selectRaw('
                SUM(biodegradable) as biodegradable,
                SUM(residual)      as residual,
                SUM(recyclable)    as recyclable,
                SUM(infectious)    as infectious
            ')
            ->first()
            ->toArray();


        /* -----------------------------------------------------
         * 8. PER-BUILDING DAILY WASTE
         * --------------------------------------------------- */

        $buildingDatasets = [];

        foreach ($buildings as $building) {
            $dailyTotals = [];

            foreach ($dates as $d) {
                $dailyTotals[] = WasteEntry::where('building_id', $building->id)
                    ->where('date', $d)
                    // ADJUSTMENT: Use  suffixes
                    ->selectRaw('SUM(residual + recyclable + biodegradable + infectious) AS total')
                    ->value('total') ?? 0;
            }

            $buildingDatasets[] = [
                'name' => $building->name,
                'totals' => $dailyTotals
            ];
        }

        /* -----------------------------------------------------
         * 9. GATHERING WASTE ENTRIES
         * --------------------------------------------------- */
        $query = WasteEntry::query()->with('building');

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

        if ($request->waste_type) {
            $column = $request->waste_type;

                if (in_array($request->waste_type, ['residual', 'recyclable', 'biodegradable', 'infectious'])) {
                    $query->where($column, '>', 0);
                }
        }

        $validPerPages = [20, 50, 100];
        $perPage = (int)$request->input('per_page', 20);
        if (!in_array($perPage, $validPerPages)) {
            $perPage = 20; // Default if invalid value passed
        }


        $wastes = $query->orderBy('date', 'desc')->paginate($perPage)->appends($request->query()); 


        /* -----------------------------------------------------
         * 10. RETURN TO VIEW
         * --------------------------------------------------- */

        return view('partials.dashboard', [
            'labels' => $dates,
            'totals' => $totalsPerDate,
            'highest' => [
                'kg' => $highestKg,
                'date' => $highestDate ? Carbon::parse($highestDate)->format('j M') : null,
            ],
            'lowest' => [
                'kg' => $lowestKg,
                'date' => $lowestDate ? Carbon::parse($lowestDate)->format('j M') : null,
            ],
            'average' => $avgKg,
            'composition' => $composition,
            'buildingLabels' => $buildings->pluck('name'),
            'buildingDatasets' => $buildingDatasets,
            'selectedRange' => $range,
            'campuses' => $campuses,
            'selectedCampus' => $selectedCampus,
            'campus' => $campus,
            'buildings' => $buildings,
            'wastes' => $wastes,
        ]);
    }

// ... inside App\Http\Controllers\DashboardController

// ... (Your existing index method ends here) ...


/* -----------------------------------------------------
 * API METHODS FOR MARKER CRUD
 * --------------------------------------------------- */
    public function updateBuildingCoordinates(Request $request, $buildingId)
    {
        // 1. Find the existing building
        $building = Building::findOrFail($buildingId);

        // 2. Validate the incoming coordinates
        // CHANGE 'required' to 'nullable' so you can clear the marker
        $validated = $request->validate([
            'map_x_percent' => 'nullable|numeric|between:0,100', 
            'map_y_percent' => 'nullable|numeric|between:0,100',
            '_method' => 'required|in:PUT', 
        ]);
        
        // 3. Update only the coordinate fields
        $building->update([
            'map_x_percent' => $validated['map_x_percent'],
            'map_y_percent' => $validated['map_y_percent'],
        ]);

        // 4. Return the updated building data
        return response()->json($building);
    }

    public function showCampusMapViewer()
    {
        // Fetch all buildings that have coordinates set (assuming 'latitude' and 'longitude' are on the 'buildings' table)
        $buildings = Building::whereNotNull('map_x_percent')
                            ->whereNotNull('map_y_percent')
                            ->get(['name', 'map_x_percent', 'map_y_percent']);

        // Pass the building data to the new view
        return view('sections.map-content', compact('buildings'));
    }
}