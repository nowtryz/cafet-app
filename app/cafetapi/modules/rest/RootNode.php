<?php
namespace cafetapi\modules\rest;

use cafetapi\modules\rest\cafet\CafetNode;
use cafetapi\modules\rest\user\UserNode;
use cafetapi\modules\rest\server\ServerNode;
use cafetapi\modules\rest\errors\ClientError;

/**
 *
 * @author damie
 *        
 */
class RootNode implements RestNode
{
    const CAFET  = 'cafet';
    const USER   = 'user';
    const SERVER = 'server';

    /**
     */
    public function __construct()
    {}
    
    public static function handle(array $path, ?array $body, string $method, array $headers): RestResponse
    {
        $dir = array_shift($path);
        
        switch ($dir) {
            case self::CAFET:  return CafetNode::handle($path, $body, $method, $headers);
            case self::USER:   return UserNode::handle($path, $body, $method, $headers);
            case self::SERVER: return ServerNode::handle($path, $body, $method, $headers);
            default:           return ClientError::resourceNotFound('Unknown ' . $dir . ' node');
        }
    }

}

