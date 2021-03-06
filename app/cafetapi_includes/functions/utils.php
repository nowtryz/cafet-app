<?php

use cafetapi\data\Calendar;

/**
 * Function file for classic functions
 * @package essaim_cafet
 * @since API 0.1.0 (2018)
 */

/**
 * Capitalize the first letter of the string
 *
 * @param string $source
 *            the string to format
 * @return string the formated string
 * @since API 0.1.0 (2018)
 */
function capitalize_first_letter(string $source): string
{
    if ($source === null)
        return null;
    if (strlen($source) <= 1)
        return strtoupper($source);
    return strtoupper(substr($source, 0, 1)) . strtolower(substr($source, 1));
}

/**
 * Checks wether the given array is an associative array or not
 *
 * @param array $array
 *            the array to check
 * @return bool the result of the test
 * @since API 0.1.0 (2018)
 */
function is_associative_array(array $array): bool
{
    if (!$array) return false;
    return count(array_filter(array_keys($array), 'is_string')) > 0;
}

/**
 * Guess the image format of a base64 encoded image
 *
 * @param string $base64 the encoded image
 * @return string the mime
 * @since API 0.1.0 (2018)
 */
function guess_image_mime(string $base64): string
{
    switch (substr($base64, 0, 1)) {
        case '/': return 'image/jpeg';
        case 'R': return 'image/gif';
        case 'i': return 'image/png';
        default:  return 'application/*';
    }
}

/**
 * Guess the image format of a base64 encoded image
 * @param string $base64 the encoded image
 * @return string the extension of the image
 */
function get_base64_image_format(string $base64): string
{
    switch (substr($base64, 0, 1)) {
        case '/': return '.jpeg';
        case 'R': return '.gif';
        case 'i': return '.png';
        default:  return '';
    }
}

/**
 * Convert an array to its XML equivalent
 * <br>From https://www.codexworld.com/convert-array-to-xml-in-php/
 * @param array $data
 * @param SimpleXMLElement $xml
 */
function array_to_xml(array $data, SimpleXMLElement &$xml ) {
    foreach( $data as $key => $value ) {
        if ( is_numeric($key) ){
            if(isset($value['type'])) $key = strtolower($value['type']);
            else $key = 'item'.$key; //dealing with <0/>..<n/> issues
        } elseif ($key == '*'){
            $key = 'all';
        }
        
        if ( is_array($value) ) {
            $subnode = $xml->addChild($key);
            array_to_xml($value, $subnode);
        } elseif (is_bool($value)) {
            $xml->addChild("$key",var_export($value, true));
        } else {
            $xml->addChild("$key",htmlspecialchars("$value"));
        }
    }
}

/**
 * Compare versions with semantic versioning 2.0.0
 * @param string $version1 the version that would be superior
 * @param string $version2 base version
 * @return mixed true if $version1 > $version2, false if $version1 < $version2 or nul if equal
 */
function is_version_superior_to(string $version1, string $version2, bool $compare_pre_versions = true) {
    $temp_version1 = explode('-', array_shift(explode('+', $version1)));
    $temp_version2 = explode('-', array_shift(explode('+', $version2)));
    
    $release1 = explode('.', $temp_version1[0]);
    $release2 = explode('.', $temp_version2[0]);
    
    $pre_release1 = explode('.', @$temp_version1[1]);
    $pre_release2 = explode('.', @$temp_version2[1]);
    
    unset($temp_version1);
    unset($temp_version2);
    
    if($release1[0] > $release2[0]) return true;
    if($release1[0] < $release2[0]) return false;
    
    if(!isset($release1[1]) || !isset($release2[1])) return null;
    if($release1[1] > $release2[1]) return true;
    if($release1[1] < $release2[1]) return false;
    
    if(!isset($release1[2]) || !isset($release2[2])) return null;
    if($release1[2] > $release2[2]) return true;
    if($release1[2] < $release2[2]) return false;
    
    if(!$compare_pre_versions) return null;
    if(!$pre_release1 || !$pre_release2) return null;
    
    $max_comp = min(count($pre_release1), count($pre_release2));
    for($i = 0; $i < $max_comp; $i++) {
        if(strcmp($pre_release1, $pre_release2) > 0) return true;
        if(strcmp($pre_release1, $pre_release2) < 0) return false;
    }
    
    return null;
}

/**
 * Return classname of the object without the namespace
 * @param object $object
 * @return string the simple name of the object class
 */
function get_simple_classname(object $object) : string
{
    return substr(get_class($object), strrpos(get_class($object), '\\') + 1);
}

/**
 * Get a Calendar object from an SQL DateTime format
 * @param string $datetime the output of the database
 * @return Calendar the calendar representing the datetime
 */
function get_calendar_from_datetime(string $datetime) : Calendar
{
    $_datetime = new DateTime($datetime);
    
    return new Calendar(
        intval($_datetime->format('Y')),
        intval($_datetime->format('m')),
        intval($_datetime->format('d')),
        intval($_datetime->format('H')),
        intval($_datetime->format('i')),
        intval($_datetime->format('s'))
   );
}

/**
 * Get information about storage
 * @param int $used return used storage
 * @param int $total return total storage
 */
function storage_info(&$used, &$total)
{
    $_total = disk_total_space(CAFET_DIR) / (1024**3);
    $_free = disk_free_space(CAFET_DIR) / (1024**3);
    $_used = $_total - $_free;
    
    $used = round($_used, 2);
    $total = round($_total, 2);
}
