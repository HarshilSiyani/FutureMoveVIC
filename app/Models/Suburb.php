<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suburb extends Model
{
    protected $fillable = [
        'name', 'population', 'registered_vehicles', 'public_transport_stops', 'annual_patronage'
    ];

    public function calculateSustainabilityScore()
    {
        // This is a placeholder calculation. Adjust based on your specific criteria.
        $ptUsageScore = $this->annual_patronage / $this->population;
        $vehicleRatioScore = 1 - ($this->registered_vehicles / $this->population);
        $ptInfrastructureScore = $this->public_transport_stops / ($this->population / 1000);

        $score = ($ptUsageScore * 0.4 + $vehicleRatioScore * 0.3 + $ptInfrastructureScore * 0.3) * 10;

        return min(max($score, 0), 10); // Ensure score is between 0 and 10
    }
}
