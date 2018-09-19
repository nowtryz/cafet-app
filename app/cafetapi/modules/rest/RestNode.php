<?php
namespace cafetapi\modules\rest;

/**
 *
 * @author damie
 *        
 */
interface RestNode
{
    protected static function handle(array $path, array $body, string $method, array $headers) : RestResponse;
}

