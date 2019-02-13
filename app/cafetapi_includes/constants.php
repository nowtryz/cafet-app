<?php
/*
 * Definition of usefull constances for the application
 */
if (! defined('CAFET_DIR'))
    define('CAFET_DIR', realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR));

/**
 * Server version number as semantic versioning
 *
 * @var unknown
 * @since API 1.0.0 (2018)
 */
define('SERVER_VERSION', 'O.2.0');
/**
 * API version number for api calls
 *
 * @var unknown
 * @since API 1.0.0 (2018)
 */
define('API_VERSION', '1.0.0-beta');
/**
 * The cafet API classes directory
 * <br/><em>'cafetapi/'</em>
 *
 * @var string CLASS_DIR
 * @since API 1.0.0 (2018)
 */

define('CLASS_DIR', CAFET_DIR . 'cafetapi' . DIRECTORY_SEPARATOR);
/**
 * The cafet API content directory
 * <br/><em>cafetapi_content/</em>
 *
 * @var string CONTENT_DIR
 * @since API 1.0.0 (2018)
 */
define('CONTENT_DIR', CAFET_DIR . 'cafetapi_content' . DIRECTORY_SEPARATOR);
/**
 * The cafet API languages directory
 * <br/><em>cafetapi_languages/</em>
 *
 * @var string LANGUAGES_DIR
 * @since API 1.0.0 (2018)
 */
define('LANGUAGES_DIR', CAFET_DIR . 'cafetapi_languages' . DIRECTORY_SEPARATOR);
/**
 * The cafet API includes directory
 * <br/><em>cafetapi_includes/</em>
 *
 * @var string INCLUDES_DIR
 * @since API 1.0.0 (2018)
 */
define('INCLUDES_DIR', CAFET_DIR . 'cafetapi_includes' . DIRECTORY_SEPARATOR);
/**
 * The cafet API pages directory
 * <br/><em>cafetapi_includes/pages/</em>
 *
 * @var string INCLUDES_DIR
 * @since API 1.0.0 (2018)
 */
define('PAGES_DIR', INCLUDES_DIR . 'pages' . DIRECTORY_SEPARATOR);
/**
 * The cafet API function libraries directory
 * <br/><em>cafetapi_includes/pages/</em>
 *
 * @var string FUNCTIONS_DIR
 * @since API 1.0.0 (2018)
 */
define('FUNCTIONS_DIR', INCLUDES_DIR . 'functions' . DIRECTORY_SEPARATOR);
/**
 * The cafet API mail templates directory
 * <br/><em>cafetapi_includes/pages/</em>
 *
 * @var string FUNCTIONS_DIR
 * @since API 1.0.0 (2018)
 */
define('MAILS_DIR', CONTENT_DIR . 'mails' . DIRECTORY_SEPARATOR);

/**
 * Whether the application should use URL Rewriting or not
 *
 * @var bool URL_REWRITE
 * @since API 1.0.0 (2018)
 */
define('URL_REWRITE', function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules()));