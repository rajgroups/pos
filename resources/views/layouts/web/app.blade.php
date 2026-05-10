<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SKY MAHAL - Home</title>
    @include('layouts.web.dependency.css')
</head>

<body>
    <header class="float-start w-100">
        <!-- Top Header-->
        @include('layouts.web.partition.top-header')

        <!-- Top Header-->
        @include('layouts.web.partition.header')


  </header>

    <!-- slider-->
    @include('layouts.web.partition.slider')

    <main class="float-start w-100">

        <!-- start Page-content -->
        @section('content')

        @show
        </div>
        <!-- end main content-->

    {{-- footer  --}}
    @include('layouts.web.partition.footer')

    <!-- JAVASCRIPT -->
    @include('layouts.web.dependency.js')


        <!-- Top Header-->
        @include('layouts.web.partition.mobilemenu')
</body>
</html>

