@extends('layouts.app')
@section('pageTitle', trans('simplehome.properties.list.pageTitle') )
@section('content')
<div class="container">
    @include('components.search')
    <div class="container-fluid"></div>
    <div class="row justify-content-center">
        <div class="col-md-12"> 
            <div class="col-md-12">
                <div class="row">
                    <div class="col">
                        <h2>{{ __('simplehome.properties.list.pageTitle') }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
         
    @if (!empty($properties) && count($properties) > 0)         
        <div class="row row-cols-1 row-cols-md-3">  
            @foreach ($properties as $property)
            <div class="col mb-4">
            <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md">
                                <h5 class="card-title">
                                    <a href="{{ route('properties_detail', $property->id) }}">
                                        <i class="fas {{$property->icon}}"></i> {{strtoupper($property->device->hostname)}}: {{ucwords($property->nick_name)}}
                                    </a>
                                </h5>
                            </div>
                        
                            <div class="col-xs">
                                @if (!empty($property->last_value->value))
                                    <h5 class="text-right">State: {{$property->last_value->value}}</h5>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            @if (!empty($property->last_value->value) && ($property->type == "switch"))
								@if (strtolower($property->last_value->value) == "off")
									<button type="button" onclick="deviceControl('{{ $property->device->hostname }}', '{{ $property->id }}', 'state', 'on');" class="btn btn-success">Turn On</button>
								@else
									<button type="button" onclick="deviceControl('{{ $property->device->hostname }}', '{{ $property->id }}', 'state', 'off');" class="btn btn-danger">Turn Off</button>
								@endif
							@endif
							
                            &nbsp;
                            @if (strtolower($property->type) == "light")
                                <button type="button" onclick="deviceControl('{{ $property->device->hostname }}', '{{ $property->id }}', 'brightness', '10');" class="btn btn-primary">Max Brightness</button>
                                &nbsp;
                                <button type="button" onclick="deviceControl('{{ $property->device->hostname }}', '{{ $property->id }}', 'brightness', '1');" class="btn btn-primary">Min Brightness</button>
                            @endif
							
                            @if (strtolower($property->type) == "speaker")
                                <button type="button" onclick="deviceControl('{{ $property->device->hostname }}', '{{ $property->id }}', 'volume', '10');" class="btn btn-primary">Max Volume</button>
                                &nbsp;
                                <button type="button" onclick="deviceControl('{{ $property->device->hostname }}', '{{ $property->id }}', 'volume', '1');" class="btn btn-primary">Min Volume</button>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer">
                        <small class="text-muted">
                            <p class="mb-0 {{ $property->connection_error ? 'text-danger' : 'text-success' }}">Last updated {{$property->connection_ago}}</p>
                        </small>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <p class="text-center">{{ __('simplehome.properties.notFound') }}</p>
    @endif
</div>
@endsection
