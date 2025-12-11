<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CampusController;
use App\Http\Controllers\WasteEntryController;
use App\Http\Middleware\AdminVerified;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\DataController;

//
// PUBLIC ROUTES (No Authentication Required)
// 

// Landing Page - Main Home Route
Route::get('/', function () {
    return view('landing');
})->name('home');

// Auth Page (Login/Register) - Only for guests
Route::get('/auth', [RegisterController::class, 'showRegistrationForm'])
    ->middleware('guest')
    ->name('auth.page');

// Login & Register Actions
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register.show');
Route::post('/register', [RegisterController::class, 'register'])->name('register');

// Admin Verification
Route::post('/admin/verify', [AdminAuthController::class, 'verify'])
    ->name('admin.verify');


//Data Actions
Route::get('/data', [DataController::class, 'showData'])->name('waste.data');
// For deleting an entry
Route::delete('/data/{id}', [DataController::class, 'destroy'])->name('waste.destroy');

// PROTECTED ROUTES (Authentication Required)
// 

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Waste Entry
    Route::post('/waste-entry/store', [WasteEntryController::class, 'store'])->name('waste.store');
    
    // AJAX route for fetching buildings
    Route::get('/get-buildings/{campusId}', function ($campusId) {
        return \App\Models\Building::where('campus_id', $campusId)
            ->select('id', 'name')
            ->get();
    })->name('api.getBuildings');
});

// 
// ADMIN ROUTES (Admin Middleware Required)
// 

Route::middleware(['auth', AdminVerified::class])->group(function () {
    // Main Settings Page (List Campuses)
    Route::get('/admin/settings', [CampusController::class, 'editPage'])
        ->name('admin.settings');
    
    // Add Campus Form
    Route::get('/admin/campus/create', function() {
        return view('admin.add-campus');
    })->name('admin.campus.create');
    
    // Edit Campus Form
    Route::get('/admin/campus/{id}/edit', [CampusController::class, 'editCampus'])
        ->name('admin.campus.edit');
    
    // Store New Campus
    Route::post('/admin/campus/store', [CampusController::class, 'store'])
        ->name('admin.campus.store');
    
    // Update Campus
    Route::put('/admin/campus/{campus}', [CampusController::class, 'update'])
        ->name('admin.campus.update');
    
    // Delete Campus
    Route::delete('/campus/{campus}', [CampusController::class, 'destroy'])
        ->name('admin.campus.destroy');
});