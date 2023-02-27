<!DOCTYPE html>
<html>
  <head>
    <title>@yield('title')</title>
  </head>
  <body>
    @include('partials.header')

    <div class="container">
      @yield('content')
    </div>
  </body>
</html>
