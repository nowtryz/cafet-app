<?php
namespace cafetapi\modules\rest\errors;

use cafetapi\Logger;
use cafetapi\modules\rest\HttpCodes;
use cafetapi\modules\rest\RestResponse;

/**
 *
 * @author damie
 *        
 */
class ServerError
{
    
    public static function internalServerError() : RestResponse
    {
        return new RestResponse(500, HttpCodes::HTTP_500, Logger::grabErrorInfos('01-500'));
    }
}

