<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--favicon-->
    <link rel="icon" href="/dist/img/logo-polines.png" type="image/png" />
    <!-- loader-->
    <link href="/assets/css/pace.min.css" rel="stylesheet" />
    <script src="/assets/js/pace.min.js"></script>
    <!-- Bootstrap CSS -->
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/bootstrap-extended.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="/assets/css/app.css" rel="stylesheet">
    <link href="/assets/css/icons.css" rel="stylesheet">
    <title>{{ env('SITAMA', 'SITAMA') }}</title>
</head>

<body class="">
    <!--wrapper-->
    <div class="wrapper">
        <div class="section-authentication-cover">
            <div class="">
                <div class="row g-0">

                    <div
                        class="col-12 col-xl-7 col-xxl-8 auth-cover-left align-items-center justify-content-center d-none d-xl-flex">

                        <div class="card shadow-none bg-transparent shadow-none rounded-0 mb-0">
                            <div class="card-body">
                                <img src="/assets/images/login-images/login-cover.svg"
                                    class="img-fluid auth-img-cover-login" width="500" alt="" />
                            </div>
                        </div>

                    </div>

                    <div class="col-12 col-xl-5 col-xxl-4 auth-cover-right align-items-center justify-content-center">
                        <div class="card rounded-0 m-0 shadow-none bg-transparent mb-0">
                            <div class="card-body p-sm-5">
                                <div class="">
                                    <div class="mb-3 text-center">
                                        <img src="/dist/img/logo-polines.png" width="80" alt="Logo Polines">
                                    </div>
                                    <div class="text-center mb-4">
                                        <h3 class="">{{ env('SITAMA', 'SITAMA') }}</h3>
                                        <p class="mb-0"></p>
                                    </div>
                                    <div class="form-body" id="show_hide_password">
                                        <form method="POST" action="{{ route('reset-password-action') }}">
                                            @csrf
                                            <input type="hidden" name="user" value="{{ encrypt($user->id) }}">
                                            <div class="row mb-3">
                                                <label for="password" class="col-md-4 col-form-label text-md-end">
                                                    Password</label>
                                                <div class="col-md-8">
                                                    <div class="input-group">
                                                        <input id="password" type="password"
                                                            class="form-control @error('password') is-invalid @enderror"
                                                            name="password" value="{{ $password ?? old('password') }}"
                                                            required autocomplete="off">
                                                        <a href="javascript:;"
                                                            class="input-group-text bg-transparent"><i
                                                                class='bx bx-hide'></i></a>
                                                    </div>
                                                </div>
                                                @error('password')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="row mb-3">
                                                <label for="confirmPassword"
                                                    class="col-md-4 col-form-label text-md-end">
                                                    Konfirmasi Password</label>
                                                <div class="col-md-8">
                                                    <div class="input-group">
                                                        <input id="confirmPassword" type="password"
                                                            class="form-control @error('confirmPassword') is-invalid @enderror"
                                                            name="confirmPassword"
                                                            value="{{ $confirmPassword ?? old('confirmPassword') }}"
                                                            required autocomplete="off">
                                                        <a href="javascript:;"
                                                            class="input-group-text bg-transparent"><i
                                                                class='bx bx-hide'></i></a>
                                                    </div>
                                                </div>
                                                @error('confirmPassword')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="row mb-0">
                                                <div class="col-md-6 offset-md-4">
                                                    <button type="submit" class="btn btn-primary">
                                                        {{ __('Konfirmasi') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!--end row-->
            </div>
        </div>
    </div>
    <!--end wrapper-->
    <!-- Bootstrap JS -->
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <!--plugins-->
    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/plugins/simplebar/js/simplebar.min.js"></script>
    <script src="/assets/plugins/metismenu/js/metisMenu.min.js"></script>
    <script src="/assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
    <!--Password show & hide js -->
    <script>
        $(document).ready(function() {
            $("#show_hide_password a").on('click', function(event) {
                event.preventDefault();
                if ($(this).parent().children('input').attr("type") == "text") {
                    $(this).parent().children('input').attr('type', 'password');
                    $(this).children('.bx').addClass("bx-hide");
                    $(this).children('.bx').removeClass("bx-show");
                } else if ($(this).parent().children('input').attr("type") ==
                    "password") {
                    $(this).parent().children('input').attr('type', 'text');
                    $(this).children('.bx').removeClass("bx-hide");
                    $(this).children('.bx').addClass("bx-show");
                }
            });
        });
    </script>
    <!--app JS-->
    <script src="/assets/js/app.js"></script>
</body>

</html>
