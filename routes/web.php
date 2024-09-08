<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuburbAnalysisController;

// Route::post('/suburb-analysis/{postcode}', [SuburbAnalysisController::class, 'show'])->name('suburb-analysis');

// GET request for viewing the dashboard
Route::get('/dashboard', [SuburbAnalysisController::class, 'show'])->name('dashboard');

// POST request for form submission
Route::post('/dashboard', [SuburbAnalysisController::class, 'show']);

// Route::get('/process-csv', [SuburbAnalysisController::class, 'processCsvWithPostcodes']);
