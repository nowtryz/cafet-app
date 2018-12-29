<?php
require_once 'cafetapi_load.php';

use cafetapi\modules\rest\Rest;
use cafetapi\modules\rest\RootNode;
use cafetapi\modules\rest\errors\ServerError;

$api = new Rest(substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/')));


try {
    $api->printResponse(RootNode::handle($api));
} catch (Error | Exception $e) {
    cafet_log($e);
    $api->printResponse(ServerError::internalServerError());
}