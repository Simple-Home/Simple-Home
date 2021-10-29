<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">{{ __('simplehome.oauth.clients') }}
        <!-- Button trigger modal -->
        <button type="button" class="location-edit btn btn-primary" data-url="{{ route('system.locations.ajax.new') }}"
            title="{{ __('simplehome.locations.create') }}">
            <i class="fas fa-plus"></i>
        </button>
    </div>
    <div class="card-body">
        @if (!empty($locations) && count($locations) > 0)
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th></th>
                            <th scope="col">{{ __('simplehome.location.name') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($locations as $location)
                            <tr>
                                <td class="col">
                                    <i class="fas {{ isset($location) ? $location->icon : 'fa-warehouse' }}"></i>
                                </td>
                                <td class="col-auto">
                                    {{ $location->name }}
                                </td>
                                <td class="col">
                                    <div>
                                        <button
                                            data-url="{{ route('system.locations.ajax.edit', ['location_id' => $location->id]) }}"
                                            class="location-edit btn btn-primary"
                                            title="{{ __('simplehome.location.edit') }}">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                        <a href="{{ route('system.locations.remove', ['location_id' => $location->id]) }}"
                                            class="btn btn-danger" title="{{ __('simplehome.room.delete') }}">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-center">{{ __('No Rooms Found') }}</p>
        @endif
    </div>
</div>
