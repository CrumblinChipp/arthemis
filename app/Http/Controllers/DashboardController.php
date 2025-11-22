<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WasteEntry;
use Carbon\Carbon;
use DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // RANGE FILTER (default: 7 days)
        $range = $request->input('days', 7);
        if (!in_array($range, [7, 30, 90])) {
            $range = 7; // safety fallback
        }

        // Start date
        $startDate = Carbon::now()->subDays($range - 1)->toDateString();

        // 1. Get distinct dates within the selected range
        $dates = WasteEntry::where('date', '>=', $startDate)
            ->select('date')
            ->orderBy('date')
            ->distinct()
            ->pluck('date')
            ->toArray();

        // If no data exists in selected range → generate empty placeholder range
        if (empty($dates)) {
            $dates = collect(range($range - 1, 0))->map(fn($i) =>
                Carbon::now()->subDays($i)->toDateString()
            )->toArray();
        }

        // 2. Compute total kg per date
        $totalsPerDate = [];
        foreach ($dates as $d) {
            $totalsPerDate[] = WasteEntry::where('date', $d)
                ->select(DB::raw('
                    SUM(residual + recyclable + biodegradable + infectious) AS total
                '))
                ->value('total') ?? 0;
        }

        // 3. Summary stats
        $highestKg   = max($totalsPerDate);
        $lowestKg    = min($totalsPerDate);
        $avgKg       = count($totalsPerDate) ? round(array_sum($totalsPerDate) / count($totalsPerDate), 1) : 0;

        $highestDate = $dates[array_search($highestKg, $totalsPerDate)] ?? null;
        $lowestDate  = $dates[array_search($lowestKg, $totalsPerDate)] ?? null;

        // 4. Composition (sum only inside selected range)
        $composition = WasteEntry::where('date', '>=', $startDate)
            ->selectRaw('
                SUM(biodegradable) as biodegradable,
                SUM(residual)      as residual,
                SUM(recyclable)    as recyclable,
                SUM(infectious)    as infectious
            ')
            ->first()
            ->toArray();
                
        // 5. Daily waste per building

        // 6. Pass to view
        return view('dashboard', [
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
            'selectedRange' => $range, // for UI highlighting of active filter
        ]);
    }
}
