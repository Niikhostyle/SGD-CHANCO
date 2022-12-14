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

        <link rel="shortcut icon" href="http://www.padrelascasas.cl/newplc/wp-content/themes/plc/favicon.ico" type="image/x-icon">



        <title>{{ config('app.name', 'Laravel') }}</title>


        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
        <!-- Bootstrap 3.4.1 -->
        <link rel="stylesheet" href="css/inicio/bootstrap.min.css">
        <link rel="stylesheet" href="css/inicio/font-awesome.min.css">
        <link rel="stylesheet" href="css/inicio/ionicons.min.css">
        <link rel="stylesheet" href="css/inicio/gijgo.min.css"  type="text/css" />
        <link rel="stylesheet" href="css/inicio/AdminLTE.min.css">
        <!-- AdminLTE Skins. Choose a skin from the css/skins folder instead of downloading all of them to reduce the load. -->
        <link rel="stylesheet" href="css/inicio/_all-skins.min.css">
        <link rel="stylesheet" href="css/inicio/pace.min.css">
        <link rel="stylesheet" href="css/inicio/adminlte_config.css">
        <link rel="stylesheet" href="css/admin_custom.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">



    </head>
    <body class="row hold-transition skin-blue ">
        <header class="main-header-guest">
                <span class="logo-lg">SISTEMA DE GESTIÓN DOCUMENTAL - PADRE LAS CASAS</span>
        </header>
        <div class="font-sans text-gray-900 antialiased">
            {{ $slot }}
        </div>


        <footer class="footer_login  m-b-0 text-sm">
            <div class="row">
                <div class="col-md-12">
                  <span class="help-block m-r-20 m-l-20 text-center">
                      2021 © Padre Las casas Maquehue 1441 - 45 2 590 000
                  </span>
                </div>
            </div>

        </footer>


        </body>

  <!-- jQuery 3.3.1 -->
  <script src="js/inicio/jquery.min.js"></script>
  <!-- Bootstrap 3.4.1 -->
  <script src="js/inicio/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>
  <script src="js/inicio/pace.min.js"></script>
  <script src="js/inicio/jquery.slimscroll.min.js"></script>
  <script src="js/inicio/adminlte.js"></script>

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
