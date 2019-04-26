<?php
namespace cafetapi\modules\cafet_app;

use cafetapi\Logger;
use cafetapi\data\JSONParsable;

/**
 *
 * @author Damien
 *        
 */
class ReturnStatement
{

    private $array;

    private $json;

    const NL = "\r\n";

    /**
     */
    public function __construct(string $status, $return)
    {
        if (is_array($return) || is_bool($return)) {
            $this->array = array(
                'status' => $status,
                'result' => $return,
                'computing' => cafet_execution_duration()
            );

            if (! $this->json = json_encode($this->array))
                $this->throw_internal_server_error();

            $this->array = json_decode($this->json);
        } elseif ($return instanceof JSONParsable) {
            $this->json = '{' . ReturnStatement::NL . '"status":"' . $status . '",' . ReturnStatement::NL . '"result": ' . $return->__toString() . ',' . ReturnStatement::NL . '"computing": ' . cafet_execution_duration() . ReturnStatement::NL . '}';

            $this->array = json_decode($this->json);
        } else
            $this->throw_internal_server_error();
    }

    private function throw_internal_server_error()
    {
        Logger::log('An error occured while parsing the result');
        Logger::throwError('01-500', 'An error occured while parsing the result');
    }

    public final function print()
    {
        header('X-Content-Type-Options: nosniff');
        header('Content-Type: application/json; charset=utf-8');
        echo $this->json;
        exit();
    }

    /**
     * Returns the $array
     *
     * @return array the $array
     * @since API 0.1.0 (2018)
     */
    public final function getArray()
    {
        if (! $this->array)
            return array();
        else
            return $this->array;
    }

    /**
     * Returns the $json
     *
     * @return string the $json
     * @since API 0.1.0 (2018)
     */
    public final function getJson()
    {
        return $this->json;
    }
}

