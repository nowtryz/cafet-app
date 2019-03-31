<?php
use cafetapi\ErrorPageBuilder;
use cafetapi\Logger;

require_once 'cafetapi_load.php';

$error_code = $_SERVER['REDIRECT_STATUS'];

Logger::logHttpError($error_code);

(new ErrorPageBuilder($error_code))->print();