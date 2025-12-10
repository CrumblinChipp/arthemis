<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campus;
use App\Models\WasteEntry;
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
         * 9. RETURN TO VIEW
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
        ]);
    }
}