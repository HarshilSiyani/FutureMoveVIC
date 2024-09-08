<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suburb;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use geoPHP;

class SuburbAnalysisController extends Controller
{
    protected $baseUrl = 'https://geo.abs.gov.au/arcgis/rest/services/ASGS2021/LGA/MapServer/0/query';

    public function show(Request $request)
    {
        $lgaName = $request->input('lga', 'Knox');
        $calculations = self::getLgaCalculations($lgaName);
        $lgaNames = self::getDistinctLgaNames();
        $lgaData = self::loadLgaData($lgaName);

        return view('current-data', [
        'lgaData' => $lgaData,
        'lgaName' => $lgaName,
        'lgaNames' => $lgaNames,
        'sustainabilityScore' => $calculations->sustainability_score,
        'futureScore' => $calculations->future_score,
        'additionalBusStops' => $calculations->additional_bus_stops,
        'additionalTrainStops' => $calculations->additional_train_stops,
        ]);
    }

    public static function processAllLgas()
    {
        $lgas = DB::table('lga_survey_results')
            ->select('lga_name')
            ->distinct()
            ->get();

        foreach ($lgas as $lga) {
            $lgaName = preg_replace('/\s*\([^)]*\)/', '', trim($lga->lga_name));
            $lgaData = self::loadLgaData($lgaName);
            $allLgaData = [$lgaName => $lgaData]; // This needs to be adjusted to include all LGAs

            $currentScore = self::calculateSustainabilityScore($lgaData, $allLgaData);
            $futureScore = self::predictFutureScore($lgaData, 1.2);
            $requiredInfrastructure = self::calculateRequiredInfrastructure($currentScore, $currentScore + 10, $lgaData);

            DB::table('lga_calculations')->updateOrInsert(
                ['lga_name' => $lgaName],
                [
                    'sustainability_score' => $currentScore,
                    'future_score' => $futureScore,
                    'additional_bus_stops' => $requiredInfrastructure['additional_bus_stops'],
                    'additional_train_stops' => $requiredInfrastructure['additional_train_stops'],
                    'updated_at' => now(),
                ]
            );
        }
    }

    public static function getLgaCalculations($lgaName)
    {
        return DB::table('lga_calculations')
            ->where('lga_name', $lgaName)
            ->first() ?? self::calculateAndStoreLgaData($lgaName);
    }

    private static function calculateAndStoreLgaData($lgaName)
    {
        $lgaData = self::loadLgaData($lgaName);
        $allLgaData = [$lgaName => $lgaData]; // This needs to be adjusted to include all LGAs

        $currentScore = self::calculateSustainabilityScore($lgaData, $allLgaData);
        $futureScore = self::predictFutureScore($lgaData, 1.2);
        $requiredInfrastructure = self::calculateRequiredInfrastructure($currentScore, $currentScore + 10, $lgaData);

        return DB::table('lga_calculations')->updateOrInsert(
            ['lga_name' => $lgaName],
            [
                'sustainability_score' => $currentScore,
                'future_score' => $futureScore,
                'additional_bus_stops' => $requiredInfrastructure['additional_bus_stops'],
                'additional_train_stops' => $requiredInfrastructure['additional_train_stops'],
                'updated_at' => now(),
            ]
        );
    }

