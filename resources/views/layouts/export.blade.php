<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">

        <title>{{ $title ?? 'Style' }}</title>

        <link href="{{ asset('css/print.css') }}" rel="stylesheet" />

        @yield('styles')

        @stack('scripts')
    </head>

    <body>
        <main class="c-main">
            <div class="container-fluid">
                @yield('content')
            </div>
        </main>
    </body>
</html>
