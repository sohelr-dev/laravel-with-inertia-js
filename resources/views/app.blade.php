<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title inertia>{{ config('app.name', 'Laravel + Inertia + Vue') }}</title>

    @vite(['resources/js/app.js'])
    @inertiaHead
</head>
<body>
    {{-- for Inertia requests --}}
    @inertia
</body>
</html>
