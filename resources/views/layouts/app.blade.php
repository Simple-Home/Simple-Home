<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - {{ config('app.name', 'Simple Home') }}</title>

    <!-- Scripts -->
    <script src="{{ asset(mix('js/app.js'), Request::server('HTTP_X_FORWARDED_PROTO') != 'http' ? true : '') }}">
    </script>
    <script src="{{ asset(mix('js/manifest.js'), Request::server('HTTP_X_FORWARDED_PROTO') != 'http' ? true : '') }}">
    </script>
    <script src="{{ asset(mix('js/vendor.js'), Request::server('HTTP_X_FORWARDED_PROTO') != 'http' ? true : '') }}">
    </script>

    <!-- Styles -->
    <link href="https://necolas.github.io/normalize.css/8.0.1/normalize.css">
    <link href="{{ asset(mix('css/app.css'), Request::server('HTTP_X_FORWARDED_PROTO') != 'http' ? true : '') }}"
        rel="stylesheet">

    <meta name="color-scheme" content="dark light">

    <style>
        .nav-bar-padding {
            padding-bottom: 60px;
        }

    </style>

    @yield('customHead')

    <!-- PWA Manifest -->
    @laravelPWA
</head>

<body>
    <div class="container nav-bar-padding h-100 d-flex flex-column">
        <div class="row justify-content-between">
            @if (!session('dashboard'))
                <div class="col-4 p-md-0">
                    <h1 class="mb-0">@yield('title')</h1>
                </div>
            @else
                <div class="col">
                    <h2 id='ct' class="my-auto"></h2>
                </div>
                <div class="col text-end my-auto">
                    <div>
                        <div class="h2 d-inline me-3">
                            <a onClick="$('#notifications').modal('toggle')">
                                <i class="fa fa-bell">
                                    <span
                                        class="position-absolute top-0 p-1 bg-danger border border-light rounded-circle d-inline d-md-none">
                                        <span class="visually-hidden">New alerts</span>
                                    </span>
                                </i>
                            </a>
                        </div>
                        <div class="h2 d-inline">
                            <a class="l1" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- <div class="row">
                <div class="col p-md-0">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                        </div>
                        <div class="card-footer">
                            Card footer
                        </div>
                    </div>
                </div>
            </div> --}}

        <div class="row">
            <div class="col p-md-0">
                <nav class="navbar p-0 overflow-auto text-nowrap no-scrool-bar" style="">

                    <div class="container-fluid p-0 mb-1">
                        <div class="navbar-expand w-100">
                            <ul class="navbar-nav nav-pills">
                                @auth
                                    @yield('subnavigation')
                                @endauth
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col p-md-0">
                @auth
                    @include('components.alerts')
                @endauth
            </div>
        </div>

        <div class="row flex-grow-1">
            <div class="col p-md-0">
                @auth
                    @yield('content')
                @endauth
            </div>
        </div>

        @if (!session('dashboard'))
            <!-- Botom Fixed Menu -->
            <nav class="navbar fixed-bottom bg-light" style="z-index: 1056;">
                <div class="container-fluid">
                    <div class="navbar-expand w-100">
                        <ul class="navbar-nav justify-content-around nav-pills">
                            @auth
                                @include('components.navigation')
                            @endauth
                        </ul>
                    </div>
                </div>
            </nav>
        @endif

        <script src="{{ asset(mix('js/app.js'), Request::server('HTTP_X_FORWARDED_PROTO') != 'http' ? true : '') }}">
        </script>
        <script defer>
            window.addEventListener("load", function() {
                var darkThemeSelected =
                    localStorage.getItem("darkSwitch") !== null &&
                    localStorage.getItem("darkSwitch") === "dark";
                if (darkThemeSelected) {
                    document.body.setAttribute("data-theme", "dark");
                } else {
                    document.body.removeAttribute("data-theme");
                }
            });
        </script>

        @auth
            <!-- Full screen modal -->
            <div class="modal" id="notifications" tabindex="-1" aria-labelledby="notifications" aria-hidden="true"
                role="dialog">
                <div class="modal-dialog modal-fullscreen-md-down">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">{{ __('simplehome.notification') }}
                            </h5>
                            <div class="btn-group">
                                <a data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-h"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a id="notification-control-load" class="btn btn-primary dropdown-item"
                                            data-url="{{ route('notifications.read', ['notification_id' => 'all']) }}">
                                            readAll
                                        </a>
                                    </li>
                                    <li>
                                        <a id="notification-control-load" class="btn btn-primary dropdown-item"
                                            data-url="{{ route('notifications.delete', ['notification_id' => 'all']) }}">
                                            deleteAll
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div id="notifications-list"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endauth
        @auth
            @yield('modal')
        @endauth
        @if (session('dashboard'))
            <script>
                function display_c() {
                    var refresh = 30000; // Refresh rate in milli seconds
                    mytime = setTimeout('display_ct()', refresh)
                }

                function display_ct() {
                    var x = new Date()
                    var x1 = x.getMonth() + 1 + "/" + x.getDate() + "/" + x.getFullYear();
                    x1 = x1 + " - " + x.getHours() + ":" + x.getMinutes();
                    document.getElementById('ct').innerHTML = x1;
                    display_c();
                }
                window.addEventListener("load", function() {
                    display_ct();
                });
            </script>
        @endif

        @auth
            <script>
                function ajaxContentLoader(target, sourceUrl, loadingSpinner = true) {
                    console.log("loading from: ", sourceUrl, "loading to: ", target)
                    $.ajax({
                        start_time: new Date().getTime(),
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        url: sourceUrl,
                        beforeSend: function() {
                            if (loadingSpinner) {
                                target.html(
                                    '<div class="d-flex h-100"><div class="text-center m-auto"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div></div>'
                                );
                            }
                        },
                        success: function(msg) {
                            target.html(msg);
                            console.log((new Date().getTime() - this.start_time) + ' ms');
                        },
                        error: function() {
                            console.log((new Date().getTime() - this.start_time) + ' ms');
                        },
                        timeout: 3000,
                    });
                }

                window.addEventListener("load", function() {
                    var loadingAnimation = true;
                    //Initial Load
                    var lastRoom = localStorage.getItem("lastRoomId");
                    if (lastRoom) {
                        url = $("div.carousel-item[data-room-id='" + lastRoom + "']").data("url");
                    } else {
                        //First Load Ever no room selected in Memory
                        url = $("div.carousel-item").first().data("url");
                        lastRoom = url.split('/').reverse()[1];
                        console.log("savingRoomId", lastRoom);
                        localStorage.setItem('lastRoomId', lastRoom);
                    }

                    $(".subnavigation").removeClass("active");
                    $("div.nav-link[data-room-id='" + lastRoom + "']").addClass("active");
                    $("div.carousel-item[data-room-id='" + lastRoom + "']").addClass("active");
                    ajaxContentLoader($("div.carousel-item[data-room-id='" + lastRoom + "']"), url, loadingAnimation);

                    $('#carouselExampleSlidesOnly').on('slid.bs.carousel', function(e) {
                        loadingAnimation = false;
                        //Load Thinks
                        targetObj = $(e.relatedTarget);
                        url = targetObj.data("url");

                        //Menu Handling
                        $(".subnavigation").removeClass("active");
                        thisObj = $("div.nav-link[data-room-id='" + url.split('/').reverse()[1] + "']");
                        thisObj.addClass("active");

                        //Load load content from URL
                        ajaxContentLoader(targetObj, url, loadingAnimation);

                        localStorage.lastRoomId = url.split('/').reverse()[1];
                        console.log("savingRoomId", localStorage.lastRoomId);
                        loadingAnimation = true;
                    });

                    $('div.subnavigation ').click(function(event) {
                        loadingAnimation = false;

                        //Load Thinks
                        targetObj = $(this);
                        url = targetObj.data("url");
                        roomId = url.split('/').reverse()[1];
                        localStorage.setItem('lastRoomId', url.split('/').reverse()[1]);
                        console.log("savingRoomId", localStorage.lastRoomId);

                        //Menu Handling
                        $(".subnavigation").removeClass("active");
                        $("div.carousel-item").removeClass("active");
                        $("div.nav-link[data-room-id='" + roomId + "']").addClass("active");
                        $("div.carousel-item[data-room-id='" + roomId + "']").addClass("active");

                        //Load load content from URL
                        ajaxContentLoader($("div.carousel-item[data-room-id='" + roomId + "']"), url,
                            loadingAnimation);
                    });

                    //Desktop Arow Control
                    $(document).bind('keyup', function(e) {
                        if (e.which == 39) {
                            loadingAnimation = false;
                            $('#carouselExampleSlidesOnly').carousel('next');
                        } else if (e.which == 37) {
                            loadingAnimation = false;
                            $('#carouselExampleSlidesOnly').carousel('prev');
                        }
                    });
                });

                $('body').on('click', 'div.control-relay', function(event) {
                    navigator.vibrate([10]);
                    thisObj = $(this);
                    thisObj.html("<div class=\"spinner-border text-primary\" role=\"status\"></div>");
                    console.log(thisObj.data("url"));
                    $.ajax({
                        type: 'POST',
                        url: thisObj.data("url"),
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(msg) {
                            thisObj.html(msg.icon);
                            thisObj.data("url", msg.url)
                        },
                        error: function() {
                            //timeout
                        },
                        timeout: 3000,
                    });
                });

                var lastLoad = new Date().getTime();
                $("div#ajax-loader").click(function(event) {
                    thisObj = $(this);

                    localStorage.setItem('lastRoomId', thisObj.data("room-id"));


                    if (thisObj.hasClass("active") && (new Date().getTime() - lastLoad) < 9000) {
                        console.log((new Date().getTime() - lastLoad) + ' ms');
                        return;
                    }
                    $("#" + thisObj.data("target-id")).html(
                        '<div class="d-flex h-100"><div class="text-center m-auto"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div></div>'
                    );
                    console.log("Loading dynamic oontent");

                    console.log(thisObj.data("url"));

                    $(".subnavigation").removeClass("active");
                    thisObj.addClass("active");

                    $.ajax({
                        start_time: new Date().getTime(),
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        url: thisObj.data("url"),
                        success: function(msg) {
                            $("#" + thisObj.data("target-id")).html(msg);
                            console.log((new Date().getTime() - this.start_time) + ' ms');
                        },
                        error: function() {
                            console.log((new Date().getTime() - this.start_time) + ' ms');
                        },
                        timeout: 3000,
                    });
                });

                $('body').on('click', 'a#notification-control-load', function(event) {
                    console.log($(this).data("url"));
                    $("#notifications-list").html(
                        '<div style="height: 200px" class="d-flex"><div class="text-center m-auto"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div></div>'
                    );
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        url: $(this).data("url"),
                        success: function(msg) {
                            $("#notifications-list").html(msg);
                        },
                    });
                });

                $("div#notifications").on('shown.bs.modal', function() {
                    console.log("Loading Notifications");
                    $("#notifications-list").html(
                        '<div style="height: 200px" class="d-flex"><div class="text-center m-auto"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div></div>'
                    );
                    $.ajax({
                        type: 'GET',
                        url: '{{ route('notifications.list') }}',
                        success: function(msg) {
                            $("#notifications-list").html(msg);
                        },
                    });
                });
            </script>
        @endauth
        @yield('beforeBodyEnd')
</body>

</html>