    public static function calculateSustainabilityScore($lgaData, $allLgaData)
    {

        $benchmarks = [
            'pt_usage' => 0.5, // 50% of population using public transport daily
            'active_transport' => 0.2, // 20% of population walking
            'vehicle_dependency' => 0.3, // Only 30% using private vehicles
            'pt_infrastructure' => 5, // 5 bus stops per 1000 people
            'multi_modal' => 2, // Average of 2 mode changes per trip
            'avg_commute_time' => 30, // 30 minutes average commute time
            'ideal_growth' => 0.15, // 15% population growth by 2030
            'mode_balance' => 0.5, // Ratio of least used to most used transport mode
        ];
        
        $score = 0;
        $maxScore = 100;
    
        // Calculate total users as a proxy for population
        $totalUsers = $lgaData['bus_users'] + $lgaData['train_users'] + $lgaData['walkers'] + $lgaData['vehicle_users'];
        if ($totalUsers == 0) return 0; // Return 0 if no users data available
    
        // 1. Sustainable Transport Usage (40 points)
        $sustainableUsers = $lgaData['bus_users'] + $lgaData['train_users'] + $lgaData['walkers'];
        $sustainableRatio = $sustainableUsers / $totalUsers;
        $score += 40 * $sustainableRatio;
    
        // 2. Public Transport Efficiency (30 points)
        $ptUsers = $lgaData['bus_users'] + $lgaData['train_users'];
        $ptTime = $lgaData['avg_time_bus'] + $lgaData['avg_time_train'];
        $ptEfficiency = $ptUsers > 0 && $ptTime > 0 ? $ptUsers / $ptTime : 0;
    
        $maxPtEfficiency = max(array_map(function($lga) {
            $users = $lga['bus_users'] + $lga['train_users'];
            $time = $lga['avg_time_bus'] + $lga['avg_time_train'];
            return $users > 0 && $time > 0 ? $users / $time : 0;
        }, $allLgaData));
    
        $score += $maxPtEfficiency > 0 ? 30 * ($ptEfficiency / $maxPtEfficiency) : 0;
    
        // 3. Walking Promotion (20 points)
        $walkingRatio = $lgaData['walkers'] / $totalUsers;
        $maxWalkingRatio = max(array_map(function($lga) {
            $total = $lga['bus_users'] + $lga['train_users'] + $lga['walkers'] + $lga['vehicle_users'];
            return $total > 0 ? $lga['walkers'] / $total : 0;
        }, $allLgaData));
    
        $score += $maxWalkingRatio > 0 ? 20 * ($walkingRatio / $maxWalkingRatio) : 0;
    
        // 4. Multi-modal Transport (10 points)
        $avgModesChanged = $lgaData['avg_modes_changed'] / $totalUsers;
        $maxAvgModesChanged = max(array_map(function($lga) {
            $total = $lga['bus_users'] + $lga['train_users'] + $lga['walkers'] + $lga['vehicle_users'];
            return $total > 0 ? $lga['avg_modes_changed'] / $total : 0;
        }, $allLgaData));
    
        $score += $maxAvgModesChanged > 0 ? 10 * ($avgModesChanged / $maxAvgModesChanged) : 0;
    
        return round($score, 2);
}

public static function predictFutureScore($lgaData, $populationGrowthFactor)
{
    $futureData = $lgaData;
    foreach (['bus_users', 'train_users', 'walkers', 'vehicle_users'] as $key) {
        $futureData[$key] *= $populationGrowthFactor;
    }
    return self::calculateSustainabilityScore($futureData, [$futureData]);
}

public static function calculateRequiredInfrastructure($currentScore, $targetScore, $lgaData)
{
    $currentTotal = $lgaData['bus_users'] + $lgaData['train_users'];
    $requiredIncrease = ($targetScore - $currentScore) / 70 * $currentTotal; // 70 is the max points for PT
    
    return [
        'additional_bus_stops' => ceil($requiredIncrease * 0.7), // Assuming 70% of increase through bus stops
        'additional_train_stops' => ceil($requiredIncrease * 0.3), // Assuming 30% of increase through train stops
    ];
}

