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
                if(intval($dir, 0)) {
                    if(!count($request->getPath())) return self::expense($request, intval($dir, 0));
                    else {
                        $subdir = $request->shiftPath();
                        switch ($subdir) {
                            case self::DETAILS:       return self::expenseDetails($request, intval($dir, 0));
                            
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
        foreach (DataFetcher::getInstance()->getExpenses() as $expense) $expenses[] = $expense->getProperties();
        return new RestResponse('200', HttpCodes::HTTP_200, $expenses);
    }
    
    private static function expense(Rest $request, int $id) : RestResponse
    {
        if($request->getMethod() !== 'GET') return ClientError::methodNotAllowed($request->getMethod(), array('GET'));
        if(!$request->isClientAbleTo(Perm::CAFET_ADMIN_GET_EXPENSES)) return ClientError::Forbidden();
        
        $reload = DataFetcher::getInstance()->getExpense($id);
        if($reload) return new RestResponse('200', HttpCodes::HTTP_200, $reload->getProperties());
        else return ClientError::resourceNotFound('Unknown expense with id ' . $id);
    }
    
    private static function expenseDetails(Rest $request, int $id) : RestResponse
    {
        if($request->getMethod() !== 'GET') return ClientError::methodNotAllowed($request->getMethod(), array('GET'));
        if(!$request->isClientAbleTo(Perm::CAFET_ADMIN_GET_EXPENSES)) return ClientError::Forbidden();
        
        $details = array();
        foreach (DataFetcher::getInstance()->getExpenseDetails($id) as $detail) $details[] = $detail->getProperties();
        
        if($details) return new RestResponse('200', HttpCodes::HTTP_200, $details);
        elseif (DataFetcher::getInstance()->getExpense($id)) return new RestResponse('200', HttpCodes::HTTP_200, array());
        else return ClientError::resourceNotFound('Unknown expense with id ' . $id);
    }
}

