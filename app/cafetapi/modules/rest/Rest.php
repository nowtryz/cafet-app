<?php
namespace cafetapi\modules\rest;

use cafetapi\modules\rest\errors\ServerError;
use Exception;
use SimpleXMLElement;
use cafetapi\modules\rest\errors\ClientError;

/**
 *
 * @author damie
 *        
 */
class Rest
{
    const VERSION_FIELD = 'version';
    const PATH_FIELD = 'path';
    const RETURN_TYPE_FIELD = 'return_type';
    const DEFAUL_RETURN_TYPE = 'json';
    //array $path, array $boddy, string $method, array $headers
    private $version;
    private $path;
    private $body;
    private $method;
    private $contentType;
    private $pretty;
    private $headers;

    /**
     */
    public function __construct()
    {
        if (!isset($_REQUEST[self::VERSION_FIELD]) || !isset($_REQUEST[self::PATH_FIELD]) || !isset($_REQUEST[self::RETURN_TYPE_FIELD]) || !isset($_SERVER['REQUEST_METHOD'])) {
            $this->printResponse(ServerError::internalServerError());
        }
        
        if ($_REQUEST[self::VERSION_FIELD] === '') $this->printResponse(ClientError::badRequest('missing version'));
        if ($_REQUEST[self::PATH_FIELD] === '') $this->printResponse(ClientError::badRequest('missing path'));
        
        
        $this->version = $_REQUEST[self::VERSION_FIELD];
        $this->path = explode("/", $_REQUEST[self::PATH_FIELD]);
        $this->body = json_decode(file_get_contents('php://input'), true);
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->contentType = $_REQUEST[self::RETURN_TYPE_FIELD] !== '' ? $_REQUEST[self::RETURN_TYPE_FIELD] : self::DEFAUL_RETURN_TYPE;
        $this->pretty = isset($_REQUEST['pretty']);
        
        $this->headers = apache_request_headers();
        if(!$this->headers) $this->headers = array();
        
        try {
            $this->printResponse(RootNode::handle($this->path, $this->body, $this->method, $this->headers));
        } catch (Exception $e) {
            $this->printResponse(ServerError::internalServerError());
        }
    }
    
    private function printResponse(RestResponse $response) {
        header('HTTP/1.1 ' . $response->getCode() . ' ' . $response->getMessage());
        foreach ($response->getRemoveHeader() as $header) header_remove($header);
        foreach($response->getHeaders() as $name => $content) header("$name: $content");
        
        switch ($this->contentType) {
            case 'xml':
                $this->printXMLResponse($response);
                break;
            case 'json':
            default:
                $this->printJSONResponse($response);
                break;
        }
        
        exit();
    }
    
    
    private function printXMLResponse(RestResponse $response) {
        header('Content-type: application/xml; charset=UTF-8');
        
        $xml = new SimpleXMLElement('<data/>');
        array_to_xml($response->getBody(), $xml);
        
        if($this->pretty) {
            $dom = dom_import_simplexml($xml)->ownerDocument;
            $dom->formatOutput = true;
            echo $dom->saveXML();
        } else {
            echo $xml->asXML();
        }
    }
    
    private function printJSONResponse(RestResponse $response) {
        header('Content-Type: application/json; charset=UTF-8');
        
        if($this->pretty) echo json_encode($response->getBody(), JSON_PRETTY_PRINT);
        else              echo json_encode($response->getBody());
    }
}

