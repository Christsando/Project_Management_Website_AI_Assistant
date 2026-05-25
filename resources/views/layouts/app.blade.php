<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        {{ config('app.name', 'KelolaIN') }} |
        {{ ucwords(str_replace('.', ' ', Route::currentRouteName())) }}
    </title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Icon FAS & FAB -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script href=""></script>
</head>

<body class="font-sans antialiased bg-[#F8FAFC]">
    <div class="flex min-h-screen p-4">
        <div class="w-60 shrink-0">
            @include('layouts.navigation')
        </div>


        <!-- Page Content -->
        <main class="flex-1">
            <!-- Page Heading -->
            @isset($header)
                <header class="pl-4">
                    {{ $header }}
                </header>
            @endisset

            {{ $slot }}
        </main>
    </div>
</body>

</html>
