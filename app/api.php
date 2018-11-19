<?php
require_once 'cafetapi_load.php';

use cafetapi\modules\rest\Rest;

new Rest(substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/')));