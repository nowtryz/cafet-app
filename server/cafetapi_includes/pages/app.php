<!doctype html>
<html>
  <head>
    <title>Loading...</title>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="initial-scale=1.0, width=device-width" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  </head>
  <body>
    <div class="app"></div>
    <script src="<?=@json_decode(file_get_contents(CAFET_DIR . 'dist/manifest.json'),true)['main.js']?>"></script>
  </body>
</html>