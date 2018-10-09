<?php
namespace cafetapi\modules\rest\cafet;

use cafetapi\io\DataFetcher;
use cafetapi\modules\rest\HttpCodes;
use cafetapi\modules\rest\Rest;
use cafetapi\modules\rest\RestNode;
use cafetapi\modules\rest\RestResponse;
use cafetapi\modules\rest\errors\ClientError;
use cafetapi\user\Perm;
use cafetapi\modules\rest\errors\ServerError;
use cafetapi\io\DataUpdater;

/**
 *
 * @author damie
 *        
 */
class ReloadNode implements RestNode
{
    const LIST = 'list';
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
            case self::LIST: return self::list($request);
            case self::NEW:  return self::new($request);
            
            case null: return ClientError::forbidden();
            default:
                if(intval($dir)) return self::reload($request, intval($dir));
                else return ClientError::resourceNotFound('Unknown cafet/reload/' . $dir . ' node');
        }
    }
    
    private static function list(Rest $request) : RestResponse
    {
        if($request->getMethod() !== 'GET') return ClientError::methodNotAllowed($request->getMethod(), array('GET'));
        if(!$request->isClientAbleTo(Perm::CAFET_ADMIN_GET_RELOADS)) return ClientError::forbidden();
        
        $reloads = array();
        foreach (DataFetcher::getInstance()->getReloads() as $reload) $reloads[] = $reload->getProperties();
        return new RestResponse('200', HttpCodes::HTTP_200, $reloads);
    }
    
    private static function new(Rest $request) : RestResponse
    {
        if($request->getMethod() !== 'POST') return ClientError::methodNotAllowed($request->getMethod(), array('POST'));
        if(!$request->isClientAbleTo(Perm::CAFET_ADMIN_RELOAD)) return ClientError::forbidden();
        
        //body checks
        if(!$request->getBody())                         return ClientError::badRequest('Empty body');
        if(!isset($request->getBody()['client_id']))     return ClientError::badRequest('Missing `client_id` field');
        if(!isset($request->getBody()['amount']))        return ClientError::badRequest('Missing `amount` field');
        if(!intval($request->getBody()['client_id'], 0)) return ClientError::badRequest('Expected `client_id` field to be an integer');
        if(!is_scalar($request->getBody()['amount']))    return ClientError::badRequest('Expected `amount` field to be a float');
        
        $client_id = intval($request->getBody()['client_id'], 0);
        $amount = intval($request->getBody()['amount']);
        
        $reload = DataUpdater::getInstance()->saveReload($client_id, $amount, 'by a registered capable user');
        if($reload) return new RestResponse('204', HttpCodes::HTTP_204, null);
        else return ServerError::internalServerError();
    }
    
    private static function reload(Rest $request, int $id) : RestResponse
    {
        if($request->getMethod() !== 'GET') return ClientError::methodNotAllowed($request->getMethod(), array('GET'));
        if(!$request->isClientAbleTo(Perm::CAFET_ADMIN_GET_RELOADS)) return ClientError::forbidden();
        
        $reload = DataFetcher::getInstance()->getReload($id);
        if($reload) return new RestResponse('200', HttpCodes::HTTP_200, $reload->getProperties());
        else return ClientError::resourceNotFound('Unknown reload with id ' . $id);
    }
}

