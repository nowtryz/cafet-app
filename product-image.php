<?php
require 'cafetapi_load.php';

if (! isset($_GET['id']))
    die('no image asked');

if (! cafet_render_product_image(intval($_GET['id']), isset($_GET['dl']) || isset($_GET['download'])))
    die('unable to render image');