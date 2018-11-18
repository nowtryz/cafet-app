<?php
/**
 * Bootstrap for API initialisation, including constants definition and database connection
 * @package cafetapi
 * @copyright 2017-2018 Damien Djomby
 * @author Damien <damien.djmb@gmail.com>
 * @license cafetapi_content/license.md proprietary license
 * @license cafetapi_content/license.txt proprietary license
 * @since API 1.0.0
 * @version 1.0.0-alpha+server-php
 */

use cafetapi\io\DatabaseConnection;

/*
 * Definition of the needed base constants
 */
/**
 * Launch time of the application
 *
 * @var string|float START_TIME
 * @since API 1.0.0 (2018)
 */
define('START_TIME', microtime(true));
/**
 * The cafet API directory
 *
 * @var string CAFET_DIR
 * @since API 1.0.0 (2018)
 */
define('CAFET_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);

/**
 *  Reports all errors
 */
error_reporting(E_ALL);
/**
 *  Do not display errors for the end-users for security issue purposes
 */
// ini_set('display_errors','Off'); // lets show them for the moment
/**
 *  Set the logging file
 */
ini_set('error_log', CAFET_DIR . 'error.log');

/*
 * constants file
 */
require CAFET_DIR . 'cafetapi_includes' . DIRECTORY_SEPARATOR . 'constants.php';
/*
 * functions files
 */
require FUNCTIONS_DIR . 'utils.php';
require FUNCTIONS_DIR . 'cafet_basics.php';
require FUNCTIONS_DIR . 'logging.php';
require FUNCTIONS_DIR . 'authentication.php';

/*
 * Configure header check
 */
if (function_exists('cafet_headers_check')) {
    header_register_callback('cafet_headers_check');
}

/*
 * Register cafet class loader
 */
spl_autoload_register('cafet_class_autoload');

/*
 * Load configuration file
 */
cafet_load_conf_file();
/*
 * load all configurations, incule those from the database
 */
define('CONFIGURATIONS', cafet_get_configurations());

/*
 * Handle error for debug
 */
// TODO cafet_configure_error_handling();

/*
 * initialise database conection
 */
$DB = DatabaseConnection::getDatabaseConnectionInstance();
