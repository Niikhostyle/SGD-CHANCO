<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="noindex, nofollow">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="theme-color" content="#ffffff">

      

        <title>{{ config('app.name', 'Laravel') }}</title>

        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="stylesheet" href="{{ env('CODIGO_SGD').'/css/custom.css' }}">
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
        <link rel="stylesheet" href="css/inicio/font-awesome.min.css">
        <!-- Bootstrap 3.4.1 -->
        {{-- <link rel="stylesheet" href="css/inicio/bootstrap.min.css">
        
        <link rel="stylesheet" href="css/inicio/ionicons.min.css">
        <link rel="stylesheet" href="css/inicio/gijgo.min.css"  type="text/css" />
        <link rel="stylesheet" href="css/inicio/AdminLTE.min.css">
        <!-- AdminLTE Skins. Choose a skin from the css/skins folder instead of downloading all of them to reduce the load. -->
        <link rel="stylesheet" href="css/inicio/_all-skins.min.css">
        <link rel="stylesheet" href="css/inicio/pace.min.css">
        <link rel="stylesheet" href="css/inicio/adminlte_config.css">
        <link rel="stylesheet" href="css/admin_custom.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic"> --}}

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>

    </head>
    <body class="d-flex flex-column hold-transition skin-blue ">        
        <div class="font-sans text-gray-900 antialiased">
            {{ $slot }}
        </div>


        <footer class="footer_login w-100 m-b-0 text-sm bg-light">
            <div class="">
                <div class="  text-center">
                  <span class="help-block m-r-20 m-l-20">
                  {!! config('sgd.footer_txt') !!}
                  </span>
                </div>
            </div>

        </footer>


        </body>

  {{-- <!-- jQuery 3.3.1 -->
  <script src="js/inicio/jquery.min.js"></script>
  <!-- Bootstrap 3.4.1 -->
  <script src="js/inicio/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>
  <script src="js/inicio/pace.min.js"></script>
  <script src="js/inicio/jquery.slimscroll.min.js"></script>
  <script src="js/inicio/adminlte.js"></script> --}}

  <!-- page script -->
  <script type="text/javascript">
      // To make Pace works on Ajax calls
      $(document).ajaxStart(function() { Pace.restart(); });

      // Ajax calls should always have the CSRF token attached to them, otherwise they won't work
      $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          });


      var activeTab = $('[href="' + location.hash.replace("#", "#tab_") + '"]');
      location.hash && activeTab && activeTab.tab('show');
      $('.nav-tabs a').on('shown.bs.tab', function (e) {
          location.hash = e.target.hash.replace("#tab_", "#");
      });
  </script>


      <!-- JavaScripts -->


  </html>
