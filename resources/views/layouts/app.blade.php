<!DOCTYPE html>
<html ng-app="Claims" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
    <link rel="shortcut icon" href="assets/images/mbr-96x96.png" type="image/x-icon">
    <meta name="description" content="">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Support IT') }}</title>
    <!-- Bootstrap CSS v5.0.2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.1/css/all.css"
        integrity="sha384-O8whS3fhG2OnA5Kas0Y9l3cfpmYjapjI0E4theH4iuMD+pLhbf6JI0jIMfYcK3yZ" crossorigin="anonymous">
    {{-- sweet alert --}}
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-sweetalert/dist/sweetalert.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/toastr/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/angucomplete-alt/angucomplete-alt.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/summernote/dist/summernote.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/summernote/dist/summernote-bs5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css') }}">
    <link rel='stylesheet' href='{{ asset('bower_components/angular-loading-bar/build/loading-bar.min.css') }}' type='text/css' media='all' />
    <link rel="stylesheet"
        href="{{ asset('bower_components/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/v/bs5/dt-1.11.5/af-2.3.7/b-2.2.2/b-colvis-2.2.2/b-html5-2.2.2/b-print-2.2.2/cr-1.5.5/date-1.1.2/fc-4.0.2/fh-3.2.2/kt-2.6.4/r-2.2.9/rr-1.2.8/sc-2.0.5/sb-1.3.2/sp-2.0.0/sl-1.3.4/sr-1.1.0/datatables.min.css" />
    <!-- Fonts -->
    <link rel="stylesheet" href="{{ asset('assets/dropdown/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/theme/css/style.css') }}">
    <link rel="preload"
        href="https://fonts.googleapis.com/css?family=Jost:100,200,300,400,500,600,700,800,900,100i,200i,300i,400i,500i,600i,700i,800i,900i&display=swap"
        as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet"
            href="https://fonts.googleapis.com/css?family=Jost:100,200,300,400,500,600,700,800,900,100i,200i,300i,400i,500i,600i,700i,800i,900i&display=swap">
    </noscript>
    <link rel="preload" as="style" href="{{ asset('assets/mobirise/css/mbr-additional.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/mobirise/css/mbr-additional.css') }}" type="text/css">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('index.css') }}">
    {{-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> --}}
</head>

