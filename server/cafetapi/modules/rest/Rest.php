<?php
namespace cafetapi\modules\rest;

use cafetapi\modules\rest\errors\ClientError;
use cafetapi\modules\rest\errors\ServerError;
use cafetapi\user\User;
use SimpleXMLElement;
use cafetapi\config\Config;
use cafetapi\Logger;

/**
 *
 * @author damie
 *
 */
class Rest
{
    //types
    const PARAM_INT = 1;
    const PARAM_SCALAR = 2;
    const PARAM_STR = 3;
    const PARAM_BOOL = 4;
    const PARAM_ARRAY = 5;
    const PARAM_ANY = 6;

    //conflicts
    const CONFLICT_DUPLICATED = 'duplicated';
    const CONFLICT_NOT_VALID = 'not valid';
    const CONFLICT_DIFFERENT = 'different';

    //headers
    const SKIP_HEADERS = 'Skip-Headers';

    //api
    private const VERSION_FIELD = 'version';
    private const PATH_FIELD = 'path';
    private const RETURN_TYPE_FIELD = 'return_type';

    private const API_VERSION = '2.0.0';
    private const DEFAUL_RETURN_TYPE = 'json';
    private const CHARSET = 'UTF-8';

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

        if ($this->method == 'HEAD') $this->method = 'GET';
        if (!$this->headers) $this->headers = array();

        if (isset($_COOKIE[Config::session_name])) {
            $this->session = cafet_init_session();
        } elseif (isset($this->headers['Session'])) {
            $this->session = $this->headers['Session'];
            cafet_init_session(true, $this->session);
        }

        $this->user = cafet_get_logged_user();
    }

    private function registerContentType(string $contentType) {
        header('Content-type: '. $contentType . '; charset=' . self::CHARSET);
    }

    /*******************
     ** Rest printers **
     *******************/

    public function printResponse(RestResponse $response) {
        // prepare Runtime header
        ob_start(function (string $buff) {
            if (!headers_sent()) {
                header('Runtime: ' . cafet_execution_duration());
            }
            return $buff;
        });
        header('HTTP/1.1 ' . $response->getCode() . ' ' . $response->getMessage());
        header('Cache-Control: max-age=0, private, must-revalidate', true);
        header_remove('Expires');
        foreach ($response->getRemoveHeader() as $header) header_remove($header);
        foreach ($response->getHeaders() as $name => $content) header("$name: $content");

        if (isset($this->headers[self::SKIP_HEADERS])) {
            $matches = [];
            preg_match_all('/"([^"]*)"/', trim($this->headers[self::SKIP_HEADERS]), $matches);

            if (isset($matches[1])) foreach ($matches[1] as $header_to_skip) {
                header_remove($header_to_skip);
            }
        }

        if ($response->getBody() !== null) {
            switch ($this->contentType) {
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
        }

        if ($this->session && $this->user) cafet_set_logged_user($this->user);

        exit();
    }


    private function printXMLResponse(RestResponse $response) {
        $this->registerContentType('application/xml');

        $xml = new SimpleXMLElement('<data/>');
        array_to_xml($response->getBody(), $xml);

        if($this->pretty) {
            $dom = dom_import_simplexml($xml)->ownerDocument;
            $dom->formatOutput = true;
            $this->send($dom->saveXML());
        } else {
            $this->send($xml->asXML());
        }
    }

    private function printYAMLResponse(RestResponse $response) {
        $this->registerContentType('text/yaml');

        require_once INCLUDES_DIR . 'spyc.php';
        $this->send(spyc_dump($response->getBody()));
    }

    private function printJSONResponse(RestResponse $response) {
        $this->registerContentType('application/json');

        if($this->pretty) $this->send(json_encode($response->getBody(), JSON_PRETTY_PRINT));
        else              $this->send(json_encode($response->getBody()));
    }

    private function send(string $content) {
        echo $content;
    }



    /***************
     ** Computers **
     ***************/

    /**
     * Set wich permissions to check, print a 403 error if one of the given permissions is not granted to the client
     * @param array $permission
     */
    public final function needPermissions(string... $permissions)
    {
        if ($this->user) {
            foreach ($permissions as $permission) if (!$this->user->hasPermission($permission)) {
                $this->printResponse(ClientError::forbidden());
            }
        } else foreach ($permissions as $permission) if (!cafet_get_guest_group()->hasPermission($permission)) {
            $this->needLogin();
        }
    }

    public final function needLogin()
    {
        if (!$this->user)
        {
            $api_root = $this->root_url . '/api/v' . $this->version;
            $after = urlencode($_SERVER['REQUEST_URI']);

            $this->printResponse(ClientError::forbidden(array(
                'Location' => $api_root . '/user/login?after=' . $after . ($this->pretty ? '&pretty' : ''),
                'Cache-Control' => 'no-cache'
            )));
        }
    }

    public final function allowMethods(string... $methods)
    {
        if(!in_array($this->method, $methods)) $this->printResponse(ClientError::methodNotAllowed($this->method, $methods));
    }

    public final function shiftPath() : ?string
    {
        return array_shift($this->path);
    }

    public final function checkBody(array $fields) {
        if (!$this->getBody()) $this->printResponse(ClientError::badRequest('Empty body'));

        $missing = array();
        foreach ($fields as $key => $value) if (!isset($this->body[$key])) $missing[] = $key;
        if ($missing) $this->printResponse(ClientError::badRequest('Missing fields', array(
            "missing" => $missing
        )));
        unset($missing);

        $wrong_types = array();
        foreach ($fields as $key => $value) switch ($value) {
            case self::PARAM_INT:
                if (!intval($this->body[$key], 0)) $wrong_types[$key] = 'integer';
                break;

            case self::PARAM_SCALAR:
                if (!is_scalar($this->body[$key])) $wrong_types[$key] = 'float';
                break;

            case self::PARAM_BOOL:
                if (!is_bool($this->body[$key])) $wrong_types[$key] = 'boolean';
                break;

            case self::PARAM_ARRAY:
                if (!is_array($this->body[$key])) $wrong_types[$key] = 'array';
                break;
        }
        if ($wrong_types) $this->printResponse(ClientError::badRequest('Wrong types', [
            "type_expectation" => $wrong_types
        ]));
        unset($wrong_types);
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

