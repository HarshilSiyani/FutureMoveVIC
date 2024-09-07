<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuburbAnalysisController;

Route::get('/suburb-analysis/{suburbName}', [SuburbAnalysisController::class, 'show']);

Route::get('/', function () {
    return view('current-data');
});
Route::get('/suburb-analysis', [SuburbAnalysisController::class, 'show']);
