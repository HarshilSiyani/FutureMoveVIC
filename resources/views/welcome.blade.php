@extends('layouts.app')

@section('content')
    <h1>CommuteSmart Victoria: Policymaker Transit Planning Tool</h1>
    
    <div class="grid">
        @include('current-data')
        <!-- Other components will go here -->
    </div>
@endsection