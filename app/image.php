<?php
require 'cafetapi_load.php';

if(! isset($_GET['item']))
    die('no resouce specified');

if (! isset($_GET['id']))
    die('no image asked');

if($_GET['item'] == 'formula') {
    if (! cafet_render_formula_image(intval($_GET['id']), isset($_GET['dl']) || isset($_GET['download'])))
        die('unable to render image');
} elseif($_GET['item'] == 'product') {
    if (! cafet_render_product_image(intval($_GET['id']), isset($_GET['dl']) || isset($_GET['download'])))
        die('unable to render image');
}