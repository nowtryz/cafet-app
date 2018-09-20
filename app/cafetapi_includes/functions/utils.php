<?php

/**
 * Function file for classic functions
 * @package essaim_cafet
 * @since API 1.0.0 (2018)
 */

/**
 * Capitalize the first letter of the string
 *
 * @param string $source
 *            the string to format
 * @return string the formated string
 * @since API 1.0.0 (2018)
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
 * @since API 1.0.0 (2018)
 */
function is_associative_array(array $array): bool
{
    if (array() === $array)
        return false;
    return count(array_filter(array_keys($array), 'is_string')) > 0;
}

/**
 * Guess the image format of a base64 encoded image
 *
 * @param string $base64
 *            the image encoded
 * @return string the mime
 * @since API 1.0.0 (2018)
 */
function guess_image_mime(string $base64): string
{
    if (substr($base64, 0, 1) == '/')
        return 'image/jpeg';
    else if (substr($base64, 0, 1) == 'R')
        return 'image/gif';
    else if (substr($base64, 0, 1) == 'i')
        return 'image/png';
    else
        return 'application/*';
}

function get_base64_image_format(string $base64): string
{
    if (substr($base64, 0, 1) == '/')
        return '.jpeg';
    else if (substr($base64, 0, 1) == 'R')
        return '.gif';
    else if (substr($base64, 0, 1) == 'i')
        return '.png';
    else
        return '';
}

/**
 * Convert an array to its XML equivalent
 * <br>From https://www.codexworld.com/convert-array-to-xml-in-php/
 * @param array $data
 * @param SimpleXMLElement $xml
 */
function array_to_xml(array $data, SimpleXMLElement &$xml ) {
    foreach( $data as $key => $value ) {
        if( is_numeric($key) ){
            $key = 'item'.$key; //dealing with <0/>..<n/> issues
        }
        if( is_array($value) ) {
            $subnode = $xml->addChild($key);
            array_to_xml($value, $subnode);
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
