<?php
namespace cafetapi\modules\rest\errors;

use cafetapi\modules\rest\HttpCodes;
use cafetapi\modules\rest\RestResponse;

/**
 *
 * @author damie
 *        
 */
class ClientError
{
    
    public static function badRequest(string $reason = '') : RestResponse
    {
        $headers = array('Reason' => $reason);
        return new RestResponse(400, HttpCodes::HTTP_400, cafet_grab_error_infos('02-400', $reason), $headers);
    }
    
    public static function Forbidden() : RestResponse
    {
        return new RestResponse(403, HttpCodes::HTTP_403, cafet_grab_error_infos('02-403'));
    }

    public static function resourceNotFound(string $reason = '') : RestResponse
    {
        $headers = array('Reason' => $reason);
        return new RestResponse(404, HttpCodes::HTTP_404, cafet_grab_error_infos('02-404', $reason), $headers);
    }
    
    public static function methodNotAllowed(string $method, array $allowedMethods) : RestResponse
    {
        $headers = array('Allow' => implode(', ', $allowedMethods));
        return new RestResponse(405, HttpCodes::HTTP_405, cafet_grab_error_infos('02-405', 'Method ' . $method . ' is not allowed for the chosen resource'), $headers);
    }
}

