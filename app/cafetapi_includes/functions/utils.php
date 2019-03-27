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