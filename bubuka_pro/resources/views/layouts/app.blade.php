<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href='https://unpkg.com/css.gg@2.0.0/icons/css/image.css' rel='stylesheet'>
    <link href='https://unpkg.com/css.gg@2.0.0/icons/css/folder.css' rel='stylesheet'>
    <link href='https://unpkg.com/css.gg@2.0.0/icons/css/add-r.css' rel='stylesheet'>
    <link href='https://unpkg.com/css.gg@2.0.0/icons/css/list.css' rel='stylesheet'>
    <link href='https://unpkg.com/css.gg@2.0.0/icons/css/lock.css' rel='stylesheet'>
    <link href='https://unpkg.com/css.gg@2.0.0/icons/css/file-document.css' rel='stylesheet'>
    <link href='https://unpkg.com/css.gg@2.0.0/icons/css/pen.css' rel='stylesheet'>
    <link href='https://unpkg.com/css.gg@2.0.0/icons/css/trash.css' rel='stylesheet'>
    <link href='https://unpkg.com/css.gg@2.0.0/icons/css/add.css' rel='stylesheet'>
    <link href='https://unpkg.com/css.gg@2.0.0/icons/css/log-in.css' rel='stylesheet'>
    <link href='https://unpkg.com/css.gg@2.0.0/icons/css/log-out.css' rel='stylesheet'>
    <link href='https://unpkg.com/css.gg@2.0.0/icons/css/toggle-off.css' rel='stylesheet'>
    <link href='https://unpkg.com/css.gg@2.0.0/icons/css/toggle-on.css' rel='stylesheet'>
    <link href='https://unpkg.com/css.gg@2.0.0/icons/css/math-minus.css' rel='stylesheet'>
    <link href='https://unpkg.com/css.gg@2.0.0/icons/css/user-add.css' rel='stylesheet'>
    <link href='https://unpkg.com/css.gg@2.0.0/icons/css/user-remove.css' rel='stylesheet'>
    <link href='https://unpkg.com/css.gg@2.0.0/icons/css/software-download.css' rel='stylesheet'>
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js', 'resources/css/app.css'])
</head>
<body>
<div id="app">
    @yield('content')

</div>
</body>
</html>