    public static function loadLgaData($lga)
    {
        $lgaData = [];
        $lgaData['bus_stops'] = DB::table('lga_bus_stops')
        ->where('lga_name', $lga)
        ->count();

        $lgaData['train_stops'] = DB::table('lga_train_stops')->where('lga_name', $lga)->get()->count();

        $lgaData['registered_vehicles'] = DB::table('lga_vehicle_reg')->where('lga_name', $lga)->get('lga_name')->count();

        $lgaData['annual_patronage'] = DB::table('lga_train_annual')->where('lga_name', $lga)->sum('train_annual');

        // $lgaData['survey_results'] = DB::table('lga_survey_results')->where('lga_name', $lga)->get();

        $lgaData['population'] = DB::table('lga_population')->where('LGA_NAME_2023', $lga)->sum('2023_population');

        $lgaData['predicted_population'] = DB::table('lga_prediction_population')->where('lga_name', $lga)->where('reference_date', '6/30/2032 12:00:00 AM')->sum('persons_total');

        $lgaData['walkers'] = DB::table('lga_survey_results')->where('lga_name', 'like', "%{$lga}%")->sum('walking');

        $lgaData['vehicle'] = DB::table('lga_survey_results')->where('lga_name', 'like', "%{$lga}%")->sum('vehicle');

        $lgaData['train'] = DB::table('lga_survey_results')->where('lga_name', 'like', "%{$lga}%")->sum('train'); 

        $lgaData['bus'] = DB::table('lga_survey_results')->where('lga_name', 'like', "%{$lga}%")->sum('bus');

        $lgaData['other'] = DB::table('lga_survey_results')->where('lga_name', 'like', "%{$lga}%")->sum('other');

        $lgaData['stops'] = DB::table('lga_survey_results')->where('lga_name', 'like', "%{$lga}%")->sum('stops');

        $lgaData['count'] = DB::table('lga_survey_results')->where('lga_name', 'like', "%{$lga}%")->get('count'); 

        $lgaData['total_time_walking'] = DB::table('lga_survey_results')->where('lga_name', 'like', "%{$lga}%")->sum('total_time_walking');

        $lgaData['total_dist_walking'] = DB::table('lga_survey_results')->where('lga_name', 'like', "%{$lga}%")->sum('total_dist_walking');   

        $lgaData['total_time_vehicle'] = DB::table('lga_survey_results')->where('lga_name', 'like', "%{$lga}%")->sum('total_time_vehicle');   

        $lgaData['total_dist_vehicle'] = DB::table('lga_survey_results')->where('lga_name', 'like', "%{$lga}%")->sum('total_dist_vehicle');   

        $lgaData['total_time_train'] = DB::table('lga_survey_results')->where('lga_name', 'like', "%{$lga}%")->sum('total_time_train');   

        $lgaData['total_dist_train'] = DB::table('lga_survey_results')->where('lga_name', 'like', "%{$lga}%")->sum('total_dist_train');   

        $lgaData['total_time_bus'] = DB::table('lga_survey_results')->where('lga_name', 'like', "%{$lga}%")->sum('total_time_bus');   

        $lgaData['total_dist_bus'] = DB::table('lga_survey_results')->where('lga_name', 'like', "%{$lga}%")->sum('total_dist_bus');   

        $lgaData['total_time_other'] = DB::table('lga_survey_results')->where('lga_name', 'like', "%{$lga}%")->sum('total_time_other');   

        $lgaData['total_dist_other'] = DB::table('lga_survey_results')->where('lga_name', 'like', "%{$lga}%")->sum('total_dist_other');

        $lgaData = [
            'annual_train_passengers' => $lgaData['annual_patronage'],
            'bus_users' => $lgaData['bus'],
            'train_users' => $lgaData['train'],
            'population' => $lgaData['population'],
            'walkers' => $lgaData['walkers'],
            'vehicle_users' => $lgaData['vehicle'],
            'registered_vehicles' => $lgaData['registered_vehicles'],
            'train_stops' => $lgaData['train_stops'],
            'bus_stops' => $lgaData['bus_stops'],
            'avg_modes_changed' => $lgaData['stops'],
            'avg_time_train' => $lgaData['total_time_train'],
            'avg_time_bus' => $lgaData['total_time_bus'],
            'avg_time_vehicle' => $lgaData['total_time_vehicle'],
            'avg_time_walking' =>  $lgaData['total_time_walking'],
            'predicted_population_2030' => $lgaData['predicted_population'],
        ];
        return $lgaData;
    }

