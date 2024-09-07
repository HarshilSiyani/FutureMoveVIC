<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suburb;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use League\Csv\Reader;
use League\Csv\Writer;
use SplTempFileObject;

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

        $postcode = $request['suburb'];
        $vehicleCount = DB::table('vehicle_registrations')
    ->select(DB::raw('count(*)'))
    ->where('POSTCODE', $postcode)
    ->get()[0]->count;

    $suburb = "";
    switch($postcode) {
        case 3155:
            $suburb = "Boronia";
            break;
        case 3156:  
            $suburb = "Ferntree Gully - North";
            break;
        case 3152:  
            $suburb = "Knoxfield - Scoresby";
            break;
        case 3153:
            $suburb = "Bayswater";
            break;
    }


    $suburb_population = DB::table('suburb_population')->where('name', $suburb)->get();

    $suburb_population = $suburb_population[0]->population;

    $bus_stops = DB::table('bus_stops')->where('postcode', $postcode)->get()->count();
    $train_stops = DB::table('train_stop')->where('postcode', $postcode)->get()->count();

    $response = Http::get('https://discover.data.vic.gov.au/api/3/action/datastore_search?resource_id=d92a2616-9b6b-42ca-960a-b225d82541ac&q=' . $suburb);
            $response->json();
            $response = $response['result']['records'][0]['Pax_annual'] ?? 0;
            $annual_patronage = $response;


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

    // public function processCsvWithPostcodes(Request $request)
    // {
    //     // Load CSV file from request or from the storage
    //     $path = storage_path('app/public/PTV_METRO_TRAIN_STOP.csv'); // Update with your file path
    //     $csv = Reader::createFromPath($path, 'r');
    //     $csv->setHeaderOffset(0); // Assume the first row is the header

    //     // Prepare CSV writer to write output file with postcodes
    //     $outputCsv = Writer::createFromFileObject(new SplTempFileObject());
    //     $outputCsv->insertOne(array_merge($csv->getHeader(), ['Postcode'])); // Add Postcode column

    //     // Process each row
    //     foreach ($csv as $record) {
    //         // dd($record);
    //         $latitude = $record['LATITUDE'];
    //         $longitude = $record['LONGITUDE'];

    //         // Call the Nominatim API to get postcode
    //         $postcode = $this->getPostcodeFromCoordinates($latitude, $longitude);
    //         dd($postcode);

    //         // Append the postcode to the row
    //         $outputCsv->insertOne(array_merge($record, [$postcode]));
    //     }

    //     // Output CSV to browser or save it
    //     $outputFile = storage_path('app/public/PTV_METRO_TRAIN_STOP1.csv');
    //     $outputCsv->output('output_with_postcodes.csv');

    //     return response()->download($outputFile)->deleteFileAfterSend();
    // }

    // // Function to reverse geocode using Nominatim
    // private function getPostcodeFromCoordinates($lat, $lon)
    // {
    //     $response = Http::get('https://nominatim.openstreetmap.org/reverse', [
    //         'format' => 'json',
    //         'lat' => $lat,
    //         'lon' => $lon,
    //     ]);
    //     dd($response);
    //     sleep(1); // Sleep for 1 second to avoid rate limiting
    //     if ($response->successful()) {
    //         $data = $response->json();
    //         return $data['address']['postcode'] ?? 'N/A'; // Return postcode or N/A if not available
    //     }

    //     return 'N/A';
    // }
}