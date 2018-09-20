<?php
namespace cafetapi\modules\rest\user;

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
    public function handle(array $path, ?array $body, string $method, array $headers)
    {
        $dir = array_shift($path);
        
        switch ($dir) {
            case self::LOGIN:   return self::login($path, $body, $method, $headers);
            case self::LOGOUT:  return self::logout($path, $body, $method, $headers);
            case self::CURRENT: return self::current($path, $body, $method, $headers);
        }
    }
    
    private static function login(array $path, array $boddy, $method, array $headers) : RestResponse
    {
        
    }
    
    private static function logout(array $path, array $boddy, $method, array $headers) : RestResponse
    {
        
    }
    
    private static function current(array $path, array $boddy, $method, array $headers) : RestResponse
    {
        
    }
}

