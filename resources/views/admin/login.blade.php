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
    <title>Login</title>

    <!--Google font-->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,500,600,700" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/dashboard.css') }}">
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
        <div class="container-fluid">
            <!--login page start-->
            <div class="authentication-main">
                <div class="row">
                    <div class="col-md-4 p-0">
                        <div class="auth-innerleft">
                            <div class="text-center">
                                <img src="{{ asset('images/logo-login.png') }}" class="logo-login" alt="">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8 p-0">
                        <div class="auth-innerright">
                            <div class="authentication-box">
                                <h4>LOGIN</h4>
                                <h6>Enter your Email and Password For Login or Signup</h6>
                                <div class="card mt-4 p-4 mb-0">
                                    <form class="theme-form" id="formLogin">
                                        <div class="form-group">
                                            <label class="col-form-label pt-0">Email</label>
                                            <input type="text" name="email" class="form-control form-control-lg" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-form-label">Password</label>
                                            <input type="password" name="password" class="form-control form-control-lg" required>
                                        </div>
                                        <div class="checkbox p-0">
                                            <input id="checkbox1" type="checkbox">
                                            <label for="checkbox1">Remember me</label>
                                        </div>
                                        <div class="form-group form-row mt-3 mb-0">
                                            <button type="submit" class="btn btn-secondary">LOGIN</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--login page end-->
        </div>
    </div>
    <!--page-wrapper Ends-->

    <script src="{{ asset('js/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('assets/dashboard.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('vendor/loadingoverlay.js') }}"></script>

    <script>
        const loading = (type, selector = null, option = { image: '', text: 'Loading...' }) => {
            if(selector) { $(selector).LoadingOverlay(type, option) } else { $.LoadingOverlay(type, option) }
        }
        $(document).ready(() => {
            $('#formLogin').on('submit', (e) => {
                e.preventDefault()
                loading('show')
                new Promise((resolve, reject) => {
                    $axios.post(`{{ route('admin.login') }}`, $('#formLogin').serialize())
                        .then(({data}) => {
                            $swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Anda berhasil login',
                            })
                            loading('hide')
                            window.location.replace(data.redirect)
                        })
                        .catch(err => {
                            console.log(err)
                            loading('hide')
                            $swal.fire({
                                icon: 'error',
                                title: 'Login Gagal',
                                text: 'Username atau Password salah',
                            })
                        })
                })
            })
        })
    </script>

</body>

</html>
