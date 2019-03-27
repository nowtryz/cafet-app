<?php
use cafetapi\ErrorPageBuilder;

require_once 'cafetapi_load.php';

$error_code = $_SERVER['REDIRECT_STATUS'];

cafet_http_error($error_code);

(new ErrorPageBuilder($error_code))->print();