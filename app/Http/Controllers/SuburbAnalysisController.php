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
    public function _construct()
    {
        // $this->loadLgaData();
    }
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

       

        // $suburb = "";
        // switch($postcode) {
        //     case 3155:
        //         $suburb = "Boronia";
        //         break;
        //     case 3156:  
        //         $suburb = "Ferntree Gully - North";
        //         break;
        //     case 3152:  
        //         $suburb = "Knoxfield - Scoresby";
        //         break;
        //     case 3153:
        //         $suburb = "Bayswater";
        //         break;
        // }


        // $suburb_population = DB::table('suburb_population')->where('name', $suburb)->get();

        // $suburb_population = $suburb_population[0]->population;

        // $bus_stops = DB::table('bus_stops')->where('postcode', $postcode)->get()->count();
        // $train_stops = DB::table('train_stop')->where('postcode', $postcode)->get()->count();
        // $lga = $this->findLga(145.271162184045, -37.8377010038382);
        // dd($lga);

        

        // dd(1);


        dd($lgaData);


        $survey = Http::get('https://discover.data.vic.gov.au/api/3/action/datastore_search?resource_id=32949433-4c83-4ce8-83f5-7f7b40bd04d0&q=Knox&limit=100000');
        $survey->json();
        
        $survey = $survey['result']['records'] ?? 0;
        // dd($survey);
        $walking = 0;
        $vehicle = 0;
        $train = 0;
        $bus = 0;
        $other = 0;
        $count = 0;
        $one = 0;
        $two = 0;
        $three = 0;
        $four = 0;

        foreach ($survey as $record) {
            // dd($record);÷
            switch($record['mode1']) {
                case 'Walking':
                    $walking++;
                    break;
                case 'Vehicle Driver':
                    $vehicle++;
                    break;
                case 'Vehicle Passenger':
                    $vehicle++;
                    break;
                case 'Train':
                    $train++;
                    break;
                case 'Public Bus':
                    $bus++;
                    break;
                case 'School Bus':
                    $bus++;
                    break;
                case 'Other':
                    $other++;
                    break;
            }
            switch($record['mode2']) {
                case 'Walking':
                    $walking++;
                    break;
                case 'Vehicle Driver':
                    $vehicle++;
                    break;
                case 'Vehicle Passenger':
                    $vehicle++;
                    break;
                case 'Train':
                    $train++;
                    break;
                case 'Public Bus':
                    $bus++;
                    break;
                case 'School Bus':
                    $bus++;
                    break;
                case 'Other':
                    $other++;
                    break;
            }
            switch($record['mode3']) {
                case 'Walking':
                    $walking++;
                    break;
                case 'Vehicle Driver':
                    $vehicle++;
                    break;
                case 'Vehicle Passenger':
                    $vehicle++;
                    break;
                case 'Train':
                    $train++;
                    break;
                case 'Public Bus':
                    $bus++;
                    break;
                case 'School Bus':
                    $bus++;
                    break;
                case 'Other':
                    $other++;
                    break;
            }
            switch($record['mode4']) {
                case 'Walking':
                    $walking++;
                    break;
                case 'Vehicle Driver':
                    $vehicle++;
                    break;
                case 'Vehicle Passenger':
                    $vehicle++;
                    break;
                case 'Train':
                    $train++;
                    break;
                case 'Public Bus':
                    $bus++;
                    break;
                case 'School Bus':
                    $bus++;
                    break;
                case 'Other':
                    $other++;
                    break;
            }

            
            if($record['mode1'] != 'N/A') {
                $one++;
                // dd($record);   
                if($record['mode2'] != 'N/A') {
                    $two++;
                    if($record['mode3'] != 'N/A') {
                        $three++;
                        if($record['mode4'] != 'N/A') {
                            $four++;
                        }
                    }
                }
            }
            // $walking = ($record['mode1'] == 'Walking' || $record['mode2'] == 'Walking' || $record['mode3'] == 'Walking' || $record['mode4'] == 'Walking') ? $walking++ : $walking;
            // $vehicle = ($record['mode1'] == 'Vehicle Driver' || $record['mode2'] == 'Vehicle Driver' || $record['mode3'] == 'Vehicle Driver' || $record['mode4'] == 'Vehicle Driver') ? $vehicle++ : $vehicle;
            // $vehicle = ($record['mode1'] == 'Vehicle Passenger' || $record['mode2'] == 'Vehicle Passenger' || $record['mode3'] == 'Vehicle Passenger' || $record['mode4'] == 'Vehicle Passenger') ? $vehicle++ : $vehicle;
            // $train = ($record['mode1'] == 'Train' || $record['mode2'] == 'Train' || $record['mode3'] == 'Train' || $record['mode4'] == 'Train') ? $vehicle++ : $vehicle;
            // $bus = ($record['mode1'] == 'Public Bus' || $record['mode2'] == 'Public Bus' || $record['mode3'] == 'Public Bus' || $record['mode4'] == 'Public Bus') ? $bus++ : $bus;
            // $bus = ($record['mode1'] == 'School Bus' || $record['mode2'] == 'School Bus' || $record['mode3'] == 'School Bus' || $record['mode4'] == 'School Bus') ? $bus++ : $bus;
            // $other = ($record['mode1'] == 'Other' || $record['mode2'] == 'Other' || $record['mode3'] == 'Other' || $record['mode4'] == 'Other') ? $other++ : $other;
            $count = $count + 1;
        }

        return view('current-data', [
            'count' => $vehicleCount,
            'population' => $suburb_population,
            'bus_stops' => $bus_stops,
            'train_stops' => $train_stops,
            'suburb' => $suburb,
            'annual_patronage' => $annual_patronage,
            'walking' => $walking,
            'vehicle' => $vehicle,
            'train' => $train,
            'bus' => $bus,
            'other' => $other,
            'count' => $count,
            'survey_total' => count($survey),
            'one' => $one,
            'two' => $two,
            'three' => $three,
            'four' => $four
        ]);
    }

    public function loadLgaData($lga)
    {
        $lgaData = [];
        $lgaData['bus_stops'] = DB::table('lga_bus_stops')
        ->where('lga_name', $lga)
        ->get()->toArray()->count();

        $lgaData['train_stops'] = DB::table('lga_train_stops')->where('lga_name', $lga)->get()->toArray()->count();

        $lgaData['registered_vehicles'] = DB::table('lga_vehicle_reg')->where('lga_name', $lga)->get()->toArray()->count();

        $lgaData['annual_patronage'] = DB::table('lga_train_annual')->where('lga_name', $lga)->get()->toArray()->count();

        return $lgaData;
    }

    public static function getSurveyResults($lga){
        $survey = Http::get('https://discover.data.vic.gov.au/api/3/action/datastore_search?resource_id=32949433-4c83-4ce8-83f5-7f7b40bd04d0&limit=100000');
        $survey->json();
        $survey = $survey['result']['records'] ?? [];
    
        // Initialize an array to store survey data grouped by LGA
        $surveyData = [];
    
        // Loop through the survey records and group them by LGA
        foreach ($survey as $record) {
            $lgaName = $record['lga'] ?? 'Unknown';
    
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
                ];
            }
    
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

    public static function processLgaVehiclesRegistered($postcode){
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

    protected function findLga($longitude, $latitude)
    {
        
        try {
            $response = Http::get($this->baseUrl, [
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

        $response = Http::get('https://discover.data.vic.gov.au/api/3/action/datastore_search?resource_id=d92a2616-9b6b-42ca-960a-b225d82541ac');
        $lgaData = [];
        foreach ($response['result']['records'] as $record) {
            $train_annual_lat = $record['Stop_lat'];
            $train_annual_long = $record['Stop_long'];
            $lga = $this->findLga($train_annual_long, $train_annual_lat);
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
                'STOP_ID' => $record['STOP_ID'],
                'STOP_NAME' => $record['STOP_NAME'],
                'Stop_lat' => $record['Stop_lat'],
                'Stop_long' => $record['Stop_long'],
                'train_annual' => $record['train_annual'],
                'lga_name' => $lga
            ]);
        }

        return $lgaData;
    }

}