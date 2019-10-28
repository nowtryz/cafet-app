<?php
namespace cafetapi\modules\rest\cafet;

use cafetapi\io\ClientManager;
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
            case null: return self::index($request);
            default:
                if(intval($dir)) return self::reload($request, intval($dir));
                else return ClientError::resourceNotFound('Unknown cafet/reload/' . $dir . ' node');
        }
    }
    
    private static function index(Rest $request) : RestResponse
    {
        $request->allowMethods('GET','POST');
        
        switch ($request->getMethod()) {
            case 'GET': return self::list($request);
            case 'POST': return self::new($request);
        }
    }
    
    private static function list(Rest $request) : RestResponse
    {
        $request->needPermissions(Perm::CAFET_ADMIN_GET_RELOADS);
        
        $reloads = [];
        foreach (ReloadManager::getInstance()->getReloads() as $reload) $reloads[] = $reload->getProperties();
        return new RestResponse('200', HttpCodes::HTTP_200, $reloads);
    }
    
    private static function new(Rest $request) : RestResponse
    {
        $request->needPermissions(Perm::CAFET_ADMIN_RELOAD);
        
        //body checks
        $request->checkBody([
            'client_id' => Rest::PARAM_INT,
            'amount' => Rest::PARAM_SCALAR
        ]);
        
        $client_id = intval($request->getBody()['client_id'], 0);
        $amount = floatval($request->getBody()['amount']);
        
        if (!ClientManager::getInstance()->getClient($client_id)) return ClientError::conflict('Unknown client with id ' . $client_id, [
            'on' => 'client_id', 
            'problem' => 'not found'
        ]);

        if ($amount < 0) $request->needPermissions(Perm::CAFET_ADMIN_NEGATIVERELOAD);
        
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

