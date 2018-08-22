<?php
namespace cafetapi\data;

/**
 * An object that can be parse into a JSON string
 *
 * @author Damien
 * @since API 1.0.0 (2018)
 */
abstract class JSONParsable
{

    /**
     * Parse all vars in JSON
     *
     * @param array $vars
     *            called with get_object_vars( $this )
     * @return string the JSON
     * @since API 1.0.0 (2018)
     */
    protected final function parse_JSON(array $vars): string
    {
        $type_path = explode('\\', get_class($this));
        $type = $type_path[count($type_path) - 1];

        $string = '{' . "\n";
        $string .= '"type":' . '"' . $type . '",' . "\n";
        $string .= $this->parse_associative_array($vars);
        $string .= '}' . "\n";

        return $string;
    }

    /**
     * Parse an associative array into a JSON string
     *
     * @param array $array
     *            the array to parse
     * @return string the JSON string
     * @since API 1.0.0 (2018)
     */
    private function parse_associative_array(array $array): string
    {
        $i = 1;
        $length = count($array);
        $string = '';

        foreach ($array as $key => $value) {
            $string .= '"' . $key . '":';
            $string .= $this->parse_value($value);
            $string .= ($i < $length ? ',' : '') . "\n";
            $i ++;
        }

        return $string;
    }

    /**
     * Parse a classic array into a JSON string
     *
     * @param array $array
     *            the array to parse
     * @return string the JSON string
     * @since API 1.0.0 (2018)
     */
    private function parse_array(array $array): string
    {
        $i = 1;
        $length = count($array);
        $string = '[' . "\n";

        foreach ($array as $value) {
            $string .= $this->parse_value($value);
            $string .= ($i < $length ? ',' : '') . "\n";
            $i ++;
        }

        $string .= ']' . "\n";

        return $string;
    }

    /**
     * Parse a typed value into the corresponding JSON string
     *
     * @param Mixed $value
     *            the value to parse
     * @return string the JSON string
     * @since API 1.0.0 (2018)
     */
    private function parse_value($value): string
    {
        $string = '';
        switch (gettype($value)) {
            case "boolean":
                $string .= $value ? 'true' : 'false';
                break;
            case "integer":
            case "double":
            case "object":
                $string .= $value;
                break;
            case "string":
                $string .= '"' . $value . '"';
                break;
            case "NULL":
                $string .= 'null';
                break;
            case "array":
                if (is_associative_array($value)) {
                    $string .= '{' . "\n";
                    $string .= $this->parse_associative_array($value);
                    $string .= '}' . "\n";
                } else {
                    $string .= $this->parse_array($value);
                }
                break;
        }

        return $string;
    }

    /**
     * Call $this->parseJSON(get_object_vars( $this )) to parse this object
     *
     * @return string the JSON encoded string of the given object
     * @since API 1.0.0 (2018)
     */
    abstract public function __toString(): string;
}

