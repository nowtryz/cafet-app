<?php
namespace cafetapi\exceptions;

/**
 *
 * @author Damien
 *        
 */
class TooManyArgumentsException extends CafetAPIException
{

    /**
     *
     * @param $message string
     *            [optional]
     * @param $code int
     *            [optional]
     * @param $previous \Throwable
     *            [optional]
     * @since API 1.0.0 (2018)
     */
    public function __construct($message = null, $code = null, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

