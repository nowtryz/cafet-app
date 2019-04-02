<?php
namespace cafetapi\modules\rest\cafet;

use cafetapi\data\FormulaOrdered;
use cafetapi\data\ProductOrdered;
use cafetapi\exceptions\CafetAPIException;
use cafetapi\exceptions\NotEnoughtMoneyException;
use cafetapi\io\ClientManager;
use cafetapi\io\ExpenseManager;
use cafetapi\io\ReloadManager;
use cafetapi\modules\rest\HttpCodes;
use cafetapi\modules\rest\Rest;
use cafetapi\modules\rest\RestNode;
use cafetapi\modules\rest\RestResponse;
use cafetapi\modules\rest\errors\ClientError;
use cafetapi\modules\rest\errors\ServerError;
use cafetapi\user\Perm;
use Exception;
use cafetapi\Logger;

/**
 *
 * @author damie
 *        
 */
class ClientsNode implements RestNode
{
    const SEARCH = 'search';
    
    const RELOADS = 'reloads';
    const EXPENSES = 'expenses';
    const LAST_EXPENSES = 'last_expenses';
    const ORDER = 'order';
    
    

    /**
     * (non-PHPdoc)
     *
     * @see \cafetapi\modules\rest\RestNode::handle()
     */
    public static function handle(Rest $request) : RestResponse
    {
        $dir = $request->shiftPath();
        
        switch ($dir) {
            case self::SEARCH: return self::search($request);
            
            case null: return self::list($request);
            default:
                if(intval($dir, 0)) {
                    if(!count($request->getPath())) return self::client($request, intval($dir, 0));
                    else {
                        $subdir = $request->shiftPath();
                        switch ($subdir) {
                            case self::RELOADS:       return self::clientReloads($request, intval($dir, 0));
                            case self::EXPENSES:      return self::clientExpenses($request, intval($dir, 0));
                            case self::LAST_EXPENSES: return self::clientLastExpenses($request, intval($dir, 0));
                            case self::ORDER:         return self::clientOrder($request, intval($dir, 0));
                            
                            default: return ClientError::resourceNotFound('Unknown ' . $subdir . ' node for a client');
                        }
                    }
                }
                
                else return ClientError::resourceNotFound('Unknown cafet/client/' . $dir . ' node');
        }
    }
    
    private static function list(Rest $request) : RestResponse
    {
        $request->allowMethods('GET');
        $request->needPermissions(Perm::CAFET_ADMIN_GET_CLIENTS);
        
        $clients = array();
        foreach (ClientManager::getInstance()->getClients() as $client) $clients[] = $client->getProperties();
        return new RestResponse('200', HttpCodes::HTTP_200, $clients);
    }
    
    private static function search(Rest $request) : RestResponse
    {
        $request->allowMethods('GET');
        $request->needPermissions(Perm::CAFET_ADMIN_GET_CLIENTS);
        
        $clients = array();
        foreach (ClientManager::getInstance()->searchClient(urldecode($request->shiftPath())) as $client) $clients[] = $client->getProperties();
        return new RestResponse('200', HttpCodes::HTTP_200, $clients);
        
        
    }
    
    private static function client(Rest $request, int $id) : RestResponse
    {
        $request->allowMethods('GET', 'PATCH');
        
        switch ($request->getMethod()) {
            case 'GET': return self:: get($request, $id);
            case 'PATCH': return self::patch($request, $id);
        }
    }
    
    private static function get(Rest $request, int $id) : RestResponse
    {
        if($request->getUser() && $request->getUser()->getId() == $id) $request->needPermissions(Perm::CAFET_ME_CLIENT);
        else                                                           $request->needPermissions(Perm::CAFET_ADMIN_GET_CLIENTS);
        
        $client = ClientManager::getInstance()->getClient($id);
        if($client) return new RestResponse('200', HttpCodes::HTTP_200, $client->getProperties());
        else return ClientError::resourceNotFound('Unknown client with id ' . $id);
    }
    
    private static function patch(Rest $request, int $id) : RestResponse
    {
        $request->needPermissions(Perm::CAFET_ADMIN_MANAGE_CLIENTS);
        
        $manager = ClientManager::getInstance();
        $body = $request->getBody();
        
        if (!$manager->getClient($id)) return ClientError::resourceNotFound('Unknown client with id ' . $id);
        if($body && isset($body['member']))
        {
            $request->checkBody(array(
                'member' => Rest::PARAM_BOOL
            ));
            
            try {
                $manager->setMember($id, $body['member']);
            } catch (Exception $e) {
                Logger::log($e);
                return ServerError::internalServerError();
            }
        }
        
        return new RestResponse('204', HttpCodes::HTTP_204, null);
    }
    
