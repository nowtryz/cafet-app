<?php
namespace cafetapi\exceptions;

/**
 *
 * @author Damien
 *        
 */
class CafetAPIException extends \Exception
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
    public function __construct($message = null, $code = null, $previous = null, string $file = null, int $line = 0)
    {
        parent::__construct($message, $code, $previous);
        if ($file)
            $this->file = $file;
        if ($line)
            $this->line = $line;
    }
}

