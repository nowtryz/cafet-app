<?php
require_once 'cafetapi_load.php';

use cafetapi\config\Config;
use cafetapi\exceptions\RequestFailureException;
use cafetapi\Logger;
use cafetapi\modules\rest\Rest;
use cafetapi\modules\rest\RootNode;
use cafetapi\modules\rest\errors\ServerError;

$api = new Rest(substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/')));


try {
    $api->printResponse(RootNode::handle($api));
} catch (RequestFailureException $ex) {
    if (Config::production) Logger::log('The following error occurred during an SQL query, your database may be outdated.');
    Logger::log($ex);
    $api->printResponse(ServerError::internalServerError());
} catch (Error | Exception $e) {
    Logger::log($e);
    $api->printResponse(ServerError::internalServerError());
}
