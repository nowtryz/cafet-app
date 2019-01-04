<?php
/**
 * Function file for configurations functions
 *
 * @package cafetapi
 * @since API 1.0
 */

use cafetapi\Mail;
use cafetapi\config\Database;
use cafetapi\io\ClientManager;
use cafetapi\io\DatabaseConnection;
use cafetapi\io\FormulaManager;
use cafetapi\io\ProductManager;
use cafetapi\modules\cafet_app\CafetApp;
use cafetapi\user\Group;
use cafetapi\io\OptionManager;

if (! defined('configurations_functions_loaded') ) {
    define('configurations_functions_loaded', true);
    
    /**
     * Load configurations from the yaml configuration file
     *
     * @since API 1.0.0 (2018)
     */
    function cafet_load_conf_file()
    {
        try {
            require_once CONTENT_DIR . 'config.php';
            
            if (! defined("DB_INFO")) {
                $db_info = array();
                
                $db_info['driver'] = Database::driver;
                $db_info['host'] = Database::host;
                $db_info['port'] = Database::port;
                $db_info['database'] = Database::database;
                $db_info['username'] = Database::username;
                $db_info['password'] = Database::password;
                
                /**
                 * Databases configurations
                 *
                 * @var array
                 * @since API 1.0.0 (2018)
                 */
                define("DB_INFO", $db_info);
            }
            
            if (! defined("DEFAULT_CONFIGURATIONS")) {
                /**
                 * Default configurations
                 *
                 * @var array
                 * @since API 1.0.0 (2018)
                 */
                define("DEFAULT_CONFIGURATIONS", (new ReflectionClass('cafetapi\config\Defaults'))->getConstants());
            }
        } catch (Exception $e1) {
            try {
                $conf = json_decode(implode('', file(CONTENT_DIR . "config.json")), true);
                
                if (isset($conf['database']) && array_key_exists('driver', $conf['database']) && array_key_exists('host', $conf['database']) && array_key_exists('database', $conf['database']) && array_key_exists('username', $conf['database']) && array_key_exists('password', $conf['database'])) {
                    if (! defined("DB_INFO")) {
                        /**
                         *
                         * @ignore
                         */
                        define("DB_INFO", $conf['database']);
                    }
                } else {
                    cafet_throw_error("01-001");
                }
                
                if (isset($conf['defaults'])) {
                    if (! defined("DEFAULT_CONFIGURATIONS")) {
                        /**
                         *
                         * @ignore
                         */
                        define("DEFAULT_CONFIGURATIONS", $conf['defaults']);
                    }
                }
            } catch (Exception $e2) {
                cafet_log($e2->getMessage());
            }
        }
    }
    
    /**
     * Gives the errors messages of the application
     *
     * @return array an array containing all errors lmessages
     * @since API 1.0.0 (2018)
     */
    function cafet_get_errors_info(): array
    {
        static $errors = array();
        
        if (! empty($errors))
            return $errors;
            
            $file = implode('', file(CONTENT_DIR . 'errors.json'));
            if ($errors = json_decode($file, true))
                return $errors;
                else
                    return array();
    }
    
    /**
     * Gives configurations depending on registered properties and default ones
     *
     * @return array configurations
     * @since API 1.0.0 (2018)
     */
    function cafet_get_configurations(): array
    {
        static $conf = array();
        
        if (! empty($conf))
            return $conf;
            
            if (! defined("DEFAULT_CONFIGURATIONS"))
                cafet_load_conf_file();
                
                foreach (DEFAULT_CONFIGURATIONS as $key => $value)
                    $conf[$key] = $value;
                    foreach (OptionManager::getConfigurations() as $key => $value)
                        $conf[$key] = $value;
                        
                        return $conf;
    }
    
    /**
     * Gives the specified configuration
     * @param string $key the configuration name wanted
     * @return mixed the value of the configuration if defined or false
     */
    function cafet_get_configuration(string $key)
    {
        static $conf = array();
        
        if (empty($conf)) $conf = cafet_get_configurations();
        
        return @$conf[$key];
    }
}