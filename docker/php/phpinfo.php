<?php
require 'cafetapi_load.php';
if (cafetapi\config\Config::debug) phpinfo();
else (new ErrorPageBuilder(404))->print();