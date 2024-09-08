@extends('layouts.app')


@section('content')
<div class="bg-gradient-to-br from-green-50 to-blue-50 min-h-screen p-6">
    <h1 class="text-4xl font-bold mb-8 text-center text-green-600">
        {{ $lgaName }} Sustainability Dashboard
    </h1>

    <x-lga-selector :lgaNames="$lgaNames" :selectedLga="$lgaName" />

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Current Sustainability Score</h2>
            <div class="text-5xl font-bold text-green-600">{{ number_format($sustainabilityScore, 1) }}/10</div>
            <div class="mt-4 bg-gray-200 h-4 rounded-full overflow-hidden">
                <div class="bg-green-500 h-full" style="width: {{ $sustainabilityScore * 10 }}%"></div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Projected 2030 Score</h2>
            <div class="text-5xl font-bold text-blue-600">{{ number_format($futureScore, 1) }}/10</div>
            <p class="mt-2 text-gray-600">Based on current trends</p>
            <div class="mt-4 flex items-center">
                <span class="text-2xl font-bold {{ $futureScore > $sustainabilityScore ? 'text-green-500' : 'text-red-500' }}">
                    {{ $futureScore > $sustainabilityScore ? '+' : '' }}{{ number_format($futureScore - $sustainabilityScore, 1) }}
                </span>
                <svg class="w-6 h-6 ml-2 {{ $futureScore > $sustainabilityScore ? 'text-green-500' : 'text-red-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $futureScore > $sustainabilityScore ? 'M5 10l7-7m0 0l7 7m-7-7v18' : 'M19 14l-7 7m0 0l-7-7m7 7V3' }}"></path>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Population</h2>
            <div class="text-4xl font-bold text-purple-600">{{ number_format($lgaData['population']) }}</div>
            <p class="mt-2 text-gray-600">Current population</p>
            <div class="mt-4 flex items-center">
                <span class="text-2xl font-bold text-blue-500">
                    {{ number_format($lgaData['predicted_population_2030']) }}
                </span>
                <p class="ml-2 text-gray-600">Predicted by 2030</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Transport Mode Distribution</h2>
            <canvas id="transportModeChart"></canvas>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Average Travel Times</h2>
            <canvas id="travelTimeChart"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Public Transport Usage</h2>
            <div class="flex justify-between items-center mb-4">
                <span class="text-gray-600">Annual Train Passengers:</span>
                <span class="font-bold">{{ number_format($lgaData['annual_train_passengers']) }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Bus Stops:</span>
                <span class="font-bold">{{ $lgaData['bus_stops'] }}</span>
            </div>
            <div class="flex justify-between items-center mt-2">
                <span class="text-gray-600">Train Stops:</span>
                <span class="font-bold">{{ $lgaData['train_stops'] }}</span>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Vehicle Usage</h2>
            <div class="text-4xl font-bold text-red-600 mb-2">{{ number_format($lgaData['registered_vehicles']) }}</div>
            <p class="text-gray-600">Registered Vehicles</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Recommended Improvements</h2>
            <div class="flex justify-between items-center mb-2">
                <span class="text-gray-600">Additional Bus Stops:</span>
                <span class="font-bold text-green-600">+{{ $additionalBusStops }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Additional Train Stops:</span>
                <span class="font-bold text-green-600">+{{ $additionalTrainStops }}</span>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Transport Mode Distribution Chart
    new Chart(document.getElementById('transportModeChart'), {
        type: 'pie',
        data: {
            labels: ['Bus', 'Train', 'Vehicle', 'Walking'],
            datasets: [{
                data: [
                    {{ $lgaData['bus_users'] }},
                    {{ $lgaData['train_users'] }},
                    {{ $lgaData['vehicle_users'] }},
                    {{ $lgaData['walkers'] }}
                ],
                backgroundColor: ['#4CAF50', '#2196F3', '#FFC107', '#9C27B0']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });

    // Average Travel Times Chart
    new Chart(document.getElementById('travelTimeChart'), {
        type: 'bar',
        data: {
            labels: ['Bus', 'Train', 'Vehicle', 'Walking'],
            datasets: [{
                label: 'Average Travel Time (minutes)',
                data: [
                    {{ $lgaData['avg_time_bus'] }},
                    {{ $lgaData['avg_time_train'] }},
                    {{ $lgaData['avg_time_vehicle'] }},
                    {{ $lgaData['avg_time_walking'] }}
                ],
                backgroundColor: ['#4CAF50', '#2196F3', '#FFC107', '#9C27B0']
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Time (minutes)'
                    }
                }
            }
        }
    });
</script>
@endsection