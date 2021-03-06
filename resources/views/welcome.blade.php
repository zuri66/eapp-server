<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <title>Jeux de mots</title>
        <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        @if(config('url.url.base'))
        <base href="{{ config('url.url.base') }}">
        @endif
    </head>
    <body>
        <div id="app">
            <my-app></my-app>
        </div>
        <script src="{{ asset('js/app.js') }}" ></script>
    </body>
</html>
