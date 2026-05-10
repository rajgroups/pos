<!DOCTYPE html>
<html lang="en">
<head>
    @stack('meta')
    @include('layouts.admin.dependency.css')
    @stack('css')
</head>

<body>

    <div class="main-wrapper">

        @include('layouts.admin.partition.header')

        @include('layouts.admin.partition.left-menu')

        <div class="page-wrapper">
            <div class="content">

                @yield('content')

                @include('layouts.admin.partition.footer')

            </div>
        </div>
    </div>

    {{-- 1️⃣ Load required JS files FIRST --}}
    @include('layouts.admin.dependency.js')
    @stack('scripts')
</body>
</html>
