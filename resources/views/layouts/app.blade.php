<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@hasSection('title') @yield('title') - @endif {{ config('app.name', 'Simple Home') }}</title>

    <!-- Scripts -->
    <script src="{{ asset(mix('js/manifest.js')) }}"></script>
    <script src="{{ asset(mix('js/vendor.js')) }}"></script>


    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <script src="https://kit.fontawesome.com/9c343c1f2d.js" crossorigin="anonymous"></script>

    <!-- Styles -->
    <link href="https://necolas.github.io/normalize.css/8.0.1/normalize.css">
    <link href="{{ asset(mix('css/app.css')) }}" rel="stylesheet">
    <meta name="color-scheme" content="dark light">

    @yield('customHead')

    <!-- PWA Manifest -->
    @laravelPWA
</head>

<body>
    <div class="container">
        <div class="row justify-content-between">
            <div class="col-4 p-md-0">
                <h1 class="mb-0">@yield('title')</h1>
            </div>
            <div class="col col-4 p-md-0 text-right my-auto">
                <div class="custom-control custom-switch m-auto">
                    <input type="checkbox" class="custom-control-input" id="darkSwitch" />
                    <label class="custom-control-label text-nowrap" for="darkSwitch">Dark Mode</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col p-md-0">
                <nav class="navbar p-0 overflow-auto text-nowrap">
                    <div class="container-fluid p-0 pb-2">
                        <div class="navbar-expand w-100">
                            <ul class="navbar-nav nav-pills">
                                @auth
                                @yield('subnavigation')
                                @endif
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col p-md-0">
                @auth
                @yield('alerts')
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col p-md-0">
                @auth
                @yield('content')
                @endif
            </div>
        </div>
    </div>
    <!-- Botom Fixed Menu -->
    <nav class="navbar fixed-bottom bg-light">
        <div class="container-fluid">
            <div class="navbar-expand w-100">
                <ul class="navbar-nav justify-content-around">
                    @auth
                    @include('components.navigation')
                    @endif
                </ul>
            </div>
        </div>
    </nav>


    <script src="{{ asset(mix('js/app.js')) }}"></script>
    <script>
        window.addEventListener("load", function() {
            initTheme();
        });
    </script>
    @yield('beforeBodyEnd')
</body>

</html>