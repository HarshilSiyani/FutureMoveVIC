@extends('layouts.app')

@section('content')
<div class="bg-gradient-to-br from-green-50 to-blue-50 min-h-screen p-6">
    <h1 class="text-4xl font-bold mb-8 text-center text-green-600">
        {{ $lgaName }} Sustainability Dashboard
    </h1>

    <div class="mt-8 flex justify-between mb-6">
        <a href="{{ route('home') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Home
        </a>
        <a href="{{ route('dashboard') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
            Dashboard
        </a>
    </div>

    <x-lga-selector :lgaNames="$lgaNames" :selectedLga="$lgaName" />

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 relative">
            <h2 class="text-xl font-semibold mb-4">
                Current Sustainability Score
                <span class="info-icon" data-tooltip="This score represents the overall sustainability of the LGA based on various factors including public transport usage, active transport, and infrastructure efficiency. It ranges from 0 to 10, with 10 being the most sustainable.">ⓘ</span>
            </h2>
            <div class="text-5xl font-bold text-green-600">{{ number_format($sustainabilityScore, 1) }}/10</div>
            <div class="mt-4 bg-gray-200 h-4 rounded-full overflow-hidden">
                <div class="bg-green-500 h-full" style="width: {{ $sustainabilityScore * 10 }}%"></div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 relative">
            <h2 class="text-xl font-semibold mb-4">
                Projected 2030 Score
                <span class="info-icon" data-tooltip="This score predicts the LGA's sustainability in 2030 based on current trends and planned improvements. It takes into account population growth and projected changes in transport habits.">ⓘ</span>
            </h2>
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

        <div class="bg-white rounded-lg shadow-md p-6 relative">
            <h2 class="text-xl font-semibold mb-4">
                Population
                <span class="info-icon" data-tooltip="Current population of the LGA and the predicted population for 2030. This helps in understanding the scale of transport needs and future planning requirements.">ⓘ</span>
            </h2>
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
        <div class="bg-white rounded-lg shadow-md p-6 relative">
            <h2 class="text-xl font-semibold mb-4">
                Transport Mode Distribution
                <span class="info-icon" data-tooltip="This chart shows the breakdown of different transport modes used in the LGA. It helps identify which modes are most popular and where there might be room for improvement.">ⓘ</span>
            </h2>
            <canvas id="transportModeChart"></canvas>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 relative">
            <h2 class="text-xl font-semibold mb-4">
                Average Travel Times
                <span class="info-icon" data-tooltip="This chart compares the average travel times for different transport modes. Lower times for public and active transport relative to private vehicles indicate a more efficient sustainable transport system.">ⓘ</span>
            </h2>
            <canvas id="travelTimeChart"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 relative">
            <h2 class="text-xl font-semibold mb-4">
                Public Transport Usage
                <span class="info-icon" data-tooltip="This section shows key metrics related to public transport usage in the LGA, including annual train passengers and the number of bus and train stops.">ⓘ</span>
            </h2>
            <div class="flex justify-between items-center mb-4">
                <span class="text-gray-600">Annual Train Passengers:</span>
                <span class="font-bold">{{ number_format($lgaData['annual_train_passengers']) }}</span>
            </div>
            <div class="flex justify-between items-center mb-2">
                <span class="text-gray-600">Bus Stops:</span>
                <span class="font-bold">{{ $lgaData['bus_stops'] }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Train Stops:</span>
                <span class="font-bold">{{ $lgaData['train_stops'] }}</span>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 relative">
            <h2 class="text-xl font-semibold mb-4">
                Vehicle Usage
                <span class="info-icon" data-tooltip="This section provides information on private vehicle usage in the LGA, including the number of registered vehicles and vehicle users.">ⓘ</span>
            </h2>
            <div class="text-4xl font-bold text-red-600 mb-2">{{ number_format($lgaData['registered_vehicles']) }}</div>
            <p class="text-gray-600">Registered Vehicles</p>
            <div class="mt-4 flex justify-between items-center">
                <span class="text-gray-600">Vehicle Users:</span>
                <span class="font-bold">{{ number_format($lgaData['vehicle_users']) }}</span>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 relative">
            <h2 class="text-xl font-semibold mb-4">
                Active Transport
                <span class="info-icon" data-tooltip="This section shows data on active transport (walking) in the LGA, including the number of regular walkers and average walking time.">ⓘ</span>
            </h2>
            <div class="text-4xl font-bold text-green-600 mb-2">{{ number_format($lgaData['walkers']) }}</div>
            <p class="text-gray-600">Regular Walkers</p>
            <div class="mt-4 flex justify-between items-center">
                <span class="text-gray-600">Avg. Walking Time:</span>
                <span class="font-bold">{{ number_format($lgaData['avg_time_walking'] / 60, 1) }} min</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 mb-8 relative">
        <h2 class="text-xl font-semibold mb-4">
            Recommended Infrastructure Improvements
            <span class="info-icon" data-tooltip="These recommendations are based on the current sustainability score and projected needs. They suggest the number of additional bus and train stops that could improve the LGA's sustainability.">ⓘ</span>
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex flex-col items-center p-4 bg-blue-100 rounded-lg">
                <span class="text-2xl font-bold text-blue-600">+{{ $additionalBusStops }}</span>
                <span class="text-gray-600">Additional Bus Stops</span>
            </div>
            <div class="flex flex-col items-center p-4 bg-green-100 rounded-lg">
                <span class="text-2xl font-bold text-green-600">+{{ $additionalTrainStops }}</span>
                <span class="text-gray-600">Additional Train Stops</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 relative">
        <h2 class="text-xl font-semibold mb-4">
            Multi-modal Transport
            <span class="info-icon" data-tooltip="This metric shows the average number of transport mode changes per trip. A higher number indicates a more integrated and flexible transport system.">ⓘ</span>
        </h2>
        <div class="text-4xl font-bold text-indigo-600 mb-2">{{ number_format($lgaData['avg_modes_changed'], 1) }}</div>
        <p class="text-gray-600">Average Mode Changes per Trip</p>
    </div>
</div>

<div id="tooltip" class="absolute hidden bg-black text-white p-2 rounded shadow-lg max-w-xs z-50"></div>
@endsection

@section('styles')
<style>
    .info-icon {
        cursor: pointer;
        color: #4A5568;
        font-size: 0.8em;
        vertical-align: super;
    }
    .info-icon:hover, .info-icon:focus {
        color: #2D3748;
    }
</style>
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
                    {{ $lgaData['avg_time_bus'] / 60 }},
                    {{ $lgaData['avg_time_train'] / 60 }},
                    {{ $lgaData['avg_time_vehicle'] / 60 }},
                    {{ $lgaData['avg_time_walking'] / 60 }}
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

    // Tooltip functionality
    const tooltip = document.getElementById('tooltip');
    const infoIcons = document.querySelectorAll('.info-icon');

    function showTooltip(event) {
        const text = event.target.getAttribute('data-tooltip');
        tooltip.textContent = text;
        tooltip.style.display = 'block';
        positionTooltip(event);
    }

    function hideTooltip() {
        tooltip.style.display = 'none';
    }

    function positionTooltip(event) {
        const iconRect = event.target.getBoundingClientRect();
        const tooltipRect = tooltip.getBoundingClientRect();
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        tooltip.style.left = `${iconRect.left + (iconRect.width / 2) - (tooltipRect.width / 2)}px`;
        tooltip.style.top = `${iconRect.top + scrollTop - tooltipRect.height - 10}px`;
    }

    infoIcons.forEach(icon => {
        icon.addEventListener('mouseenter', showTooltip);
        icon.addEventListener('mouseleave', hideTooltip);
        icon.addEventListener('focus', showTooltip);
        icon.addEventListener('blur', hideTooltip);
    });

    // Ensure tooltip is hidden when clicking outside
    document.addEventListener('click', (event) => {
        if (!event.target.classList.contains('info-icon')) {
            hideTooltip();
        }
    });
</script>
@endsection