<?php
require 'cafetapi_load.php';

$error_code = $_SERVER['REDIRECT_STATUS'];

cafet_http_error($error_code);

echo $error_code . ' error';