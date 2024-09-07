<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuburbAnalysisController;

// Route::post('/suburb-analysis/{postcode}', [SuburbAnalysisController::class, 'show'])->name('suburb-analysis');

Route::get('/', function () {
    return view('current-data', ['new' => 'true']);
});
Route::post('/suburb-analysis', [SuburbAnalysisController::class, 'show'])->name('suburb-analysis');
Route::get('/process-csv', [SuburbAnalysisController::class, 'processCsvWithPostcodes']);
