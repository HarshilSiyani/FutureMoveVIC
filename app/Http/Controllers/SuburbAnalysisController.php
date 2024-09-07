<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suburb;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SuburbAnalysisController extends Controller
{
    // public function show($suburbName)
    // {
    //     $suburb = Suburb::where('name', $suburbName)->firstOrFail();

    //     $currentData = [
    //         'name' => $suburb->name,
    //         'population' => $suburb->population,
    //         'registeredVehicles' => $suburb->registered_vehicles,
    //         'publicTransportStops' => $suburb->public_transport_stops,
    //         'annualPatronage' => $suburb->annual_patronage,
    //         'sustainabilityScore' => $suburb->calculateSustainabilityScore(),
    //     ];

    //     // Here you would also prepare data for other sections

    //     return view('suburb-analysis.show');
    // }

    public function show(Request $request) {
        $vehicleCount = DB::table('vehicle_registrations')
        ->where('POSTCODE', 3155)
        ->count();

        // dd($vehicleCount);
        
        return view('current-data', [
            'count' => $vehicleCount
        ]);
    }
}