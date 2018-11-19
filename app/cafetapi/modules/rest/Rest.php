<?php
namespace cafetapi\modules\rest;

use cafetapi\modules\rest\errors\ServerError;
use Error;
use Exception;
use SimpleXMLElement;
use cafetapi\modules\rest\errors\ClientError;
use cafetapi\user\User;

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
    
    const API_VERSION = '2.0.0';
    const DEFAUL_RETURN_TYPE = 'json';
    const CHARSET = 'UTF-8';
    
    private $root_url;
    
    private $version;
    private $path;
    private $body;
    private $method;
    private $contentType;
    private $pretty;
    private $headers;
    
    private $session;
    private $user;

    /**
     */
    public function __construct($root_url)
    {
        if (!isset($_REQUEST[self::VERSION_FIELD]) || !isset($_REQUEST[self::PATH_FIELD]) || !isset($_REQUEST[self::RETURN_TYPE_FIELD]) || !isset($_SERVER['REQUEST_METHOD'])) {
            $this->printResponse(ServerError::internalServerError());
        }
        
        if ($_REQUEST[self::VERSION_FIELD] === '') $this->printResponse(ClientError::badRequest('missing version'));
        if ($_REQUEST[self::PATH_FIELD] === '') $this->printResponse(ClientError::badRequest('missing path'));
        
        
        $this->root_url = $root_url;
        
        $this->version = $_REQUEST[self::VERSION_FIELD];
        $this->path = explode("/", $_REQUEST[self::PATH_FIELD]);
        $this->body = json_decode(file_get_contents('php://input'), true);
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->contentType = $_REQUEST[self::RETURN_TYPE_FIELD] !== '' ? $_REQUEST[self::RETURN_TYPE_FIELD] : self::DEFAUL_RETURN_TYPE;
        $this->pretty = isset($_REQUEST['pretty']);
        
        $this->headers = apache_request_headers();
        if(!$this->headers) $this->headers = array();
        
        if (isset($_COOKIE[cafet_get_configuration('session_name')])) {
            $this->session = cafet_init_session();
        } elseif (isset($this->headers['Session'])) {
            $this->session = $this->headers['Session'];
            cafet_init_session(true, $this->session);
        }
        
        $this->user = cafet_get_logged_user();
        
        try {
            $this->printResponse(RootNode::handle($this));
        } catch (Error | Exception $e) {
            cafet_log($e);
            $this->printResponse(ServerError::internalServerError());
        }
    }
    
    private function registerContentType(string $contentType) {
        header('Content-type: '. $contentType . '; charset=' . self::CHARSET);
    }
    
    /*******************
     ** Rest printers **
     *******************/
    
    private function printResponse(RestResponse $response) {
        header('HTTP/1.1 ' . $response->getCode() . ' ' . $response->getMessage());
        foreach ($response->getRemoveHeader() as $header) header_remove($header);
        foreach($response->getHeaders() as $name => $content) header("$name: $content");
        
        if ($response->getBody() !== null) switch ($this->contentType) {
            case 'xml':
                $this->printXMLResponse($response);
                break;
            case 'yaml':
            case 'yml':
                $this->printYAMLResponse($response);
                break;
            case 'json':
            default:
                $this->printJSONResponse($response);
                break;
        }
        
        exit();
    }
    
    
    private function printXMLResponse(RestResponse $response) {
        $this->registerContentType('application/xml');
        
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
    
    private function printYAMLResponse(RestResponse $response) {
        $this->registerContentType('text/yaml');
        
        require_once INCLUDES_DIR . 'spyc.php';
        echo spyc_dump($response->getBody());
    }
    
    private function printJSONResponse(RestResponse $response) {
        $this->registerContentType('application/json');
        
        if($this->pretty) echo json_encode($response->getBody(), JSON_PRETTY_PRINT);
        else              echo json_encode($response->getBody());
    }
    
    
    
    /***************
     ** Computers **
     ***************/
    
    /**
     * Set wich permission to check, print a 403 error if one of the given permissions is not granted  to the client
     * @param array $permission
     */
    public final function needPermissions(array $permissions)
    {
        if ($this->user) {
            foreach ($permissions as $permission) if (!$this->user->hasPermission($permission)) {
                $this->printResponse(ClientError::forbidden());
            }
        } else foreach ($permissions as $permission) if (!cafet_get_guest_group()->hasPermission($permission)) {
            $api_root = $this->root_url . '/api/v' . $this->version;
            $after = urlencode($api_root . '/' . $_REQUEST[self::PATH_FIELD]) . '.' . $this->contentType;
            
            $this->printResponse(ClientError::forbidden(array(
                'Location' => $api_root . '/user/login?after=' . $after,
                'Cache-Control' => 'no-cache'
            )));
        }
    }
    
    public final function allowMethods(array $methods)
    {
        if(!in_array($this->method, $methods)) $this->printResponse(ClientError::methodNotAllowed($this->method, $methods));
    }
    
    public final function isClientAbleTo(string $permission) : bool
    {
        //TODO permission check
        return true;
    }
    
    public final function shiftPath() : ?string
    {
        return array_shift($this->path);
    }

    /*************
     ** Getters **
     *************/
    
    /**
     * Returns the $root_url
     * @return mixed the $root_url
     */
    public final function getRoot_url()
    {
        return $this->root_url;
    }
    
    /**
     * Returns the $version
     * @return mixed the $version
     */
    public final function getVersion() : string
    {
        return $this->version;
    }

    /**
     * Returns the $path
     * @return array the $path
     */
    public final function getPath() : array
    {
        return $this->path;
    }

    /**
     * Returns the $body
     * @return mixed the $body
     */
    public final function getBody() : ?array
    {
        return $this->body;
    }

    /**
     * Returns the $method
     * @return string the $method
     */
    public final function getMethod() : ?string
    {
        return $this->method;
    }

    /**
     * Returns the $contentType
     * @return string the $contentType
     */
    public final function getContentType() : ?string
    {
        return $this->contentType;
    }

    /**
     * Returns the $pretty
     * @return mixed the $pretty
     */
    public final function getPretty() : bool
    {
        return $this->pretty;
    }

    /**
     * Returns the $headers
     * @return multitype: the $headers
     */
    public final function getHeaders() : array
    {
        return $this->headers;
    }
    /**
     * Returns the $session
     * @return string the $session
     */
    public final function getSession() : ?string
    {
        return $this->session;
    }

    /**
     * Returns the $user
     * @return ?User the $user
     */
    public final function getUser() : ?User
    {
        return $this->user;
    }


}

