<?php
namespace cafetapi\exceptions;

require_once ('cafetapi/exceptions/CafetAPIException.php');

/**
 *
 * @author Damien
 *        
 */
class NotEnoughtMoneyException extends CafetAPIException
{

    /**
     *
     * @param $message string
     *            [optional]
     * @param $code int
     *            [optional]
     * @param $previous \Throwable
     *            [optional]
     * @since API 0.1.0 (2018)
     */
    public function __construct($message = null, $code = null, $previous = null, string $file = null, int $line = 0)
    {
        parent::__construct($message, $code, $previous, $file, $line);
    }
}

