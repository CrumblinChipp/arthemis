<?php
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Kernel;
use App\Http\Controllers\Admin\CampusController;

Route::get('/', function () {
    return view('dashboard');
});

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::post('/admin/verify', [App\Http\Controllers\AdminAuthController::class, 'verify'])
    ->name('admin.verify');

Route::middleware(['adminVerified'])->group(function () {

    Route::get('/admin/settings', function () {
        return view('admin.settings');
    })->name('admin.settings');

    Route::post('/campus/add', function () {
        return 'campus added';
    })->name('campus.add');

    Route::post('/building/add', function () {
        return 'building added';
    })->name('building.add');

});

    Route::post('/admin/campus/store', [CampusController::class, 'store'])->name('admin.campus.store');
    Route::post('/admin/add-campus', [CampusController::class, 'store'])->name('admin.addCampus');
