<?php
namespace cafetapi\modules\rest\cafet;

use cafetapi\io\ReloadManager;
use cafetapi\modules\rest\HttpCodes;
use cafetapi\modules\rest\Rest;
use cafetapi\modules\rest\RestNode;
use cafetapi\modules\rest\RestResponse;
use cafetapi\modules\rest\errors\ClientError;
use cafetapi\modules\rest\errors\ServerError;
use cafetapi\user\Perm;

/**
 *
 * @author damie
 *        
 */
class ReloadsNode implements RestNode
{
    const NEW = 'new';

    /**
     * (non-PHPdoc)
     *
     * @see \cafetapi\modules\rest\RestNode::handle()
     */
    public static function handle(Rest $request) : RestResponse
    {
        $dir = $request->shiftPath();
        
        switch ($dir) {
            case self::NEW:  return self::new($request);
            
            case null: return self::list($request);
            default:
                if(intval($dir)) return self::reload($request, intval($dir));
                else return ClientError::resourceNotFound('Unknown cafet/reload/' . $dir . ' node');
        }
    }
    
    private static function list(Rest $request) : RestResponse
    {
        $request->allowMethods('GET');
        $request->needPermissions(Perm::CAFET_ADMIN_GET_RELOADS);
        
        $reloads = array();
        foreach (ReloadManager::getInstance()->getReloads() as $reload) $reloads[] = $reload->getProperties();
        return new RestResponse('200', HttpCodes::HTTP_200, $reloads);
    }
    
    private static function new(Rest $request) : RestResponse
    {
        $request->allowMethods('POST');
        $request->needPermissions(Perm::CAFET_ADMIN_RELOAD);
        
        //body checks
        if(!$request->getBody())                         return ClientError::badRequest('Empty body');
        if(!isset($request->getBody()['client_id']))     return ClientError::badRequest('Missing `client_id` field');
        if(!isset($request->getBody()['amount']))        return ClientError::badRequest('Missing `amount` field');
        if(!intval($request->getBody()['client_id'], 0)) return ClientError::badRequest('Expected `client_id` field to be an integer');
        if(!is_scalar($request->getBody()['amount']))    return ClientError::badRequest('Expected `amount` field to be a float');
        
        $client_id = intval($request->getBody()['client_id'], 0);
        $amount = intval($request->getBody()['amount']);
        
        $reload = ReloadManager::getInstance()->saveReload($client_id, $amount, 'by a registered capable user');
        if($reload) return new RestResponse('204', HttpCodes::HTTP_204, null);
        else return ServerError::internalServerError();
    }
    
    private static function reload(Rest $request, int $id) : RestResponse
    {
        $request->allowMethods('GET');
        
        $reload = ReloadManager::getInstance()->getReload($id);
        
        if($request->getUser() && $request->getUser()->getId() == $reload->getClient())
        {
            $request->needPermissions(Perm::CAFET_ME_RELOADS);
        }
        else $request->needPermissions(Perm::CAFET_ADMIN_GET_RELOADS);
        
        if($reload) return new RestResponse('200', HttpCodes::HTTP_200, $reload->getProperties());
        else return ClientError::resourceNotFound('Unknown reload with id ' . $id);
    }
}

