@extends('layouts.settings')
@section('pageTitle', trans('simplehome.devices.detail.pageTitle'))
@section('content')
    <div class="card mb-3">
        <div class="card-header">
            {{ __('simplehome.controls') }}
        </div>
        <div class="card-body">
            @include('system.components.device_controls', ['device' => $device])
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            {{ __('simplehome.controls') }}
        </div>
        <div class="card-body">
            {!! form_start($deviceForm) !!}
            {!! form_until($deviceForm, 'integration') !!}
            @if (!empty($integrations))
                <datalist id="integrations">
                    @foreach ($integrations as $value)
                        <option value="{{ $value }}"></option>
                    @endforeach
                </datalist>
            @endif
            {!! form_end($deviceForm) !!}
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            {{ __('simplehome.settings') }}
        </div>
        <div class="card-body">
            <div>
                <h3 class="mb-0">{{ $device->hostname }}</h3>
                <p class="mb-0">{{ $device->heartbeat }}
                    {{ $device->heartbeat->diffForHumans() }}
                </p>
            </div>
            @if (!empty(round($device->created)))
                {{ __('First Seen') }}: {{ $device->createdat }}</br>
            @endif
            {{ __('Last Seen') }}: {{ $device->heartbeat }}</br>
            @if (isset($device->data->network->ip))
                {{ __('Ip Address') }}: {{ $device->data->network->ip }}</br>
            @endif
            @if (isset($device->data->network->mac))
                {{ __('Mac Address') }}: {{ $device->data->network->mac }}</br>
            @endif
            @if (isset($device->data->firmware->hash))
                {{ __('Firmware') }}: {{ $device->data->firmware->hash }}</br>
            @endif
            @if (isset($device->data->firmware->ver))
                {{ __('Firmware v') }}: {{ $device->data->firmware->ver }}</br>
            @endif


            {{ __('Gateway') }}:</br>
            {{ __('subnet') }}:</br>
            @if (!empty(round($device->signal_strength)))
                {{ __('Signal') }}: {{ $device->signal_strength }} dbm
                ({{ $device->signal_strength_percent }} %)
                </br>
            @endif
            @if (!empty(round($device->battery_level)))
                {{ __('Battery') }}: {{ round($device->battery_level, 2) }} v
                ({{ round($device->battery_level_percent, 2) }} %)</br>
            @endif
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            {{ __('simplehomedevice.latest.logs') }} {{ $logfile }}
        </div>
        <div class="card-body">
            @if (!empty($logfileContent))
                <pre>
                        @php
                            echo $logfileContent;
                        @endphp
                    </pre>
            @endif
        </div>
    </div>

    @if (!empty($device->getProperties) && count($device->getProperties) > 0)
        <div class="card mb-3">
            <div class="card-header">
                {{ __('simplehome.controls') }}
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Icon</th>
                                <th scope="col">Name / Type</th>
                                <th scope="col">Last Value</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($device->getProperties as $property)
                                <tr>
                                    <th scope="row">
                                        <i class="fas {{ $property->icon }}"></i>
                                    </th>
                                    <td>{{ $property->nick_name }} <br> {{ $property->type }}</td>
                                    <td>
                                        @if (!empty(($lastRecord = $property->latestRecord)))
                                            {{ !empty($lastRecord->origin) ? $lastRecord->origin . '->' : '' }}{{ gettype($lastRecord->value) == 'int' ? round($lastRecord->value, 2) : $lastRecord->value }}
                                            {{ $property->units }}
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="btn btn-info p-1">
                                            <a href="{{ route('controls.detail', $property->id) }}"
                                                class="btn btn-primary"><i class="fa fa-chart-area"></i></a>
                                            <a href="{{ route('controls.edit', $property->id) }}"
                                                class="btn btn-primary"><i class="fa fa-cog"></i></a>
                                            <a href="{{ route('controls.hide.toggle', $property->id) }}"
                                                class="btn btn-primary">
                                                {!! $property->is_hidden ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>' !!}
                                            </a>
                                            <a href="{{ route('controls.enable.toggle', $property->id) }}"
                                                class="btn btn-danger"><i class="fas fa-ban"></i></a>
                                            <a href="{{ route('controls.remove', $property->id) }}"
                                                class="btn btn-danger"><i class="fa fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endsection
