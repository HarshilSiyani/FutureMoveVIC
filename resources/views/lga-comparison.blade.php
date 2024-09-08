@extends('layouts.app')

@section('content')
<div class="bg-gradient-to-br from-green-50 to-blue-50 min-h-screen p-6">
    <h1 class="text-4xl font-bold mb-8 text-center text-green-600">
        LGA Sustainability Comparison
    </h1>
    <div class="mt-8 flex justify-between">
        <a href="{{ route('home') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Home
        </a>
        <a href="{{ route('dashboard') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
            Dashboard
        </a>
    </div>
    <div class="mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-semibold mb-4">Top 10 LGAs by Sustainability Score</h2>
            <canvas id="topLgaChart"></canvas>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-semibold mb-4">All LGAs Sustainability Scores</h2>
        <div class="mb-4">
            <input type="text" id="lgaSearch" placeholder="Search for an LGA..." class="w-full p-2 border border-gray-300 rounded-md">
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-2 px-4 border-b text-left">LGA Name</th>
                        <th class="py-2 px-4 border-b text-left cursor-pointer" onclick="sortTable(1)">Current Score</th>
                        <th class="py-2 px-4 border-b text-left cursor-pointer" onclick="sortTable(2)">2030 Projected Score</th>
                        <th class="py-2 px-4 border-b text-left">Actions</th>
                    </tr>
                </thead>
                <tbody id="lgaTableBody">
                    @foreach($lgaData as $lga)
                    <tr>
                        <td class="py-2 px-4 border-b">{{ $lga['name'] }}</td>
                        <td class="py-2 px-4 border-b">
                            {{ $lga['current_score'] !== null ? number_format($lga['current_score'], 1) : 'N/A' }}
                        </td>
                        <td class="py-2 px-4 border-b">
                            {{ $lga['future_score'] !== null ? number_format($lga['future_score'], 1) : 'N/A' }}
                        </td>
                        <td class="py-2 px-4 border-b">
                            <a href="{{ route('dashboard', ['lga' => $lga['name']]) }}" class="text-blue-600 hover:text-blue-800">View Details</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Top 10 LGAs Chart
    const topLgaData = @json($topLgaData);
new Chart(document.getElementById('topLgaChart'), {
    type: 'bar',
    data: {
        labels: topLgaData.map(lga => lga.name),
        datasets: [{
            label: 'Current Sustainability Score',
            data: topLgaData.map(lga => parseFloat(lga.current_score)),
            backgroundColor: 'rgba(75, 192, 192, 0.6)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: false,
                min: 60,  // Adjust this based on your lowest score
                max: 100, // Adjust this based on your highest score
                ticks: {
                    stepSize: 5
                }
            }
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return `Score: ${parseFloat(context.raw).toFixed(2)}`;
                    }
                }
            }
        }
    }
});

    // Search functionality
    document.getElementById('lgaSearch').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.getElementById('lgaTableBody').getElementsByTagName('tr');
        
        for (let row of rows) {
            const lgaName = row.cells[0].textContent.toLowerCase();
            row.style.display = lgaName.includes(searchTerm) ? '' : 'none';
        }
    });

    // Sorting functionality
    function sortTable(colIndex) {
        const table = document.getElementById('lgaTableBody');
        const rows = Array.from(table.getElementsByTagName('tr'));
        const isAscending = table.getAttribute('data-order') === 'asc';
        
        rows.sort((a, b) => {
            const aValue = parseFloat(a.cells[colIndex].textContent);
            const bValue = parseFloat(b.cells[colIndex].textContent);
            if (isNaN(aValue) && isNaN(bValue)) return 0;
            if (isNaN(aValue)) return 1;
            if (isNaN(bValue)) return -1;
            return isAscending ? aValue - bValue : bValue - aValue;
        });
        
        rows.forEach(row => table.appendChild(row));
        table.setAttribute('data-order', isAscending ? 'desc' : 'asc');
    }
</script>
@endsection