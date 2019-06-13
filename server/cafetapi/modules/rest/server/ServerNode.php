<?php
namespace cafetapi\modules\rest\server;

use cafetapi\modules\rest\Rest;
use cafetapi\modules\rest\RestNode;
use cafetapi\modules\rest\RestResponse;
use cafetapi\modules\rest\errors\ClientError;
use cafetapi\user\Perm;
use cafetapi\modules\rest\HttpCodes;
use cafetapi\config\Config;

/**
 *
 * @author damie
 *        
 */
class ServerNode implements RestNode
{
    const USERS = 'users';
    const PERMISSIONS_MAP ='permissions.map';
    const INFORMATION = 'information';

    /**
     * (non-PHPdoc)
     *
     * @see \cafetapi\modules\rest\RestNode::handle()
     */
    public static function handle(Rest $request) : RestResponse
    {
        $dir = $request->shiftPath();
        
        
        switch ($dir) {
            case self::USERS:           return UsersNode::handle($request);
            case self::INFORMATION:     return self::information($request);
            case self::PERMISSIONS_MAP: return self::permissions_map($request);
            
            case null: return ClientError::forbidden();
            default:   return ClientError::resourceNotFound('Unknown server/' . $dir . ' node');
        }
    }

    private static function permissions_map($request) : RestResponse
    {
        $reflector = new \ReflectionClass(Perm::class);
        return new RestResponse(200, HttpCodes::HTTP_200, $reflector->getConstants());
    }
    
    private static function information($request) : RestResponse
    {
        return new RestResponse(200, HttpCodes::HTTP_200, [
            'debug' => Config::debug,
            'production' => Config::production,
            'organisation' => Config::organisation,
            'lang' => Config::lang,
            'session_name' => Config::session_name
        ]);
    }
}

