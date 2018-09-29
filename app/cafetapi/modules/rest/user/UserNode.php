<?php
namespace cafetapi\modules\rest\user;

use cafetapi\modules\rest\Rest;
use cafetapi\modules\rest\RestNode;
use cafetapi\modules\rest\RestResponse;

/**
 *
 * @author damie
 *        
 */
class UserNode implements RestNode
{
    const LOGIN   = 'login';
    const LOGOUT  = 'logout';
    const CURRENT = 'current';

    /**
     * (non-PHPdoc)
     *
     * @see \cafetapi\modules\rest\RestNode::handle()
     */
    public function handle(Rest $request)
    {
        $dir = $request->shiftPath();
        
        
        switch ($dir) {
            case self::LOGIN:   return self::login($request);
            case self::LOGOUT:  return self::logout($request);
            case self::CURRENT: return self::current($request);
        }
    }
    
    private static function login(Rest $request) : RestResponse
    {
        
    }
    
    private static function logout(Rest $request) : RestResponse
    {
        
    }
    
    private static function current(Rest $request) : RestResponse
    {
        
    }
}

