@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Dashboard</h1>

    <div class="row g-4">
        <div class="col">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Books</h5>
                    <p class="card-text fs-2">{{ $totalBooks }}</p>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Students</h5>
                    <p class="card-text fs-2">{{ $totalStudents }}</p>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Faculties</h5>
                    <p class="card-text fs-2">{{ $totalFaculties }}</p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection