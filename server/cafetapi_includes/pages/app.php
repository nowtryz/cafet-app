<!doctype html>
<html>
  <head>
    <title>Loading...</title>
    
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="theme-color" content="#000000" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <link rel="manifest" href="<?=cafetapi\config\Config::url?>/manifest.json" />
    <link rel="shortcut icon" href="<?=cafetapi\config\Config::url?>/favicon.ico" />
    
    <link rel="stylesheet" href="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.css" />
    <script src="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jvectormap/2.0.4/jquery-jvectormap.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" type="text/css" media="screen" />

    <!--     Fonts and icons     -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.7/css/all.css" />
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  </head>
  <body>
    <div class="app"></div>
    <noscript>You need to enable JavaScript to run this app. </noscript>
    <script>
      var SERVER_URL = "<?=cafetapi\config\Config::url?>";
    </script>
    <script src="<?=@json_decode(file_get_contents(CAFET_DIR . 'dist/manifest.json'),true)['main.js']?>"></script>
  </body>
</html>