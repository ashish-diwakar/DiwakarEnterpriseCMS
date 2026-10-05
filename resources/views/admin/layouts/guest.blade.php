<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>{{ $title ?? 'Authentication' }}</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

</head>

<body>

<div class="min-h-screen flex items-center justify-center bg-gray-100">

    {{ $slot }}

</div>

</body>

</html>