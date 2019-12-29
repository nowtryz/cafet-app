<?php
namespace cafetapi\exceptions;

/**
 *
 * @author Damien
 *
 */
class DuplicateEntryException extends CafetAPIException
{

    /**
     *
     * @param $message string
     *            [optional]
     * @param $code int
     *            [optional]
     * @param $previous \Throwable
     *            [optional]
     * @param string|null $file
     * @param int $line
     * @since API 0.1.0 (2018)
     */
    public function __construct($message = null, $code = null, $previous = null, string $file = null, int $line = 0)
    {
        parent::__construct($message, $code, $previous, $file, $line);
    }
}

