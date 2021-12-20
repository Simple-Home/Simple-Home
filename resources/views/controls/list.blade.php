@extends('layouts.app')
@section('title', trans('simplehome.home.pageTitle'))

@section('customeHead')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@^2"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@^1"></script>
@endsection

@section('subnavigation')
    <div class="ajax-subnavigation" data-url="{{ route('controls.ajax.subnavigation') }}"></div>
@endsection

@section('content')
    <div id="carouselExampleSlidesOnly" class="carousel slide h-100" data-bs-wrap="false" data-bs-keyboard="true"
        data-bs-ride="carousel" data-bs-touch="true" data-bs-interval="false">
        <div class="carousel-inner h-100">
            @foreach ($rooms as $room)
                <div class="carousel-item h-100" data-room-id="{{ $room->id }}"
                    data-url="{{ route('controls.ajax.list', ['room_id' => $room->id]) }}">
                    <div class="d-flex h-100">
                        <div class="text-center m-auto">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