    public static function getSurveyResults(){
        $survey = Http::get('https://discover.data.vic.gov.au/api/3/action/datastore_search?resource_id=32949433-4c83-4ce8-83f5-7f7b40bd04d0&limit=100000');
        $survey->json();
        $survey = $survey['result']['records'] ?? [];
    
        // Initialize an array to store survey data grouped by LGA
        $surveyData = [];
    
        // Loop through the survey records and group them by LGA
        foreach ($survey as $record) {
            $lgaName = $record['origLGA'] ?? 'Unknown';
            if (!isset($surveyData[$lgaName])) {
                $surveyData[$lgaName] = [
                    'walking' => 0,
                    'vehicle' => 0,
                    'train' => 0,
                    'bus' => 0,
                    'other' => 0,
                    'total_time_walking' => 0,
                    'total_dist_walking' => 0,
                    'total_time_vehicle' => 0,
                    'total_dist_vehicle' => 0,
                    'total_time_train' => 0,
                    'total_dist_train' => 0,
                    'total_time_bus' => 0,
                    'total_dist_bus' => 0,
                    'total_time_other' => 0,
                    'total_dist_other' => 0,
                    'stops' => 0,
                    'count' => 0
                ];
            }

            $surveyData[$lgaName]['stops'] += $record['stops'] ?? 0;
    
            for ($i = 1; $i <= 4; $i++) {
                $mode = $record['mode' . $i] ?? null;
                $dist = $record['dist' . $i] ?? 0;
                $time = $record['time' . $i] ?? 0;
    
                switch ($mode) {
                    case 'Walking':
                        $surveyData[$lgaName]['walking']++;
                        $surveyData[$lgaName]['total_dist_walking'] += $dist;
                        $surveyData[$lgaName]['total_time_walking'] += $time;
                        break;
                    case 'Vehicle Driver':
                    case 'Vehicle Passenger':
                        $surveyData[$lgaName]['vehicle']++;
                        $surveyData[$lgaName]['total_dist_vehicle'] += $dist;
                        $surveyData[$lgaName]['total_time_vehicle'] += $time;
                        break;
                    case 'Train':
                        $surveyData[$lgaName]['train']++;
                        $surveyData[$lgaName]['total_dist_train'] += $dist;
                        $surveyData[$lgaName]['total_time_train'] += $time;
                        break;
                    case 'Public Bus':
                    case 'School Bus':
                        $surveyData[$lgaName]['bus']++;
                        $surveyData[$lgaName]['total_dist_bus'] += $dist;
                        $surveyData[$lgaName]['total_time_bus'] += $time;
                        break;
                    case 'Other':
                        $surveyData[$lgaName]['other']++;
                        $surveyData[$lgaName]['total_dist_other'] += $dist;
                        $surveyData[$lgaName]['total_time_other'] += $time;
                        break;
                }
            }
            $surveyData[$lgaName]['count']++;
        }
    
        // Save the aggregated results in the database
        foreach ($surveyData as $lgaName => $data) {
            DB::table('lga_survey_results')->updateOrInsert(
                ['lga_name' => $lgaName],
                $data
            );
        }

            // // dd($record);÷

            // switch($record['mode2']) {
            //     case 'Walking':
            //         $walking++;
            //         break;
            //     case 'Vehicle Driver':
            //         $vehicle++;
            //         break;
            //     case 'Vehicle Passenger':
            //         $vehicle++;
            //         break;
            //     case 'Train':
            //         $train++;
            //         break;
            //     case 'Public Bus':
            //         $bus++;
            //         break;
            //     case 'School Bus':
            //         $bus++;
            //         break;
            //     case 'Other':
            //         $other++;
            //         break;
            // }
            // switch($record['mode3']) {
            //     case 'Walking':
            //         $walking++;
            //         break;
            //     case 'Vehicle Driver':
            //         $vehicle++;
            //         break;
            //     case 'Vehicle Passenger':
            //         $vehicle++;
            //         break;
            //     case 'Train':
            //         $train++;
            //         break;
            //     case 'Public Bus':
            //         $bus++;
            //         break;
            //     case 'School Bus':
            //         $bus++;
            //         break;
            //     case 'Other':
            //         $other++;
            //         break;
            // }
            // switch($record['mode4']) {
            //     case 'Walking':
            //         $walking++;
            //         break;
            // }
        }

    public static function processLgaPublicTransport(){
        $train_stations = DB::table('lga_train_stops')->get()->toArray();
        // $train_stations = $train_stations->toArray();
        $train_stations = array_slice($train_stations, 3);
        foreach($train_stations as $train_station) {
            $latitude = $train_station->Y;
            $longitude = $train_station->X;
            // dd($latitude, $longitude);
            $lga = $this->findLga($longitude, $latitude);
            $train_station->lga = $lga;
            //db insert lga

            DB::table('lga_train_stops')
            ->where('STOP_ID', $train_station->STOP_ID)
            ->where('STOP_NAME', $train_station->STOP_NAME)
            ->where('X', $train_station->X)
            ->where('Y', $train_station->Y)
            ->update(['lga_name' => $lga]);
        }

        $bus_stops = DB::table('lga_bus_stops')->whereNull('lga_name')->get()->toArray();

        // $bus_stops = $bus_stops->toArray();
        // $bus_stops = array_slice($bus_stops, 3);
        foreach($bus_stops as $bus_stop) {
            $latitude = $bus_stop->Y;
            $longitude = $bus_stop->X;
            // dd($latitude, $longitude);
            $lga = $this->findLga($longitude, $latitude);
            $bus_stop->lga = $lga;
            //db insert lga

            DB::table('lga_bus_stops')
            ->where('STOP_ID', $bus_stop->STOP_ID)
            ->where('STOP_NAME', $bus_stop->STOP_NAME)
            ->where('X', $bus_stop->X)
            ->where('Y', $bus_stop->Y)
            ->update(['lga_name' => $lga]);
        }
    }

