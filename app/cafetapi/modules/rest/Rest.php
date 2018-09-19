<?php
namespace cafetapi\modules\rest;

/**
 *
 * @author damie
 *        
 */
class Rest
{
    const VERSION = 'version';
    //array $path, array $boddy, string $method, array $headers
    private $version;
    private $path;
    private $body;
    private $method;
    private $headers;

    /**
     */
    public function __construct()
    {
        $this->version = $_REQUEST[self::VERSION];
        $this->path = explode("/", substr(@$_SERVER['PATH_INFO'], 1));
        $this->body = json_decode(file_get_contents('php://input'), true);
        $this->method = $_SERVER['REQUEST_METHOD'];
        
        $this->headers = apache_request_headers();
        if(!$this->headers) $this->headers = array();
    }
}

