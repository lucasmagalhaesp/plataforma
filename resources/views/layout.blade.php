<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <!-- <link rel="stylesheet" href="https://raw.githubusercontent.com/daneden/animate.css/master/animate.css"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css">
    
    <!-- JQuery -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <!-- Bootstrap tooltips -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.4/umd/popper.min.js"></script>
    <!-- MDB core JavaScript -->
    <!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.8.10/js/mdb.min.js"></script> -->
    <!-- <script src="{{ asset('js/bootnavbar.js') }}" defer></script> -->

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <!--<link href="{{ asset('css/bootnavbar.css') }}" rel="stylesheet">-->
    <style>
        .nav-link{
            color: #FFF !important
        }
        
        .page-title{
            padding: 8px 0;
            margin: 0 -30px;
            background: #034732;
            border-radius: 0 !important
        }
        .page-title h1{
            font-size: 2.3em;
            text-align: center;
            font-family: 'Source Sans Pro';
            margin: 0;
            color: #FFF
        }
    </style>
</head>
<body>
    <div id="app">
        <b-navbar toggleable="lg" variant="info">
            <b-navbar-brand href="{{ url('/') }}" style="color: #FFF">Plataforma</b-navbar-brand>

            <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

            <b-collapse id="nav-collapse" is-nav>
                <b-navbar-nav>
                    <b-nav-item href="{{ url('/students') }}">Alunos</b-nav-item>
                    <b-nav-item href="{{ url('/courses') }}">Cursos</b-nav-item>
                    <b-nav-item href="{{ url('/registrations') }}">Matrículas</b-nav-item>
                </b-navbar-nav>

            </b-collapse>
        </b-navbar>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>
