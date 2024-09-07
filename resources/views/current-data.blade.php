@extends('layouts.app')

@section('content')

@if(!empty($new)) 
<form action="{{ route('suburb-analysis') }}" method="POST">
    @csrf
    <select id="location" class="form-control" name="suburb">
        <option value="3155">Boronia</option>
        <option value="3156">Ferntree Gully - North</option>
        <option value="3153">Bayswater</option>
    </select>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>
@else 
<h1>CommuteSmart Victoria: Policymaker Transit Planning Tool</h1>

<form action="{{ route('suburb-analysis') }}" method="POST">
    @csrf
    <select id="location" class="form-control" name="suburb">
        <option value="3155">Boronia</option>
        <option value="3156">Ferntree Gully - North</option>
        <option value="3153">Bayswater</option>
        <option value="3152">Knoxfield</option>
    </select>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>
    <br>
    <div class="row">
        <div class="col-md-6 card">
            <h2>Current Data (2023) - {{$suburb}}</h2>
            <p>Population: {{$population ?? 'null'}}</p>
            <p>Registered Vehicles: {{$count ?? 'null'}}</p>
            <p>Bus Stops: {{$bus_stops ?? '0'}}</p>
            <p>Train Stations: {{$train_stops ?? '0'}}</p>
            <p>Public Transport Stops: {{$bus_stops + $train_stops}}</p>
            <p>Annual PT Patronage: {{$annual_patronage ?? 'no station in the suburb'}}</p>
            <p>Sustainability Score: <span class="score">7.2 / 10</span></p>
        </div>
        <div class="col-md-6 card">
            <h2>2030 Projections - Richmond</h2>
            <div>
                <label>Projected Population: 38,000</label>
                <input type="range" class="slider" min="32000" max="45000" value="38000">
            </div>
            <div>
                <label>New Public Transport Stops: 5</label>
                <input type="range" class="slider" min="0" max="20" value="5">
            </div>
            <p>Projected Annual PT Patronage: 6,840,000</p>
            <p>Projected Sustainability Score: <span class="score">8.1 / 10</span></p>
        </div>
    </div>
    <h1>Average Commute Times for LGA: KNOX</h1>
    <div class="row">
        <div class="col-md-6 card">
            <h2>Mode Share</h2>
            <p>Bus: {{$bus ?? 'null'}}</p>
            <p>Train: {{$train ?? 'null'}}</p>
            <p>Vehicle: {{$vehicle ?? 'null'}}</p>
            <p>Walking: {{$walking ?? 'null'}}</p>
            <p>Other: {{$other ?? 'null'}}</p>
            <p>count: {{$count ?? 'null'}}</p>
            <p>Accounted for: {{$bus + $walking + $vehicle + $train}} responses</p>
            <p>Unaccounted for: {{$survey_total - ($bus + $walking + $vehicle + $train)}} responses</p>
        </div>
        <div class="col-md-6 card">
            <h2>Public Transport</h2>
            <p>Bus: 25 minutes</p>
            <p>Train: 45 minutes</p>

            <p>One: {{$one ?? 'null'}}</p>
            <p>Two: {{$two ?? 'null'}}</p>
            <p>Three: {{$three ?? 'null'}}</p>
            <p>Four: {{$four ?? 'null'}}</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 card">
            <h2>Walking</h2>
            <p>Peak Hour: 15 minutes</p>
            <p>Off-Peak: 10 minutes</p>
        </div>
        <div class="col-md-6 card">
            <h2>Car</h2>
            <p>Peak Hour: 35 minutes</p>
            <p>Off-Peak: 20 minutes</p>
        </div>
    </div>
@endif
@endsection