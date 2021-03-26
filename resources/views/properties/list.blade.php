@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row row-cols-1 row-cols-md-3">
        @foreach ($properties as $property)
        <div class="col mb-4">
            <div class="card">
                <div class="card-body">
                    <i class="card-img-top fas {{$property->icon}}"></i>
                    <h5 class="card-title">{{$property->type}}</h5>
                </div>
                <div class="card-footer">
                    <small class="text-muted"><p class="mb-0 {{ $property->connection_error ? 'text-danger' : 'text-success' }} ">Last updated {{$property->connection_ago}}</p></small>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
