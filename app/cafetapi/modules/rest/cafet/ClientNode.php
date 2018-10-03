<?php
namespace cafetapi\modules\rest\cafet;

use cafetapi\modules\rest\HttpCodes;
use cafetapi\modules\rest\RestNode;
use cafetapi\modules\rest\Rest;
use cafetapi\modules\rest\RestResponse;
use cafetapi\modules\rest\errors\ClientError;
use cafetapi\user\Perm;
use cafetapi\io\DataFetcher;

/**
 *
 * @author damie
 *        
 */
class ClientNode implements RestNode
{
    const LIST = 'list';
    const SEARCH = 'search';
    
    const RELOADS = 'reloads';
    const EXPENSES = 'expenses';
    const LAST_EXPENSES = 'last_expenses';
    
    

    /**
     * (non-PHPdoc)
     *
     * @see \cafetapi\modules\rest\RestNode::handle()
     */
    public static function handle(Rest $request) : RestResponse
    {
        $dir = $request->shiftPath();
        
        switch ($dir) {
            case self::LIST:   return self::list($request);
            case self::SEARCH: return self::search($request);
            
            case null: return ClientError::Forbidden();
            default:
                if(intval($dir)) {
                    if(!count($request->getPath())) return self::client($request, intval($dir));
                    else {
                        $subdir = $request->shiftPath();
                        switch ($subdir) {
                            case self::RELOADS:       return self::clientReloads($request, intval($dir));
                            case self::EXPENSES:      return self::clientExpenses($request, intval($dir));
                            case self::LAST_EXPENSES: return self::clientLastExpenses($request, intval($dir));
                            
                            default: return ClientError::resourceNotFound('Unknown ' . $subdir . ' node for a client');
                        }
                    }
                }
                
                else return ClientError::resourceNotFound('Unknown cafet/client/' . $dir . ' node');
        }
    }
    
    private static function list(Rest $request) : RestResponse
    {
        if($request->getMethod() !== 'GET') return ClientError::methodNotAllowed($request->getMethod(), array('GET'));
        if(!$request->isClientAbleTo(Perm::CAFET_ADMIN_GET_CLIENTS)) return ClientError::Forbidden();
        
        $clients = array();
        foreach (DataFetcher::getInstance()->getClients() as $client) $clients[] = $client->getProperties();
        return new RestResponse('200', HttpCodes::HTTP_200, $clients);
    }
    
    private static function search(Rest $request) : RestResponse
    {
        if($request->getMethod() !== 'GET') return ClientError::methodNotAllowed($request->getMethod(), array('GET'));
        if(!$request->isClientAbleTo(Perm::CAFET_ADMIN_GET_CLIENTS)) return ClientError::Forbidden();
        
        $clients = array();
        foreach (DataFetcher::getInstance()->searchClient(urldecode($request->shiftPath())) as $client) $clients[] = $client->getProperties();
        return new RestResponse('200', HttpCodes::HTTP_200, $clients);
        
        
    }
    
    private static function client(Rest $request, int $id) : RestResponse
    {
        if($request->getMethod() !== 'GET') return ClientError::methodNotAllowed($request->getMethod(), array('GET'));
        if(!$request->isClientAbleTo(Perm::CAFET_ADMIN_GET_CLIENTS)) return ClientError::Forbidden();
        
        $client = DataFetcher::getInstance()->getClient($id);
        if($client) return new RestResponse('200', HttpCodes::HTTP_200, $client->getProperties());
        else return ClientError::resourceNotFound('Unknown client with id ' . $id);
    }
    
    private static function clientReloads(Rest $request, int $id) : RestResponse
    {
        if($request->getMethod() !== 'GET') return ClientError::methodNotAllowed($request->getMethod(), array('GET'));
        if(!$request->isClientAbleTo(Perm::CAFET_ADMIN_GET_RELOADS)) return ClientError::Forbidden();
        
        $reloads = array();
        foreach (DataFetcher::getInstance()->getClientReloads($id) as $reload) $reloads[] = $reload->getProperties();
        if($reloads || DataFetcher::getInstance()->getClient($id)) return new RestResponse('200', HttpCodes::HTTP_200, $reloads);
        elseif (DataFetcher::getInstance()->getClient($id)) return new RestResponse('200', HttpCodes::HTTP_200, array());
        else return ClientError::resourceNotFound('Unknown client with id ' . $id);
    }
    
    private static function clientExpenses(Rest $request, int $id) : RestResponse
    {
        if($request->getMethod() !== 'GET') return ClientError::methodNotAllowed($request->getMethod(), array('GET'));
        if(!$request->isClientAbleTo(Perm::CAFET_ADMIN_GET_EXPENSES)) return ClientError::Forbidden();
        
        $expenses = array();
        foreach (DataFetcher::getInstance()->getClientExpenses($id) as $expense) $expenses[] = $expense->getProperties();
        if($expenses || DataFetcher::getInstance()->getClient($id)) return new RestResponse('200', HttpCodes::HTTP_200, $expenses);
        elseif (DataFetcher::getInstance()->getClient($id)) return new RestResponse('200', HttpCodes::HTTP_200, array());
        else return ClientError::resourceNotFound('Unknown client with id ' . $id);
    }
    
    private static function clientLastExpenses(Rest $request, int $id) : RestResponse
    {
        if($request->getMethod() !== 'GET') return ClientError::methodNotAllowed($request->getMethod(), array('GET'));
        if(!$request->isClientAbleTo(Perm::CAFET_ADMIN_GET_EXPENSES)) return ClientError::Forbidden();
        
        $expenses = array();
        foreach (DataFetcher::getInstance()->getClientLastExpenses($id) as $expense) $expenses[] = $expense->getProperties();
        if($expenses || DataFetcher::getInstance()->getClient($id)) return new RestResponse('200', HttpCodes::HTTP_200, $expenses);
        elseif (DataFetcher::getInstance()->getClient($id)) return new RestResponse('200', HttpCodes::HTTP_200, array());
        else return ClientError::resourceNotFound('Unknown client with id ' . $id);
    }
}

