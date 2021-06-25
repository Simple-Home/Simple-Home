@extends('layouts.app')
@section('pageTitle', trans('simplehome.dashboard.dashboard.pagetTitle') )
@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
<div class="container">
    <div id="accordion">
        @if(!empty($graphs) && count($graphs) > 0)
        @foreach ($graphs as $room_id => $room)
        <div class="card">
            <div class="card-header" id="heading-{{$room_id}}">
                <h5 class="mb-0">
                    <button class="btn btn-link" data-toggle="collapse" data-target="#collapse-{{$room_id}}" aria-expanded="true" aria-controls="collapse-{{$room_id}}">
                        {{ $rooms[$room_id]}}: {{$propertiesCount[$room_id]}} Properties
                    </button>
                </h5>
            </div>

            <div id="collapse-{{$room_id}}" class="collapse {{$loop->first ? 'show' : ''}}" aria-labelledby="heading-{{$room_id}}" data-parent="#accordion">
                <div class="container">
                    <div class="row row-cols-1 row-cols-md-3">
                        @if(!empty($room) && count($room) > 0)
                        @foreach ($room as $graph)
                        @if ($graph)
                        <div class="col mb-6">
                            <div style="heigth:30%;">
                                {!! $graph->render() !!}
                            </div>
                        </div>
                        @endif
                        @endforeach
                        @endif
                    </div>
                </div>

            </div>
        </div>
        @endforeach
        @else
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-secondary" role="alert">
                    {{__('simplehome.noRooms')}} <a href="rooms/" title="{{__('simplehome.gotoRooms')}}">  {{__('simplehome.roomsPage')}}</a> {{__('simplehome.toAddRoom')}}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
