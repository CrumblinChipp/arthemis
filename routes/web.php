<?php
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CampusController;
use App\Http\Controllers\WasteEntryController;
use App\Http\Middleware\AdminVerified;

// Main Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Waste Entry (No auth middleware shown, but usually you'd want one)
Route::post('/waste-entry/store', [WasteEntryController::class, 'store'])->name('waste.store');

// AJAX route for fetching buildings
Route::get('/get-buildings/{campusId}', function ($campusId) {
    return \App\Models\Building::where('campus_id', $campusId)
        ->select('id', 'name')
        ->get();
})->name('api.getBuildings');

// Admin Routes (Grouped by middleware)
Route::middleware([AdminVerified::class])->group(function () {

    // Main Settings Page (List Campuses, or show initial Add Campus form)
    Route::get('/admin/settings', [CampusController::class, 'editPage'])
        ->name('admin.settings');
    
    // Dedicated route to show the 'Add Campus' form
    Route::get('/admin/campus/create', function() {
        return view('admin.add-campus');
    })->name('admin.campus.create'); // Or use a dedicated controller method

    // Route to show the 'Edit Campus' form for a specific ID
    Route::get('/admin/campus/{id}/edit', [CampusController::class, 'editCampus'])
        ->name('admin.campus.edit');

    // POST/PUT routes for saving data (these were mostly correct)
    Route::post('/admin/campus/store', [CampusController::class, 'store'])->name('admin.campus.store');
    Route::put('/admin/campus/{campus}', [CampusController::class, 'update'])->name('admin.campus.update');
    // The DELETE route for permanently removing a campus
    Route::delete('/campus/{campus}', [CampusController::class, 'destroy'])
        ->name('admin.campus.destroy');
});