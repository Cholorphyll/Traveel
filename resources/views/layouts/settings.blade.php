<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name', 'Travell') }} - Settings</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
	 @php
        $currentUrl = url()->current();
        $path = parse_url($currentUrl, PHP_URL_PATH) ?? '';
        $isIndianDomain = str_contains($currentUrl, 'travell.co.in');
        $mainDomain = 'https://www.travell.co' . $path;
        $indianDomain = 'https://www.travell.co.in' . $path;
    @endphp
    <link rel="canonical" href="{{ $currentUrl }}" />
    <link rel="alternate" hrefLang="en" href="{{ $mainDomain }}"/>
    <link rel="alternate" hrefLang="en-in" href="{{ $indianDomain }}"/>
    <link rel="alternate" hrefLang="x-default" href="{{ $mainDomain }}"/>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap"
    rel="stylesheet" />
    
    <!-- Styles -->
    <link rel="stylesheet" type="text/css" href="{{ asset('/settings/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('/settings/css/style.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('/settings/css/responsive.css') }}" />    
    
    <!-- Page specific styles -->
    @yield('styles')
</head>
<body>
    @yield('content')
    
    <!-- Scripts -->
    <script type="text/javascript" src="{{ asset('/settings/js/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/settings/js/bootstrap.bundle.min.js') }}"></script>
    
    <!-- Page specific scripts -->
    @yield('scripts')
</body>
</html>
