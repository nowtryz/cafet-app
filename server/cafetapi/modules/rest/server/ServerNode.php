<?php
namespace cafetapi\modules\rest\server;

use cafetapi\modules\rest\Rest;
use cafetapi\modules\rest\RestNode;
use cafetapi\modules\rest\RestResponse;
use cafetapi\modules\rest\errors\ClientError;

/**
 *
 * @author damie
 *        
 */
class ServerNode implements RestNode
{
    const USERS = 'users';

    /**
     * (non-PHPdoc)
     *
     * @see \cafetapi\modules\rest\RestNode::handle()
     */
    public static function handle(Rest $request) : RestResponse
    {
        $dir = $request->shiftPath();
        
        
        switch ($dir) {
            case self::USERS:   return UsersNode::handle($request);
            
            case null: return ClientError::forbidden();
            default:   return ClientError::resourceNotFound('Unknown server/' . $dir . ' node');
        }
    }
}

