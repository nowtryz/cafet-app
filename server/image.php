<?php
require 'cafetapi_load.php';

if(! isset($_GET['item']) || !$_GET['item']) {
    http_response_code(404);
    die('no resouce specified');
}

if (! isset($_GET['id']) || !$_GET['id'] ) {
    http_response_code(404);
    die('no image asked');
}

if($_GET['item'] == 'formula') {
    if (! cafet_render_formula_image(intval($_GET['id']), isset($_GET['dl']) || isset($_GET['download']))) {
        http_response_code(404);
        die('unable to render image');
    }
} elseif($_GET['item'] == 'product') {
    if (! cafet_render_product_image(intval($_GET['id']), isset($_GET['dl']) || isset($_GET['download']))) {
        http_response_code(404);
        die('unable to render image');
    }
}