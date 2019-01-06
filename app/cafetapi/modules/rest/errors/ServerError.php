<?php
namespace cafetapi\modules\rest\errors;

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
        return new RestResponse(500, HttpCodes::HTTP_500, cafet_grab_error_infos('01-500'));
    }
}

