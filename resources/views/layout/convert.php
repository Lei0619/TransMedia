<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    {{-- Logo Section --}}
    @include('partials.converter-logo')

    {{-- Main Converter Container --}}
    <div class="converter-container">
        {{-- Platform Selection --}}
        @include('partials.platform-buttons')

        {{-- Format Selection --}}
        @include('partials.format-buttons')

        {{-- Main Content Area --}}
        @yield('content')
    </div>

    {{-- History Section --}}
    @include('partials.conversion-history')

    {{-- Footer --}}
    @include('partials.converter-footer')

    {{-- Include conversion scripts --}}
    @include('partials.converter-scripts')

    {{-- Page specific scripts --}}
    @yield('scripts')
</body>

</html>