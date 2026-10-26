<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | Time Tracker</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('vendor/alpinejs/alpine.min.js') }}" defer></script>
</head>
<body>
    <a class="skip-link" href="#main">Skip to main content</a>
    <header class="site-header">
        <div class="container site-header-inner">
            <a class="brand" href="{{ route('employees.index') }}">Time Tracker</a>
            <nav aria-label="Main">
                <ul class="nav" role="list">
                    <li><x-ui.nav-link route="employees.index">Employees</x-ui.nav-link></li>
                </ul>
            </nav>
        </div>
    </header>
    <main id="main" class="container">
        @if (session('status') || $errors->any())
            <div class="messages" role="alert">
                @if (session('status'))
                    <p class="message message-success">{{ session('status') }}</p>
                @endif
                @if ($errors->any())
                    <div class="message message-error">
                        <p class="message-title">There is a problem</p>
                        <ul class="message-list">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endif
        @yield('content')
    </main>
</body>
</html>
