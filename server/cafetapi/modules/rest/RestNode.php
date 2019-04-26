<?php
namespace cafetapi\modules\rest;

/**
 *
 * @author damie
 *        
 */
interface RestNode
{
    public static function handle(Rest $request) : RestResponse;
}

