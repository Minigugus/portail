<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <main>
            @yield('content')
        </main>

		<nav class="navbar navbar-expand-md navbar-light" style="top: 0;">
			<div class="container">
				<a class="navbar-brand" href="{{ url('/') }}">
					{{ config('app.name', 'Laravel') }}
				</a>
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>

				<div class="collapse navbar-collapse" id="navbarSupportedContent">
					<!-- Left Side Of Navbar -->
					<ul class="navbar-nav mr-auto">

					</ul>

					<!-- Right Side Of Navbar -->
					<ul class="navbar-nav ml-auto">

						@auth
							<li class="nav-item dropdown">
								<a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									{{ Auth::user()->lastname }} {{ Auth::user()->firstname }} <span class="caret"></span>
								</a>
								<div class="dropdown-menu" aria-labelledby="navbarDropdown">
									<a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
										Se déconnecter
									</a>

									<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
										@csrf
									</form>
								</div>
							</li>
						@else
							<li class="nav-item dropdown">
								<a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									Se connecter <span class="caret"></span>
								</a>
								<div class="dropdown-menu" aria-labelledby="navbarDropdown">
									@foreach (config('auth.services') as $name => $provider)
										<a class="dropdown-item" href="{{ route('login.show', ['provider' => $name, 'redirect' => $redirect ?? url()->previous()]) }}">
											{{ $provider['name'] }}
										</a>
									@endforeach
										<a class="dropdown-item" href="{{ route('login', ['see' => 'all', 'redirect' => $redirect ?? url()->previous()]) }}">
											Tout voir
										</a>
								</div>
							</li>
						@endauth

					</ul>
				</div>
			</div>
		</nav>

		<footer class="navbar navbar-expand-md navbar-light d-flex justify-content-around" style="bottom: 0;">
			<a href={{ isset($service) ? $service : URL::previous() }}>Retourner à la page précédente</a>
			<a href={{ route('home') }}>Retourner au portail</a>
		</footer>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('script')
</body>
</html>