    public static function processLgaVehiclesRegistered(){
        $postcodes = DB::table('lga_vehicle_reg')->get('POSTCODE')->whereNull('lga_name')->toArray();
        $processedPostcodes = [];

        foreach ($postcodes as $postcodeData) {
            $postcode = $postcodeData->POSTCODE;

            // Skip if LGA name is already found or if the postcode has been processed
            if (!empty($postcodeData->lga_name) || in_array($postcode, $processedPostcodes)) {
                continue;
            }

            // Fetch latitude and longitude for the postcode
            $response = Http::get('https://nominatim.openstreetmap.org/search?postalcode=' . $postcode . '&format=json&addressdetails=1&country=AU');
            $response = $response->json();
            if (isset($response[0]['lat']) && isset($response[0]['lon'])) {
                $lat = $response[0]['lat'];
                $lon = $response[0]['lon'];

            // Find LGA name using latitude and longitude
            $lga = $this->findLga($lon, $lat);

            // Update the database with the found LGA name
            

            // Add the postcode to the set of processed postcodes
            $processedPostcodes[] = $postcode;
            } else {
                $lga = 'N/A';
            }

            DB::table('lga_vehicle_reg')
            ->where('POSTCODE', $postcode)
            ->update(['lga_name' => $lga]);
        }
    }

    protected static function findLga($longitude, $latitude)
    {
        $baseUrl = 'https://geo.abs.gov.au/arcgis/rest/services/ASGS2021/LGA/MapServer/0/query';
        
        try {
            $response = Http::get($baseUrl, [
                'geometry' => "{$longitude},{$latitude}",
                'geometryType' => 'esriGeometryPoint',
                'inSR' => '4326',
                'outFields' => 'LGA_NAME_2021',
                'returnGeometry' => 'false',
                'f' => 'json'
            ]);

            $response->throw();  // This will throw an exception for 4xx and 5xx responses

            $data = $response->json();

            if (isset($data['features'][0]['attributes']['lga_name_2021'])) {
                return $data['features'][0]['attributes']['lga_name_2021'];
            }
        } catch (RequestException $e) {
            // Log the error
            \Log::error('ABS API Error: ' . $e->getMessage());
        }
    }

    public function lgaAutoComplete(Request $request)
    {
        $query = $request->get('query');
        $suggestions = self::getDistinctLgaNames()
            ->filter(function($name) use ($query) {
                return stripos($name, $query) !== false;
            })
            ->values()
            ->all();
        dd($suggestions);

        return response()->json($suggestions);
    }

    public static function getDistinctLgaNames()
    {
        return DB::table('lga_bus_stops')->distinct()->whereNotNull('lga_name')->pluck('lga_name')->sort()->values();
    }

    public static function putTrainAnnualPax(){

        $response = Http::get('https://discover.data.vic.gov.au/api/3/action/datastore_search?resource_id=d92a2616-9b6b-42ca-960a-b225d82541ac&limit=100000');
        $lgaData = [];
        foreach ($response['result']['records'] as $record) {
            $train_annual_lat = $record['Stop_lat'];
            $train_annual_long = $record['Stop_long'];
            $lga = self::findLga($train_annual_long, $train_annual_lat);
            $record['lga'] = $lga;
        
            // Check if LGA already exists in the array
            if (isset($lgaData[$lga])) {
                // Update existing LGA data
                $lgaData[$lga]['train_annual'][] = $record;
            } else {
                // Add new LGA entry
                $lgaData[$lga] = [
                    'train_annual' => [$record],
                ];
            }
            DB::table('lga_train_annual')->insert([
                'STOP_ID' => $record['Stop_ID'],
                'STOP_NAME' => $record['Stop_name'],
                'Stop_lat' => $record['Stop_lat'],
                'Stop_long' => $record['Stop_long'],
                'train_annual' => $record['Pax_annual'],
                'lga_name' => $lga
            ]);
        }

        return $lgaData;
    }

}