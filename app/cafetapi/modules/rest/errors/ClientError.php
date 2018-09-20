<?php
namespace cafetapi\modules\rest\errors;

use cafetapi\modules\rest\RestResponse;

/**
 *
 * @author damie
 *        
 */
class ClientError extends Error
{

    public static function resourceNotFound(string $reason = '') : RestResponse
    {
        $headers = array(
            'Reason' => $reason
        );
        
        return new RestResponse(404, self::HTTP_404, cafet_grab_error_infos('02-404', $reason), $headers);
    }
    
    public static function badRequest(string $reason = '') : RestResponse
    {
        $headers = array(
            'Reason' => $reason
        );
        
        return new RestResponse(400, self::HTTP_400, cafet_grab_error_infos('02-400', $reason), $headers);
    }
}

