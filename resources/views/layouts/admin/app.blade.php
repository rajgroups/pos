<!DOCTYPE html>
<html lang="en">
   <head>
      @stack('meta')
      @include('layouts.admin.dependency.css')
      @stack('css')
   </head>
   <body>
      <div class="main-wrapper">
         <!-- main @s -->
         <!-- sidebar @s -->
         @include('layouts.admin.partition.header')
         <!-- main header @s -->
         @include('layouts.admin.partition.left-menu')
         {{-- @include('layouts.admin.partition.horizontal') --}}
         <div class="page-wrapper">
            <div class="content">
               <!-- main header @e -->
               <!-- start Page-content -->
               @section('content')

               @show
               <!-- footer @s -->
               @include('layouts.admin.partition.footer')
               <!-- footer @e -->
            </div>
         </div>
         <!-- end main content-->
      </div>
      <!-- JAVASCRIPT -->
      @include('layouts.admin.dependency.js')
      @stack('scripts')
   </body>
</html>