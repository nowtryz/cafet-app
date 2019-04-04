<?php
/**
 * Bootstrap for API initialisation, including constants definition and database connection
 * @package cafetapi
 * @copyright 2017-2018 Damien Djomby
 * @author Damien <damien.djmb@gmail.com>
 * @license cafetapi_content/license.md proprietary license
 * @license cafetapi_content/license.txt proprietary license
 * @since API 0.1.0
 * @version 0.2.3
 */

use cafetapi\io\DatabaseConnection;
use cafetapi\Kernel;
use cafetapi\config\Config;
use cafetapi\Autoloader;

/*
 * Definition of the needed base constants
 */
/**
 * Launch time of the application
 *
 * @var string|float START_TIME
 * @since API 0.1.0 (2018)
 */
define('START_TIME', microtime(true));
/**
 * The cafet API directory
 *
 * @var string CAFET_DIR
 * @since API 0.1.0 (2018)
 */
define('CAFET_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);

/*
 * constants file
 */
require CAFET_DIR . 'cafetapi_includes' . DIRECTORY_SEPARATOR . 'constants.php';
/*
 * functions files
 */
require FUNCTIONS_DIR . 'utils.php';
require FUNCTIONS_DIR . 'cafet_basics.php';
require FUNCTIONS_DIR . 'authentication.php';

/*
 * Load configuration file
 */
if (file_exists(CONTENT_DIR . 'config.php')) require_once CONTENT_DIR . 'config.php';
else require_once INCLUDES_DIR . 'default_configurations.php';

/*
 *  Reports all errors
 */
error_reporting(E_ALL);
/*
 *  Do not display errors for the end-users for security issue purposes
 */
ini_set('display_errors', Config::debug ? 'On' : 'Off');
/**
 *  Set the logging file
 */
ini_set('error_log', CAFET_DIR . 'error.log');

/*
 * Configure header check
 */
if (function_exists('cafet_headers_check')) {
    header_register_callback('cafet_headers_check');
}

/*
 * Register cafet class loader
 */
require CLASS_DIR . 'Autoloader.php';
$loader = new Autoloader();
$loader->addNamespace('cafetapi\modules\rest', CLASS_DIR . 'modules' . DIRECTORY_SEPARATOR . 'rest');
$loader->addNamespace('cafetapi\modules\cafet_app', CLASS_DIR . 'modules' . DIRECTORY_SEPARATOR . 'cafet_app');
$loader->addNamespace('cafetapi\modules', CLASS_DIR . 'modules');
$loader->addNamespace('cafetapi\data', CLASS_DIR . 'data');
$loader->addNamespace('cafetapi\exceptions', CLASS_DIR . 'exceptions');
$loader->addNamespace('cafetapi\io', CLASS_DIR . 'io');
$loader->addNamespace('cafetapi\user', CLASS_DIR . 'user');
$loader->addNamespace('cafetapi', CLASS_DIR);
$loader->register();
Kernel::setAutoloader($loader);

/*
 * Init kernel
 */
Kernel::init();

/*
 * Handle error for debug
 */
// TODO cafet_configure_error_handling();

/*
 * initialise database conection
 */
DatabaseConnection::getDatabaseConnectionInstance();
