<?php
namespace cafetapi\modules\rest;

/**
 *
 * @author damie
 *        
 */
interface RestNode
{
    public static function handle(array $path, ?array $body, string $method, array $headers) : RestResponse;
}

