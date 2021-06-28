<ul class="navbar-nav mr-auto">
    <li class="nav-item">
        <a class="nav-link" href="{{ route('server_info') }}">{{ __('Dashboard') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link disabled" href="{{ route('housekeeping') }}">{{ __('Houskeeping') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link disabled" href="{{ route('server_info') }}">{{ __('Backups') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('modules_list') }}">{{ __('Modules') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link disabled" href="{{ route('server_info') }}">{{ __('Logs') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('backup') }}">{{ __('Backups') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('system_settings') }}">{{ __('System') }}</a>
    </li>
</ul>