    private static function clientReloads(Rest $request, int $id) : RestResponse
    {
        $request->allowMethods('GET');
        
        if($request->getUser() && $request->getUser()->getId() == $id) $request->needPermissions(Perm::CAFET_ME_RELOADS);
        else                                                           $request->needPermissions(Perm::CAFET_ADMIN_GET_RELOADS);
        
        $reloads = array();
        foreach (ReloadManager::getInstance()->getClientReloads($id) as $reload) $reloads[] = $reload->getProperties();
        if($reloads || ClientManager::getInstance()->getClient($id)) return new RestResponse('200', HttpCodes::HTTP_200, $reloads);
        elseif (ClientManager::getInstance()->getClient($id)) return new RestResponse('200', HttpCodes::HTTP_200, array());
        else return ClientError::resourceNotFound('Unknown client with id ' . $id);
    }
    
    private static function clientExpenses(Rest $request, int $id) : RestResponse
    {
        $request->allowMethods('GET');
        
        if($request->getUser() && $request->getUser()->getId() == $id) $request->needPermissions(Perm::CAFET_ME_EXPENSES);
        else                                                           $request->needPermissions(Perm::CAFET_ADMIN_GET_EXPENSES);
        
        $expenses = array();
        foreach (ExpenseManager::getInstance()->getClientExpenses($id) as $expense) $expenses[] = $expense->getProperties();
        if($expenses || ClientManager::getInstance()->getClient($id)) return new RestResponse('200', HttpCodes::HTTP_200, $expenses);
        elseif (ClientManager::getInstance()->getClient($id)) return new RestResponse('200', HttpCodes::HTTP_200, array());
        else return ClientError::resourceNotFound('Unknown client with id ' . $id);
    }
    
    private static function clientLastExpenses(Rest $request, int $id) : RestResponse
    {
        $request->allowMethods('GET');
        
        if($request->getUser() && $request->getUser()->getId() == $id) $request->needPermissions(Perm::CAFET_ME_EXPENSES);
        else                                                           $request->needPermissions(Perm::CAFET_ADMIN_GET_EXPENSES);
        
        $expenses = array();
        foreach (ExpenseManager::getInstance()->getClientLastExpenses($id) as $expense) $expenses[] = $expense->getProperties();
        
        if($expenses) return new RestResponse('200', HttpCodes::HTTP_200, $expenses);
        elseif (ClientManager::getInstance()->getClient($id)) return new RestResponse('200', HttpCodes::HTTP_200, array());
        else return ClientError::resourceNotFound('Unknown client with id ' . $id);
    }

    private static function clientOrder(Rest $request, int $client_id) : RestResponse
    {
        $request->allowMethods('POST');
        $request->needPermissions(Perm::CAFET_ADMIN_ORDER);

        //body checks
        if (!$request->getBody()) return ClientError::badRequest('Empty body');

        $order = array();

        foreach ($request->getBody() as $entry) {
            if (! isset($entry['type'])) return ClientError::badRequest('Missing `type` field');
            if (! isset($entry['id'])) return ClientError::badRequest('Missing `id` field');
            if (! isset($entry['amount'])) return ClientError::badRequest('Missing `amount` field');
            
            $id = intval($entry['id'], 0);
            $amount = intval($entry['amount']);

            if (!$id)     return ClientError::badRequest('Expected `id` field to be an integer');
            if (!$amount) return ClientError::badRequest('Expected `amount` field to be an integer');

            if ($entry['type'] == 'product') $order[] = new ProductOrdered($id, $amount);
            elseif ($entry['type'] == 'formula')
            {
                if (! isset($entry['products']))              return ClientError::badRequest('Missing `products` field for a formula');
                if (! is_array($entry['products']))           return ClientError::badRequest('`products` field for a formula must be an array');
                if (is_associative_array($entry['products'])) return ClientError::badRequest('`products` field for a formula must not be an object');

                $products = array();
                
                foreach ($entry['products'] as $p) $products[] = intval($p, 0);
                
                $order[] = new FormulaOrdered($id, $amount, $products);
            }
            else ClientError::badRequest($entry['type'] . ' isn\'t a valid type');
        }
        
        try {
            // TODO retrun the id of the expense
            ExpenseManager::getInstance()->saveOrder($client_id, $order);
        } catch (NotEnoughtMoneyException $e) {
            return ClientError::conflict($e->getMessage());
        } catch (CafetAPIException $e) {
            if ($e->getCode() == 3005)
            {
                $matches = array();
                preg_match('/\D*(product|formula)\D+(\d+).*/', $e->getMessage(), $matches);
                return ClientError::conflict($e->getMessage(), array('On' => 'type: ' . @$matches[1] . '; id: ' . @$matches[2]));
            }
            else throw $e;
        }

        return new RestResponse(201, HttpCodes::HTTP_201, null);
    }
}

