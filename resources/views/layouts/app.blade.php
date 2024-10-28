<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ env('APP_NAME', 'PBL IK-TI') }}</title>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('') }}plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('') }}plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    @stack('css')
    <link rel="stylesheet" href="{{ asset('') }}dist/css/adminlte.min.css">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <style>
        /* Tooltip container */
        .my-tooltip {
            position: relative;
            display: inline-block;
            border-bottom: 1px dotted black;
            /* If you want dots under the hoverable text */
        }

        /* Tooltip text */
        .my-tooltip .tooltiptext {
            visibility: hidden;
            width: auto;
            background-color: #555;
            color: #fff;
            text-align: center;
            padding: 4px 8px;
            border-radius: 6px;

            /* Position the tooltip text */
            position: absolute;
            z-index: 1;

            /* Fade in tooltip */
            opacity: 0;
            transition: opacity 0.3s;
        }

        /* Tooltip arrow */
        .my-tooltip .tooltiptext::after {
            content: "";
            position: absolute;
            border-width: 5px;
            border-style: solid;
        }

        /* Top tooltip */
        .my-tooltip.top .tooltiptext {
            bottom: 125%;
            left: 50%;
            margin-left: -90px;
        }

        .my-tooltip.top .tooltiptext::after {
            top: 100%;
            left: 50%;
            border-color: #555 transparent transparent transparent;
        }

        /* Bottom tooltip */
        .my-tooltip.bottom .tooltiptext {
            top: 125%;
            left: 50%;
            margin-left: -60px;
        }

        .my-tooltip.bottom .tooltiptext::after {
            bottom: 100%;
            left: 50%;
            margin-left: -5px;
            border-color: transparent transparent #555 transparent;
        }

        /* Left tooltip */
        .my-tooltip.left .tooltiptext {
            top: 0px;
            right: 105%;
        }

        .my-tooltip.left .tooltiptext::after {
            top: 50%;
            right: -5px;
            margin-top: -5px;
            border-color: transparent transparent transparent #555;
        }

        /* Show the tooltip text when you mouse over the tooltip container */
        .my-tooltip:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <!-- Preloader -->
    {{-- <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__shake" src="{{ asset('') }}dist/img/logo-polines.png" alt="Polines Logo"
            height="80" width="80">
    </div> --}}
    <div class="wrapper">
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars"></i></a>
                </li>
            </ul>

            <ul class="navbar-nav ml-auto ">
                <li class="nav-item dropdown user-menu">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                        <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

                        <li class="user-header bg-info" style="height: auto;">
                            <p>
                                {{ Auth::user()->name }}
                                <small>Politeknik Negeri Semarang</small>
                            </p>
                        </li>
                        <li class="user-footer">
                            <a href="{{ route('profil.index') }}" class="btn btn-default btn-flat">Profil</a>
                            <a href="#" class="btn btn-default btn-flat float-right" data-toggle="modal"
                                data-target="#modal-logout"><i class="fas fa-sign-out-alt"></i> <span>Keluar</span></a>
                        </li>

                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                        <i class="fas fa-expand-arrows-alt"></i>
                    </a>
                </li>
            </ul>
        </nav>
        <div class="modal fade" id="modal-logout" data-backdrop="static" tabindex="-1" role="dialog"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                <div class="modal-content">

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <div class="modal-body text-center">
                            <h5>Apakah anda ingin keluar ?</h5>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-sm btn-default btn-flat"
                                data-dismiss="modal">Tidak</button>
                            <a class="btn btn-sm btn-info btn-flat float-right" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                            this.closest('form').submit();"><span>Ya,
                                    Keluar</span></a>
                        </div>
                    </form>
                </div>

            </div>

        </div>

        <aside class="main-sidebar main-sidebar-custom sidebar-dark-info elevation-4">
            <a href="{{ url('') }}" class="brand-link">
                <img src="{{ asset('') }}dist/img/logo-polines.png" alt="Logo Polines"
                    class="brand-image elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light "><strong>{{ env('SITAMA', 'SITAMA') }}</strong></span>
            </a>
            <div class="sidebar">
                <nav class="mt-2">
                    @include('layouts.sidebar')
                </nav>
            </div>

            {{-- <div class="sidebar-custom">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a class="btn btn-info btn-block" href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                this.closest('form').submit();"><i
                            class="fas fa-sign-out-alt"></i> <span>Keluar</span></a>
                </form>
            </div> --}}
        </aside>

        <div class="content-wrapper">
            @yield('content')
        </div>

        <footer class="main-footer">
            <div class="float-right d-none d-sm-block">
                <b>Version</b> 1.0.0
            </div>
            <strong>&copy; {{ date('Y') }} <i>Task Force</i> PBL TI-2C Polines</strong>
        </footer>
    </div>

    <script src="{{ asset('') }}plugins/jquery/jquery.min.js"></script>
    <script src="{{ asset('') }}plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('') }}plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
    <script src="{{ asset('') }}plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset('') }}plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset('') }}plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="{{ asset('') }}plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="{{ asset('') }}plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="{{ asset('') }}plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="{{ asset('') }}plugins/jszip/jszip.min.js"></script>
    <script src="{{ asset('') }}plugins/pdfmake/pdfmake.min.js"></script>
    <script src="{{ asset('') }}plugins/pdfmake/vfs_fonts.js"></script>
    <script src="{{ asset('') }}plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="{{ asset('') }}plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="{{ asset('') }}plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>
    @stack('js')
    <script src="{{ asset('') }}dist/js/adminlte.min.js"></script>
    <script>
        $(function() {
            $("#datatable-main").DataTable({
                "responsive": true,
                lengthMenu: [
                    [50, 100, 200, -1],
                    [50, 100, 200, 'All']
                ],
                pageLength: 50,
                // "buttons": ["excel"]
                //"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#datatable-main_wrapper .col-md-6:eq(0)');

            $('#datatable-sub').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });

        $('.confirm-button').click(function(event) {
            var form = $(this).closest("form");
            event.preventDefault();
            swal({
                    title: `Hapus data`,
                    icon: "warning",
                    buttons: {
                        confirm: {
                            text: 'Ya'
                        },
                        cancel: 'Tidak'
                    },
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        form.submit();
                    }
                });
        });
        $('.validasi-button').click(function(event) {
            var form = $(this).closest("form");
            event.preventDefault();
            swal({
                title: `Simpan data`,
                icon: "warning",
                buttons: {
                    confirm: {
                        text: 'Ya',
                        value: true,
                        visible: true,
                        className: "btn btn-success",
                        closeModal: true
                    },
                    cancel: {
                        text: "Tidak",
                        value: null,
                        visible: true,
                        className: "btn btn-danger",
                        closeModal: true,
                    }
                },
                dangerMode: true,
            }).then((willSubmit) => {
                if (willSubmit) {
                    form.submit();
                }
            });
        });
    </script>
</body>

</html>
