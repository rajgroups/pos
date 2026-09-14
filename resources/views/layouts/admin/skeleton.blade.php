<!DOCTYPE html>
<html lang="en">
   <head>
      @stack('meta')
      @include('layouts.admin.dependency.css')
      @stack('css')
   </head>
   <body>
      <div class="main-wrapper">
        @section('content')

        @show
      @include('layouts.admin.dependency.js')
      @stack('scripts')
   </body>
</html>
