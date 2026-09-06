<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('theme/html/template/assets/img/favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('theme/html/template/assets/img/apple-touch-icon.png') }}">
    
    <link rel="stylesheet" href="{{ asset('theme/html/template/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/html/template/assets/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/html/template/assets/css/dataTables.bootstrap5.min.css') }}">
    
    <link rel="stylesheet" href="{{ asset('theme/html/template/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/html/template/assets/plugins/fontawesome/css/all.min.css') }}">

    <link rel="stylesheet" href="{{ asset('theme/html/template/assets/css/style.css') }}">
</head>
<body class="error-page">
    <div id="global-loader" >
        <div class="whirly-loader"> </div>
    </div>
    <div class="main-wrapper">
        <div class="error-box">
            <div class="error-img">
                <img src="{{ asset('theme/html/template/assets/img/authentication/error-404.png') }}" class="img-fluid" alt="Img">
            </div>
            <h3 class="h2 mb-3">Oops, something went wrong</h3>
            <p>Error 404 Page not found. Sorry the page you looking for doesn’t exist or has been moved</p>
            <a href="{{ url('/') }}" class="btn btn-primary">Back to Home</a>
        </div>
    </div>

    <script src="{{ asset('theme/html/template/assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('theme/html/template/assets/js/feather.min.js') }}"></script>
    <script src="{{ asset('theme/html/template/assets/js/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ asset('theme/html/template/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('theme/html/template/assets/js/script.js') }}"></script>
</body>
</html>
