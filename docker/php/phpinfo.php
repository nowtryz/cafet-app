<?php
require 'cafetapi_load.php';
if (cafetapi\config\Config::debug) php_info();
else (new ErrorPageBuilder(404))->print();