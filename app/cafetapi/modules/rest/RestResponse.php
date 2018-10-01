<?php
namespace cafetapi\modules\rest;

/**
 *
 * @author damie
 *        
 */
class RestResponse
{
    private $body;
    private $code;
    private $message;
    private $headers;
    private $removeHeader;
    

    /**
     */
    public function __construct(int $code, string $message, ?array $body, array $hearders = array(), array $removeHeader = array())
    {
        $this->body = $body;
        $this->code = $code;
        $this->message = $message;
        $this->headers = $hearders;
        $this->removeHeader = $removeHeader;
    }
    /**
     * Returns the $body
     * @return array the $body
     */
    public final function getBody() : ?array
    {
        return $this->body;
    }

    /**
     * Returns the $code
     * @return int the $code
     */
    public final function getCode() : int
    {
        return $this->code;
    }

    /**
     * Returns the $message
     * @return string the $message
     */
    public final function getMessage() : string
    {
        return $this->message;
    }

    /**
     * Returns the $headers
     * @return array the $headers
     */
    public final function getHeaders() : array
    {
        return $this->headers;
    }

    /**
     * Returns the $removeHeader
     * @return array the $removeHeader
     */
    public final function getRemoveHeader() : array
    {
        return $this->removeHeader;
    }

}

