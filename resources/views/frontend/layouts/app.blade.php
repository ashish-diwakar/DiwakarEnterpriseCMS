<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'Website')
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body>

@include('frontend.layouts.partials.header')

<main>

    @yield('content')

</main>

@include('frontend.layouts.partials.footer')

</body>
</html>