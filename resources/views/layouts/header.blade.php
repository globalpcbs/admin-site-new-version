<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Login Global Pcbs </title>

    <!-- vendor css -->
    <link href="{{ asset('lib/font-awesome/css/font-awesome.css')}}" rel="stylesheet">
    <link href="{{ asset('lib/Ionicons/css/ionicons.css')}}" rel="stylesheet">

    <!-- Bracket CSS -->
    <link rel="stylesheet" href="{{ asset('css/bracket.css')}}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body>
    {{ $slot }}
    @vite('resources/js/app.js')
    <script src="{{ asset('lib/jquery/jquery.js')}}"></script>
    <script src="{{ asset('lib/popper.js/popper.js')}}"></script>
    <script src="{{ asset('lib/bootstrap/bootstrap.js')}}"></script>
    @livewireScripts
    <script>
    console.log("Livewire JS test:", window.livewire);
    // console.log('login form');
    </script>
</body>

</html>