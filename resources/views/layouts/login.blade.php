<!DOCTYPE html>
<html class="h-100">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ trans('panel.site_title') }}</title>

        <link href="{{ asset('css/app.css') }}" rel="stylesheet" />
        <link href="{{ asset('css/custom.css') }}" rel="stylesheet" />
        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>

        <script>
            window.sbgProduction = <?php echo json_encode([
                    'csrfToken' => csrf_token()
                ]); ?>;
        </script>

        @yield('styles')
    </head>

    <body class="c-app h-100">
        <div class="c-wrapper h-100" id="app">
            <div class="c-body h-100">
                <main class="c-main h-100">
                        @yield('content')
                </main>
            </div>
        </div>
        @yield('scripts')
    </body>
</html>
