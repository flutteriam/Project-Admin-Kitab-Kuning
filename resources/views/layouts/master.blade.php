<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="universal admin is super flexible, powerful, clean & modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords"
        content="admin template, universal admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="pixelstrap">
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/x-icon" />
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" type="image/x-icon" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kitab Kuning')</title>

    <!--Google font-->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,500,600,700" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/fontawesome/fontawesome.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/themify/themify.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">


    <style>
        @font-face {
            font-family: "KFGQPC_Naskh";
            src: local("KFGQPC Uthman Taha Naskh"), url("{{ asset('fonts/KFC_naskh.otf') }}") format("opentype");
        }

        @font-face {
            font-family: "PDMS_IslamicFont";
            font-style: normal;
            font-weight: normal;
            src: url("{{ asset('fonts/PDMS_IslamicFont.eot') }}?#iefix") format("embedded-opentype"),
                url("{{ asset('fonts/PDMS_ISLAMICFONT.ttf') }}") format("truetype");
        }

        .arab {
            font-family: 'PDMS_IslamicFont';
        }
    </style>]]
    @yield('css')
</head>

<body>

    <!-- Loader starts -->
    <div class="loader-wrapper">
        <div class="loader bg-white">
            <div class="line"></div>
            <div class="line"></div>
            <div class="line"></div>
            <div class="line"></div>
            <h4>Peace be upon you <span>&#x263A;</span></h4>
        </div>
    </div>
    <!-- Loader ends -->

    <!--page-wrapper Start-->
    <div class="page-wrapper">

        <!--Page Header Start-->
        @include('partials.dashboard.header')
        <!--Page Header Ends-->

        <!--Page Body Start-->
        <div class="page-body-wrapper">
            <!--Page Sidebar Start-->
            @include('partials.dashboard.sidebar')
            <!--Page Sidebar Ends-->

            <div class="page-body">

                <!-- Container-fluid starts -->
                <div class="container-fluid">
                    <div class="page-header">
                        <div class="row">
                            <div class="col-lg-6">
                                <h3>{{ request()->segment(2) != '' ? strtoupper(request()->segment(2)) : strtoupper(request()->segment(1)) }}
                                </h3>
                            </div>
                            <div class="col-lg-6">
                                <ol class="breadcrumb pull-right">
                                    @php
                                        $path = explode('/', request()->path());
                                    @endphp
                                    @forelse ($path as $key => $value)
                                        @if (is_numeric($value) || strpos($value, '='))
                                            @php
                                                continue;
                                            @endphp
                                        @endif
                                        @if ($key == 0)
                                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i
                                                        class="fa fa-home"></i></a></li>
                                            <li class="breadcrumb-item active">{{ ucfirst($value) }}</li>
                                        @else
                                            <li class="breadcrumb-item active">{{ ucfirst($value) }}</li>
                                        @endif
                                    @empty
                                    @endforelse
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Container-fluid Ends -->

                <!-- Container-fluid starts -->
                <div class="container-fluid">
                    @yield('content')
                </div>
                <!-- Container-fluid starts -->
            </div>
        </div>
        <!--Page Body Ends-->

    </div>
    <!--page-wrapper Ends-->

    <script src="{{ asset('js/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/popper.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/bootstrap.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('assets/dashboard.js') }}"></script>
    <script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('vendor/loadingoverlay.js') }}"></script>
    <script src="{{ asset('vendor/select2/select2.full.min.js') }}"></script>
    <script>
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute("content")
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            }
        })

        const loading = (type, selector = null, option = {
            image: '',
            text: 'Loading...'
        }) => {
            if (selector) {
                $(selector).LoadingOverlay(type, option)
            } else {
                $.LoadingOverlay(type, option)
            }
        }

        const throwErr = err => {
            if (err.response.status == 422) {
                let message = err.response.data.errors
                let teks_error = ''
                $.each(message, (i, e) => {
                    if (e.length > 1) {
                        $.each(e, (id, el) => {
                            teks_error += `<p>${el}</p>`
                        })
                    } else {
                        teks_error += `<p>${e}</>`
                    }
                })
                $swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    html: teks_error,
                })
            } else {
                let response = err.response.data
                $swal.fire({
                    icon: 'error',
                    title: response.message.head,
                    text: response.message.body,
                })
            }
        }
    </script>
    @yield('js')

</body>

</html>