<body>

    <section data-bs-version="5.1" class="menu menu2 cid-sYvkVHPdpz mb-2" once="menu" id="menu2-9">
        <nav class="navbar navbar-dropdown navbar-fixed-top navbar-expand-lg">
            <div class="container">
                <div class="navbar-brand">
                    <span class="navbar-logo">
                        <a href="#">
                            <img src="{{ asset('assets/images/mbr-96x96.png') }}" alt="" style="height: 3rem;">
                        </a>
                    </span>
                    <span class="navbar-caption-wrap"><a class="navbar-caption text-black display-5"
                            href="#">{{ config('app.name') }}</a></span>
                </div>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-bs-toggle="collapse"
                    data-target="#navbarSupportedContent" data-bs-target="#navbarSupportedContent"
                    aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                    <div class="hamburger">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav nav-dropdown" data-app-modern-menu="true">
                        @auth
                            @if (auth()->user()->is_admin)
                                <li class="nav-item dropdown"><a class="nav-link link text-black dropdown-toggle display-4"
                                        href="#" data-toggle="dropdown-submenu" data-bs-toggle="dropdown"
                                        data-bs-auto-close="outside" aria-expanded="false"><span
                                            class="fa fa-cog mbr-iconfont mbr-iconfont-btn"></span>Config</a>
                                    <div class="dropdown-menu" aria-labelledby="dropdown-undefined"><a
                                            class="text-black dropdown-item text-primary display-4"
                                            href="#!/state">States</a><a
                                            class="text-black dropdown-item text-primary display-4"
                                            href="#!/profil">Profils</a><a
                                            class="text-black dropdown-item text-primary display-4"
                                            href="#!/application">Applications</a><a
                                            class="text-black dropdown-item text-primary display-4"
                                            href="#!/user">Utilisateurs</a><a
                                            class="text-black dropdown-item text-primary display-4"
                                            href="#!/priorite">Priorités</a></div>
                                </li>
                            @endif
                            @if (auth()->user()->is_gestionnaire)
                                <li class="nav-item dropdown"><a class="nav-link link text-black dropdown-toggle display-4"
                                        href="#" data-toggle="dropdown-submenu" data-bs-toggle="dropdown"
                                        data-bs-auto-close="outside" aria-expanded="false"><span
                                            class="fa fa-exclamation-triangle mbr-iconfont mbr-iconfont-btn"></span>Gestion
                                        réclamation</a>
                                    <div class="dropdown-menu" aria-labelledby="dropdown-undefined">
                                        <a class="text-black dropdown-item text-primary display-4"
                                            href="#!/reclamation-dashboard">Tableau de bord</a>
                                        <a class="text-black dropdown-item text-primary display-4"
                                            href="#!/reclamation">Réclamations</a>
                                    </div>
                                </li>
                            @endif
                            <li class="nav-item dropdown"><a class="nav-link link text-black dropdown-toggle show display-4"
                                    href="#" data-toggle="dropdown-submenu" data-bs-toggle="dropdown"
                                    data-bs-auto-close="outside" aria-expanded="true"><span
                                        class="fa fa-user mbr-iconfont mbr-iconfont-btn"></span>{{ auth()->user()->name }}</a>
                                <div class="dropdown-menu show" aria-labelledby="dropdown-undefined" data-bs-popper="none">
                                    <a class="text-black dropdown-item text-primary display-4" href="#!/account">Profil</a>
                                    <a class="text-black dropdown-item display-4" ng-click="logout()">Déconnexion</a>
                                </div>
                            </li>
                        @else
                            <li class="nav-item"><a class="nav-link link text-black show display-4" href="#!/login">Se
                                    connecter</a></li>
                        @endauth
                    </ul>

                    <div class="navbar-buttons mbr-section-btn"><a class="btn btn-primary display-4"
                            href="#!/create-reclamation"><span
                                class="fa fa-flag-checkered mbr-iconfont mbr-iconfont-btn"></span>Signaler un
                            problème</a>
                    </div>
                </div>
            </div>
        </nav>
    </section>

    <main class="mb-5 mt-1">
        <div class="container fluid">
            <div class="row">
                <div class="col-12" ng-view="">
                    {{-- place d'inection des page angular --}}
                </div>
            </div>
        </div>
    </main>

    <section data-bs-version="5.1" style="position: fixed; bottom: 0px; width: 100%;"
        class="footer7 cid-sYh95UUUCx mt-3" once="footers" id="footer7-1">
        <div class="container">
            <div class="media-container-row align-center mbr-white">
                <div class="col-12">
                    <p class="mbr-text mb-0 mbr-fonts-style display-7">
                        © Copyright 2022 DSI - Tous droits réservés</p>
                </div>
            </div>
        </div>
    </section>


    <!-- Jquery JavaScript Libraries -->
    <script src="{{ asset('bower_components/jquery/dist/jquery.min.js') }}"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
        integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
    </script>
    <script defer src="https://use.fontawesome.com/releases/v5.1.1/js/all.js"
        integrity="sha384-BtvRZcyfv4r0x/phJt9Y9HhnN5ur1Z+kZbKVgzVBAlQZX4jvAuImlIz+bG7TS00a" crossorigin="anonymous">
    </script>
    <script src="{{ asset('bower_components/bootstrap-sweetalert/dist/sweetalert.js') }}"></script>
    <script src="{{ asset('bower_components/toastr/toastr.min.js') }}"></script>
    <!-- Scripts -->
    <script src="{{ asset('assets/smoothscroll/smooth-scroll.js') }}"></script>
    <script src="{{ asset('assets/dropdown/js/navbar-dropdown.js') }}"></script>
    <script src="{{ asset('assets/theme/js/script.js') }}"></script>
    {{-- <script src="{{ asset('js/app.js') }}" defer></script> --}}
    {{-- angular --}}
    <script src="{{ asset('bower_components/angular/angular.min.js') }}"></script>
    <script src="{{ asset('bower_components/angular-animate/angular-animate.min.js') }}"></script>
    <script src="{{ asset('bower_components/angular-sanitize/angular-sanitize.min.js') }}"></script>
    <script src="{{ asset('bower_components/angular-route/angular-route.min.js') }}"></script>
    <script src="{{ asset('bower_components/angular-filter/dist/angular-filter.min.js') }}"></script>
    <script src="{{ asset('bower_components/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('bower_components/angular-dragdrop/src/angular-dragdrop.min.js') }}"></script>
    <script src="{{ asset('bower_components/angucomplete-alt/angucomplete-alt.js') }}"></script>
    <script src="{{ asset('bower_components/summernote/dist/summernote.min.js') }}"></script>
    <script src="{{ asset('bower_components/summernote/dist/lang/summernote-fr-FR.js') }}"></script>
    <script src="{{ asset('bower_components/summernote/dist/summernote-bs5.min.js') }}"></script>
    <script src="{{ asset('bower_components/angular-summernote/dist/angular-summernote.min.js') }}"></script>
    <script src="{{ asset('bower_components/select2/select2.min.js') }}"></script>
    <script src="{{ asset('bower_components/angular-ui-select2/src/select2.js') }}"></script>
    <script src="{{ asset('bower_components/angular-drag-and-drop-lists/angular-drag-and-drop-lists.js') }}"></script>
    <script src="{{ asset('bower_components/angular-bootstrap-switch/dist/angular-bootstrap-switch.min.js') }}"></script>
    <script src="{{ asset('bower_components/bootstrap-switch/dist/js/bootstrap-switch.min.js') }}"></script>
    <script type='text/javascript' src="{{  asset('bower_components/angular-loading-bar/build/loading-bar.min.js') }}"></script>
    <script type="text/javascript"
        src="https://cdn.datatables.net/v/bs5/dt-1.11.5/af-2.3.7/b-2.2.2/b-colvis-2.2.2/b-html5-2.2.2/b-print-2.2.2/cr-1.5.5/date-1.1.2/fc-4.0.2/fh-3.2.2/kt-2.6.4/r-2.2.9/rr-1.2.8/sc-2.0.5/sb-1.3.2/sp-2.0.0/sl-1.3.4/sr-1.1.0/datatables.min.js">
    </script>
    <script src="{{ asset('bower_components/angular-datatables/dist/angular-datatables.min.js') }}"></script>
    {{-- app angular --}}
    <script src="{{ asset('angular/app.js') }}"></script>
    {{-- services --}}
    <script src="{{ asset('angular/services/state.service.js') }}"></script>
    <script src="{{ asset('angular/services/profil.service.js') }}"></script>
    <script src="{{ asset('angular/services/application.service.js') }}"></script>
    <script src="{{ asset('angular/services/user.service.js') }}"></script>
    <script src="{{ asset('angular/services/intervenant.service.js') }}"></script>
    <script src="{{ asset('angular/services/applicationprofil.service.js') }}"></script>
    <script src="{{ asset('angular/services/categoriereclamation.service.js') }}"></script>
    <script src="{{ asset('angular/services/gestionnaire.service.js') }}"></script>
    <script src="{{ asset('angular/services/auth.service.js') }}"></script>
    <script src="{{ asset('angular/services/reclamation.service.js') }}"></script>
    <script src="{{ asset('angular/services/priorite.service.js') }}"></script>
    <script src="{{ asset('angular/services/comment.service.js') }}"></script>
    {{-- controllers --}}
    <script src="{{ asset('angular/controllers/home.controller.js') }}"></script>
    <script src="{{ asset('angular/controllers/state/state.controller.js') }}"></script>
    <script src="{{ asset('angular/controllers/profil/profil.controller.js') }}"></script>
    <script src="{{ asset('angular/controllers/profil/profil.show.controller.js') }}"></script>
    <script src="{{ asset('angular/controllers/application/application.index.controller.js') }}"></script>
    <script src="{{ asset('angular/controllers/application/application.show.controller.js') }}"></script>
    <script src="{{ asset('angular/controllers/user/user.index.controller.js') }}"></script>
    <script src="{{ asset('angular/controllers/user/user.show.controller.js') }}"></script>
    <script src="{{ asset('angular/controllers/auth/login.controller.js') }}"></script>
    <script src="{{ asset('angular/controllers/reclamation/reclamation.new.js') }}"></script>
    <script src="{{ asset('angular/controllers/reclamation/reclamation.index.js') }}"></script>
    <script src="{{ asset('angular/controllers/reclamation/reclamation.show.js') }}"></script>
    <script src="{{ asset('angular/controllers/auth/account.controller.js') }}"></script>
    <script src="{{ asset('angular/controllers/reclamation/reclamation.dashboard.js') }}"></script>
    <script src="{{ asset('angular/controllers/priorite/priorite.controller.js') }}"></script>
    <script src="{{ asset('angular/controllers/auth/password.reset.controller.js') }}"></script>
    <script src="{{ asset('angular/controllers/comment/comment.reclameur.js') }}"></script>
    <script src="{{ asset('angular/controllers/reclamation/reclamation.satisfaction.controller.js') }}"></script>
    <script src="{{ asset('angular/controllers/reclamation/reclamation.insatisfaction.controller.js') }}"></script>

</body>

</html>
