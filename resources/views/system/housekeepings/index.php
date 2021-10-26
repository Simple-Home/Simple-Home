@extends('layouts.app')

@section('subnavigation')
@include('settings.components.subnavigation')
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col">
            @if ($runJob == true)
            <div class="row">
                <div class="alert alert-success col-12" role="alert">
                    {{__('simplehome.housekeeping.runJob.triggert')}}
                </div>
            </div>
            @endif
            <div class="row">
                <div class="col-2 p-0">
                    {{__('simplehome.records')}}: {{ $totalRecords }}
                </div>

                <div class="col-3 ml-auto p-0">
                    <a href="{{route('housekeeping_runjob')}}">
                        <button type="button" class="w-100 btn btn-primary">{{__('simplehome.housekeeping.runJob')}}</button>
                    </a>
                </div>
            </div>
            <div class="col">
                <div class="row">
                    <form action="{{route('housekeeping_saveform')}}" method="post">
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" name="housekeeping_active" type="checkbox" value="1" id="active" @if ($settings['active']->value != 0) checked="checked" @endif>
                                <label class="form-check-label" for="active">
                                    {{__('simplehome.active')}}
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="interval">{{__('simplehome.housekeeping.interval')}}</label>
                            <input type="number" name="housekeeping_interval" class="form-control" id="interval" value="{{$settings['interval']->value}}" placeholder="Password">
                        </div>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">{{__('simplehome.save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endsection