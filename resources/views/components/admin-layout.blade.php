<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>{{ $title }} | Diwakar Enterprise CMS</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

</head>

<body>

@include('admin.layouts.partials.header')

<div class="admin-wrapper">

    @include('admin.layouts.partials.sidebar')

    <main class="admin-content">

        {{ $slot }}

    </main>

</div>

@include('admin.layouts.partials.footer')

</body>

</html>