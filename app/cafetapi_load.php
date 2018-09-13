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
/*
 * constants file
 */
require CAFET_DIR . 'cafetapi_includes' . DIRECTORY_SEPARATOR . 'constants.php';
/*
 * functions files
 */
require INCLUDES_DIR . 'functions/utils.php';
require INCLUDES_DIR . 'functions/cafet_basics.php';

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
 * initialisation: include all classes and load default configurations
 */
// cafet_load_class_folder( CLASS_DIR ); //classes now autoloaded

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
$DB = new DatabaseConnection(DB_INFO);
