<?php
/**
 * Function file for basic functions
 * 
 * @package cafetapi
 * @since API 1.0
 */

use cafetapi\Mail;
use cafetapi\config\Database;
use cafetapi\io\DataFetcher;
use cafetapi\io\DatabaseConnection;
use cafetapi\modules\cafet_app\CafetApp;

if (! defined('basics_functions_loaded') ) {
    define('basics_functions_loaded', true);
    
    function cafet_is_app_request() {
        return isset($_POST['origin']) && $_POST['origin'] == 'cafet_app';
    }

    /**
     * Listen post request for app call
     *
     * @since API 1.0.0 (2018)
     */
    function cafet_listen_app_request()
    {
        if (cafet_is_app_request()) {
            error_reporting(-1);
            set_error_handler('cafet_error_handler');
            set_exception_handler('cafet_exception_handler');
            
            try {
                new CafetApp();
            } catch (Exception $e) {
                cafet_throw_error('01-003', $e->getMessage());
            }
            exit();
        }
    }

    function cafet_class_autoload($class)
    {
        static $classlist = array();

        if (! $classlist) {
            cafet_list_classes(CLASS_DIR, $classlist);
        }

        $name = $class;

        while (strpos($name, '\\') !== false) {
            $name = substr($name, strpos($name, '\\') + 1);
        }

        if (array_key_exists($name, $classlist))
            foreach ($classlist[$name] as $file)
                require_once $file;
    }

    function cafet_list_classes(string $dir, array &$classlist)
    {
        if (is_dir($dir)) {
            $files = scandir($dir);

            foreach ($files as $file) {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $file) && ! in_array($file, array(
                    '.',
                    '..'
                ))) {
                    cafet_list_classes($dir . $file . DIRECTORY_SEPARATOR, $classlist);
                } 
                elseif (strpos($file, '.php') == (strlen($file) - strlen('.php'))) {
                    $classlist[substr($file, 0, - strlen('.php'))][] = $dir . $file;
                }
            }
        }
    }

    /**
     * Load all class in a directory and its subfolders.
     * Warning it loads every php files
     *
     * @param string $dir
     *            the directory to analyse
     * @since API 1.0.0 (2018)
     */
    function cafet_load_class_folder(string $dir)
    {
        if (is_dir($dir)) {
            $files = scandir($dir);

            foreach ($files as $file) {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $file) && ! in_array($file, array(
                    '.',
                    '..'
                )))
                    cafet_load_class_folder($dir . $file . DIRECTORY_SEPARATOR);
                elseif (strpos($file, '.php'))
                    require_once $dir . $file;
            }
        }
    }

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
     * @global $DB
     * @since API 1.0.0 (2018)
     */
    function cafet_get_configurations(): array
    {
        static $conf = array();
        global $DB;

        if (! empty($conf))
            return $conf;

        if (! defined("DEFAULT_CONFIGURATIONS"))
            cafet_load_conf_file();
        if (! isset($DB))
            $DB = DatabaseConnection::getDatabaseConnectionInstance();

        foreach (DEFAULT_CONFIGURATIONS as $key => $value)
            $conf[$key] = $value;
        foreach ($DB->getConfigurations() as $key => $value)
            $conf[$key] = $value;

        return $conf;
    }

    function cafet_render_product_image(int $product_id, bool $dwl = false)
    {
        if (headers_sent())
            return false;

        $product = DataFetcher::getInstance()->getProduct($product_id);

        if (! $product)
            return false;

        header('content-type: ' . guess_image_mime($product->getImage()));

        if ($dwl)
            header('Content-Disposition: attachment; filename="' . $product->getName() . get_base64_image_format($product->getImage()) . '"');

        echo base64_decode($product->getImage());

        exit();
    }

    function cafet_send_reload_request(int $client_id)
    {
        $f = DataFetcher::getInstance();
        $c = $f->getClient($client_id);
        $mail = new Mail('reload_request', $c->getEmail());
        $mail->setVar('surname', $c->getSurname());
        $mail->setVar('name', $c->getFamilyNane());
        $mail->setVar('balance', number_format($c->getBalance(), 2, ',', ' '));

        $expenses = '';

        foreach ($c->getLastExpenses() as $expense) {
            $expenses .= '<tr><td>' . 'Le ' . $expense->getDate()->getFormatedDate() . ' à ' . $expense->getDate()->getFormatedTime() . '</td><td>' . number_format($expense->getTotal(), 2, ',', ' ') . ' €' . '</td><td>' . number_format($expense->getBalanceAfterTransaction(), 2, ',', ' ') . ' €' . '</td></tr>';
        }

        $mail->setVar('expenses', $expenses);

        $mail->send();
    }

    

    /**
     * Checks headers before sending them to the client
     *
     * @since API 1.0.0 (2018)
     */
    function cafet_headers_check()
    {
        $list = headers_list();
        $headers_to_remove = array(
            'X-Powered'
        );

        foreach ($list as $header) {
            foreach ($headers_to_remove as $to_remove) {
                if (strpos($header, $to_remove) !== false) {
                    header_remove(explode(':', $header)[0]);
                }
            }
        }
    }

    /**
     * Return the time difference between launch and now
     *
     * @return float the duration
     * @since API 1.0.0 (2018)
     */
    function cafet_execution_duration(): float
    {
        return microtime(true) - START_TIME;
    }

    /**
     * Only for debug
     *
     * @since API 1.0.0 (2018)
     */
    function cafet_dump_server_vars()
    {
        if (session_status() == PHP_SESSION_ACTIVE) {
            echo '<h2>session_id</h2>';
            echo session_id();
            echo '<h2>$_SESSION</h2>';
            var_dump($_SESSION);
        }

        echo '<h2>$_COOKIE</h2>';
        var_dump($_COOKIE);
        
        echo '<h2>$_GET</h2>';
        var_dump($_GET);

        echo '<h2>$_REQUEST</h2>';
        var_dump($_REQUEST);

        echo '<h2>$_FILES</h2>';
        var_dump($_FILES);

        echo '<h2>$_SERVER</h2>';
        var_dump($_SERVER);

        echo '<h2>Last SQL Error</h2>';
        var_dump(DatabaseConnection::getLastQueryErrors());

        if (session_status() == PHP_SESSION_ACTIVE && isset($_SESSION['user'])) {
            echo '<h2>Logged user</h2>';
            var_dump(cafet_get_logged_user());
        }

        echo '<h2>Execution duration</h2>';
        echo 'Computed in ' . cafet_execution_duration() . ' seconds';
    }
}