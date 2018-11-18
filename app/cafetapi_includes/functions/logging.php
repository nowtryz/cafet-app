<?php
/**
 * Function file for logging functions
 *
 * @package cafetapi
 * @since API 1.0
 */

use cafetapi\ReturnStatement;
use cafetapi\exceptions\CafetAPIException;
use cafetapi\user\Perm;

if (!defined('logging_functions_loaded')) {
    define('logging_functions_loaded', true);

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
        foreach ($logs as $line) error_log('[' . date("d-M-Y H:i:s e") . '] CAFET ' . $line . PHP_EOL, 3, CAFET_DIR . 'error.log');
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
            $_error = explode('-', $error);
            $code = intval($_error[0]) * 1000 + intval($_error[1]);
            throw new CafetAPIException($additional_message, $code, null, $file, $line);
            die();
        }
        
        cafet_log($error);
        
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
            
            return $info;
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
        
        foreach ($cafet_errors as $cafet_error) if (in_array($error, array_keys($cafet_error))) {
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
    
}