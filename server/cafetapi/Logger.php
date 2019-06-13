<?php
namespace cafetapi;

use cafetapi\ErrorPageBuilder;
use cafetapi\exceptions\CafetAPIException;
use cafetapi\modules\cafet_app\ReturnStatement;
use Throwable;

class Logger
{
    /**
     * Log some text in the API log file
     *
     * @param String $log
     *            the text to log
     * @global $avoid_log
     * @since API 0.1.0 (2018)
     */
    public static function log(string $log)
    {
        $logs = [];
        $tmp = explode("\r\n", $log);
        foreach ($tmp as $tmp2) $logs = array_merge($logs, explode("\n", $tmp2));
        foreach ($logs as $line) error_log('[' . date("d-M-Y H:i:s e") . '] CAFET ' . $line . PHP_EOL, 3, CAFET_DIR . 'error.log');
    }
    
    /**
     * build return statement when an error occured
     *
     * @param String $error
     *            the error code
     * @since API 0.1.0 (2018)
     */
    public static function throwError(string $error, string $additional_message = null, string $file = null, int $line = 0)
    {
        if (!cafet_is_app_request()) {
            $_error = explode('-', $error);
            $code = intval($_error[0]) * 1000 + intval($_error[1]);
            throw new CafetAPIException($additional_message, $code, null, $file, $line);
            exit();
        }
        
        self::log($error . ($additional_message ? ': '. $additional_message : ''));
        
        $result = new ReturnStatement("error", self::grabErrorInfos($error, $additional_message));
        $result->print();
        
        exit();
    }
    
    
    /**
     * @param error
     * @param additional_message
     */
    
    public static function grabErrorInfos($error, $additional_message = null)
    {
        $sub_error = explode('-', $error);
        $errors = Kernel::errorsInfo();
        
        $info = empty($errors) ? [
            'error_code' => '01-500',
            'error_type' => '01 : server exception',
            'error_message' => 'Internal server error',
        ] : [
            'error_code' => $error,
            'error_type' => $errors[$sub_error[0]]['def'],
            'error_message' => $errors[$sub_error[0]][$sub_error[1]],
        ];
        
        if (isset($additional_message)) $info['additional_message'] = $additional_message;
        
        return $info;
    }
    
    /**
     * Thow the cafet error corresponding to the http error
     * @param string|int $error
     */
    public static function logHttpError($error)
    {
        // Avoid logging for random 403 and 404 errors
        if(in_array($error, ['403', '404']) && !isset($_SERVER['HTTP_REFERER'])) return;
        
        $cafet_errors = Kernel::errorsInfo();
        
        foreach ($cafet_errors as $cafet_error) if (in_array($error, array_keys($cafet_error))) {
            Logger::log($error . ' Error: ' . $cafet_error[$error] . ': for "' . $_SERVER['REQUEST_URI'] . '"');
        }
        
        Logger::log('From: ' . $_SERVER['HTTP_REFERER']);
    }
    
    public static function errorHandler($errno, $errmsg, $filename, $linenum, $errcontext)
    {
        static $errortype = [
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
        ];
        
        $msg  = $errortype[$errno] . ': ';
        $msg .= $errmsg . ': ';
        $msg .= 'entry in ' . $filename;
        $msg .= ' on line ' . $linenum;
        
        Logger::throwError('01-500', $msg);
    }
    
    public static function exceptionHandler(Throwable $e)
    {
        $msg  = get_class($e) . ' (' . $e->getCode() . '): ';
        $msg .= $e->getMessage() . ': ';
        $msg .= 'entry in ' . $e->getFile();
        $msg .= ' on line ' . $e->getLine();
        
        Logger::log($msg);
        (new ErrorPageBuilder(500))->print();
    }
}

