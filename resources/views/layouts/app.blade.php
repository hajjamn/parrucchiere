<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('/images/favicon.png') }}" type="image/png">


    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', "Dream's Center") }}</title>


    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- jQuery PRIMA -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Usando Vite -->
    @vite(['resources/js/app.js'])


    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

</head>

<body>
    <div id="app">


        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <div class="">
                    {{-- <a class="nav-link" href="{{ route('admin.service-logs.index') }}"> --}}

                        <img src="{{ asset('images/favicon.png') }}" alt="Logo" style="height: 40px;">
                        {{-- </a> --}}

                </div>
                {{-- config('app.name', 'Laravel') --}}

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        {{-- <li class="nav-item">
                            <a class="nav-link" href="{{url('/admin/service-logs') }}">{{ __('Home') }}</a>
                        </li> --}}
                        @auth
                            @if (auth()->user()->role === 'admin')
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.users.index') }}">Operatori</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.services.index') }}">Servizi</a>
                                </li>
                            @endif
                            <li class="nav-item">
                                @if (auth()->user()->role !== 'admin')
                                    <a class="nav-link" href="{{ route('admin.users.show', auth()->id()) }}">
                                        Operatore
                                    </a>
                                @endif
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.service-logs.index') }}">Prestazioni</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.clients.index') }}">Clienti</a>
                            </li>

                        @endauth

                        @if (auth()->user()->role === 'admin')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.summary.index') }}">Riepilogo</a>
                            </li>
                        @endif

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Accedi') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Registrati') }}</a>
                                </li>
                            @endif
                        @else
                            <li class=" nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->first_name }}
                                    {{ strtoupper(substr(Auth::user()->last_name, 0, 1)) }}.

                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">Profilo</a>
                                    <a class="dropdown-item" href="{{ route('admin.switch-user.index') }}">Cambia
                                        Operatore</a>

                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        Esci
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>

                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="">
            @yield('content')
        </main>
    </div>

    @stack('scripts')

    <!-- jQuery (necessario per Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

</body>

</html>