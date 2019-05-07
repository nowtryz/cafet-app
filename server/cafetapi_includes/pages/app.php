<!doctype html>
<html>
  <head>
    <title>Test</title>
  </head>
  <body>
    <div class="app"></div>
    <script src="<?=@json_decode(file_get_contents(CAFET_DIR . 'dist/manifest.json'),true)['main.js']?>"></script>
  </body>
</html>