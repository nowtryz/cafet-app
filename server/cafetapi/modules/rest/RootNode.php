<?php
namespace cafetapi\modules\rest;

use cafetapi\modules\rest\cafet\CafetNode;
use cafetapi\modules\rest\user\UserNode;
use cafetapi\modules\rest\server\ServerNode;
use cafetapi\modules\rest\errors\ClientError;
use cafetapi\modules\rest\stats\StatsNode;
use cafetapi\modules\rest\locales\LocalesNode;

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
    const STATS = 'stats';
    const LOCALES = 'locales';

    /**
     */
    public function __construct()
    {}
    
    public static function handle(Rest $request): RestResponse
    {
        $dir = $request->shiftPath();
        
        switch ($dir) {
            case self::CAFET:   return CafetNode::handle($request);
            case self::USER:    return UserNode::handle($request);
            case self::SERVER:  return ServerNode::handle($request);
            case self::STATS:   return StatsNode::handle($request);
            case self::LOCALES: return LocalesNode::handle($request);
            default:            return ClientError::resourceNotFound('Unknown ' . $dir . ' node');
        }
    }

}

