<?php
namespace cafetapi\modules\rest\cafet;

use cafetapi\io\DataFetcher;
use cafetapi\modules\rest\HttpCodes;
use cafetapi\modules\rest\Rest;
use cafetapi\modules\rest\RestNode;
use cafetapi\modules\rest\RestResponse;
use cafetapi\modules\rest\errors\ClientError;
use cafetapi\user\Perm;

/**
 *
 * @author damie
 *        
 */
class ExpenseNode implements RestNode
{
    const LIST = 'list';
    
    const DETAILS = 'details';

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
            
            case null: return ClientError::Forbidden();
            default:
                if(intval($dir)) {
                    if(!count($request->getPath())) return self::expense($request, intval($dir));
                    else {
                        $subdir = $request->shiftPath();
                        switch ($subdir) {
                            case self::DETAILS:       return self::expenseDetails($request, intval($dir));
                            
                            default: return ClientError::resourceNotFound('Unknown ' . $subdir . ' node for an expense');
                        }
                    }
                }
                
                else return ClientError::resourceNotFound('Unknown cafet/expense/' . $dir . ' node');
        }
    }
    
    private static function list(Rest $request) : RestResponse
    {
        if($request->getMethod() !== 'GET') return ClientError::methodNotAllowed($request->getMethod(), array('GET'));
        if(!$request->isClientAbleTo(Perm::CAFET_ADMIN_GET_EXPENSES)) return ClientError::Forbidden();
        
        $expenses = array();
        foreach ((new DataFetcher())->getExpenses() as $expense) $expenses[] = $expense->getProperties();
        return new RestResponse('200', HttpCodes::HTTP_200, $expenses);
    }
    
    private static function expense(Rest $request, int $id) : RestResponse
    {
        if($request->getMethod() !== 'GET') return ClientError::methodNotAllowed($request->getMethod(), array('GET'));
        if(!$request->isClientAbleTo(Perm::CAFET_ADMIN_GET_EXPENSES)) return ClientError::Forbidden();
        
        $reload = (new DataFetcher())->getReload($id);
        if($reload) return new RestResponse('200', HttpCodes::HTTP_200, $reload->getProperties());
        else return ClientError::resourceNotFound('Unknown reload with id ' . $id);
    }
    
    private static function expenseDetails(Rest $request, int $id) : RestResponse
    {
        if($request->getMethod() !== 'GET') return ClientError::methodNotAllowed($request->getMethod(), array('GET'));
        if(!$request->isClientAbleTo(Perm::CAFET_ADMIN_GET_EXPENSES)) return ClientError::Forbidden();
        
        $expenses = array();
        foreach ((new DataFetcher())->getClientExpenses($id) as $expense) $expenses[] = $expense->getProperties();
        if($expenses || (new DataFetcher())->getClient($id)) return new RestResponse('200', HttpCodes::HTTP_200, $expenses);
        elseif ((new DataFetcher())->getClient($id)) return new RestResponse('200', HttpCodes::HTTP_200, array());
        else return ClientError::resourceNotFound('Unknown client with id ' . $id);
    }
}

