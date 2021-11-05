@extends('layouts.settings')
@section('title', 'integrations')

@section('subnavigation')
    @include('system.components.subnavigation')
@endsection

@section('content')
    @if (!empty($integrations) && count($integrations) > 0)
        @foreach ($integrations as $integration)
            <div class="col-lg-4 col-md-6 col-12 p-0">
                @include('system.components.integration', $integration)
            </div>
        @endforeach
    @else
        <p class="text-center">{{ __('simplehome.noIntegration') }}</p>
    @endif
@endsection
