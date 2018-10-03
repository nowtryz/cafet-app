<?php
/**
 * Function file for basic functions
 * 
 * @package essaim_cafet
 * @since API 1.0
 */
use cafetapi\Mail;
use cafetapi\ReturnStatement;
use cafetapi\config\Database;
use cafetapi\exceptions\CafetAPIException;
use cafetapi\io\DataFetcher;
use cafetapi\io\DatabaseConnection;
use cafetapi\modules\cafet_app\CafetApp;
use cafetapi\user\User;
use cafetapi\user\Perm;

global $basics_functions_loaded;

if (! isset($basics_functions_loaded) || ! $basics_functions_loaded) {
    $basics_functions_loaded = true;
    
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
     * Log some text in the API log file
     *
     * @param String $log
     *            the text to log
     * @global $avoid_log
     * @since API 1.0.0 (2018)
     */
    function cafet_log(string $log)
    {
        $logs = array();
        $tmp = explode("\r\n", $log);
        foreach ($tmp as $tmp2) $logs = array_merge($logs, explode("\n", $tmp2));
        foreach ($logs as $line) error_log('[' . date("d-M-Y H:i:s e") . '] CAFET ' . $line . PHP_EOL, 3, CAFET_DIR . 'debug.log');
    }

    /**
     * build return statement when an error occured
     *
     * @param String $error
     *            the error code
     * @since API 1.0.0 (2018)
     */
    function cafet_throw_error(string $error, string $additional_message = null, string $file = null, int $line = 0)
    {
        if (!cafet_is_app_request()) {
            throw new CafetAPIException($additional_message, null, null, $file, $line);
            die();
        }

        $result = new ReturnStatement("error", cafet_grab_error_infos($error, $additional_message));

        $result->print();

        exit();
    }
    
    
/**
 * @param error
 * @param additional_message
 */

    function cafet_grab_error_infos($error, $additional_message = null)
    {
        global $user;

        $sub_error = explode('-', $error);
        $errors = cafet_get_errors_info();

        if (empty($errors)) {
            $info = array(
                'error_code' => '01-500',
                'error_type' => '01 : server exception',
                'error_message' => 'Internal server error',
            );
        } else {
            $info = array(
                'error_code' => $error,
                'error_type' => $errors[$sub_error[0]]['def'],
                'error_message' => $errors[$sub_error[0]][$sub_error[1]],
            );
        }

        if (isset($additional_message))
            $info['additional_message'] = $additional_message;
        
        if(isset($user) && $user->hasPermission(Perm::GLOBAL_DEBUG)) {
            $debug_backtrace = debug_backtrace();
            $backtrace = '';
            
            while( ($trace = array_shift($debug_backtrace)) !== null) {
                $backtrace .= "\n";
                
                if(isset($trace['file'])) {
                    $backtrace .= 'in ' . $trace['file'];
                    $backtrace .= ' at line ' . $trace['line'] . ': ';
                } else {
                    $backtrace .= 'called by: ';
                }
                
                if(isset($trace['class'])) {
                    $backtrace .= $trace['class'] . $trace['type'];
                }
                
                $backtrace .= $trace['function'] . '()';
                
                if($trace['args']) {
                    $backtrace .= ' with args ' . str_replace('\\\\', '\\', json_encode($trace['args']));
                } else {
                    $backtrace .= ' with no args';
                }
            }
            
            if($additional_message) {
                $info['additional_message'] .= "\n" . $backtrace;
            } else {
                $info['additional_message'] = $backtrace;
            }
        }

        return $infos;
    }

    /**
     * Thow the cafet error corresponding to the http error
     * @param string|int $error
     */
    function cafet_http_error($error)
    {
        // Avoid logging for random 403 and 404 errors
        if(in_array($error, array('403', '404')) && !isset($_SERVER['HTTP_REFERER'])) return;

        $cafet_errors = cafet_get_errors_info();

        foreach ($cafet_errors as $errorgroup => $cafet_error) if (in_array($error, array_keys($cafet_error))) {
            cafet_log($error . ' Error: ' . $cafet_error[$error] . ': for "' . $_SERVER['REQUEST_URI'] . '"');
        }

        cafet_log('From: ' . $_SERVER['HTTP_REFERER']);
    }
    
    function cafet_error_handler($errno, $errmsg, $filename, $linenum, $errcontext)
    {
        static $errortype = array (
            E_ERROR              => 'Error',
            E_WARNING            => 'Warning',
            E_PARSE              => 'Parsing Error',
            E_NOTICE             => 'Notice',
            E_CORE_ERROR         => 'Core Error',
            E_CORE_WARNING       => 'Core Warning',
            E_COMPILE_ERROR      => 'Compile Error',
            E_COMPILE_WARNING    => 'Compile Warning',
            E_USER_ERROR         => 'User Error',
            E_USER_WARNING       => 'User Warning',
            E_USER_NOTICE        => 'User Notice',
            E_STRICT             => 'Runtime Notice',
            E_RECOVERABLE_ERROR  => 'Catchable Fatal Error'
        );
        
        $msg  = $errortype[$errno] . ': ';
        $msg .= $errmsg . ': ';
        $msg .= 'entry in ' . $filename;
        $msg .= ' on line ' . $linenum;
        
        cafet_throw_error('01-500', $msg);
    }
    
    function cafet_exception_handler(Throwable $e)
    {
        $msg  = get_class($e) . ' (' . $e->getCode() . '): ';
        $msg .= $e->getMessage() . ': ';
        $msg .= 'entry in ' . $e->getFile();
        $msg .= ' on line ' . $e->getLine();
        
        cafet_throw_error('01-500', $msg);
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
     * Initialise session
     *
     * @param bool $no_cookie
     *            if session cookie must be disabled
     * @param string $session_id
     *            if specified, the session id is set to the given one
     * @return string
     * @since API 1.0.0 (2018)
     */
    function cafet_init_session(bool $no_cookie = false, string $session_id = null): string
    {
        // Check if a session was already created and save it in case
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_commit();
            session_unset();
        }

        // Set session name and cookie name
        session_name("CAFET_" . strtoupper(cafet_get_configurations()['organisation']) . "_ID");

        // construe arguments
        if (isset($session_id))
            session_id($session_id);
        if ($no_cookie)
            ini_set('session.use_cookies', '0');

        // Start session
        session_start();

        // Check last activity and regenerate the session if timeout was reached
        if (isset($_SESSION['last_activity']) && $_SESSION['last_activity'] < time() - ini_get('session.gc_maxlifetime')) {
            session_unset();
        }

        // Save activity timestamp
        $_SESSION['last_activity'] = time();

        // Return the session id
        return session_id();
    }

    /**
     * Completly destroy the session
     *
     * @param string $session_id
     *            if specified, destroy the given session
     * @since API 1.0.0 (2018)
     */
    function cafet_destroy_session(string $session_id = null)
    {
        if (isset($session_id)) {
            if (session_status() == PHP_SESSION_ACTIVE) {
                $current_session = session_id();
                session_commit();
            }

            session_id($session_id);
            session_start();
        }

        session_unset();
        session_destroy();

        if (ini_get("session.use_cookies") && isset($_COOKIE[session_name()])) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }

        if (isset($current_session)) {
            session_id($current_session);
            session_start();
        }
    }

    /**
     * Return the hash version of a password according to the hash algorith specified in the congurations
     *
     * @param string $password
     *            the password to hash
     * @param string $pseudo
     *            [optional] the pseudo to hash with the old method
     * @return string the hash version of the password
     * @since API 1.0.0 (2018)
     */
    function cafet_generate_hashed_pwd(string $password, string $pseudo = null): string
    {
        $salt = bin2hex(random_bytes(16));
        $algo = CONFIGURATIONS['hash_algo'];

        return $algo . '.' . $salt . '.' . hash($algo, $salt . $password);
    }

    /**
     * Verify if a clear password corresponds to its hashed value
     *
     * @param string $password
     *            the password to check
     * @param string $hash
     *            the hashed password
     * @param string $pseudo
     *            [optional] the pseudo, to works with old site
     * @throws InvalidArgumentException if hash doesn't respect its synthax
     * @return bool if password is correct
     * @since API 1.0.0 (2018)
     */
    function cafet_verify_password(string $password, string $hash, string $pseudo = null): bool
    {
        if($hash == '')
            return false;

        $hash_info = explode('.', $hash);

        if (count($hash_info) == 1) {
            if (isset($pseudo))
                return sha1(CONFIGURATIONS['salt'] . $password . $pseudo) === $hash;
        }

        if (count($hash_info) < 3)
            throw new InvalidArgumentException('Wrong password hash format');

        return hash($hash_info[0], $hash_info[1] . $password) === $hash_info[2];
    }

    /**
     * Checks given login information
     *
     * @param string $pseudo_or_name
     *            the pseudo or the email entered
     * @param string $password
     *            the password entered
     * @return User a User object if logins are correct, false on failure
     * @since API 1.0.0 (2018)
     */
    function cafet_check_login(string $pseudo_or_name, $password): ?User
    {
        global $DB;

        $user = $DB->getUser($pseudo_or_name);

        if (! $user)
            return NULL;

        if (cafet_verify_password($password, $user->getHash(), $user->getPseudo()))
            return $user;
        else
            return NULL;
    }

    function cafet_get_logged_user(): ?User
    {
        $user = (object) unserialize($_SESSION['user']);

        if ($user instanceof User)
            return $user;
        else
            return null;
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