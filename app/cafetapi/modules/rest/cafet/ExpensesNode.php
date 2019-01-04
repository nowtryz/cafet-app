<?php
namespace cafetapi\modules\rest\cafet;

use cafetapi\io\ExpenseManager;
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
class ExpensesNode implements RestNode
{
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
            case null: return self::list($request);
            
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
        $request->allowMethods('GET');
        $request->needPermissions(Perm::CAFET_ADMIN_GET_EXPENSES);
        
        $expenses = array();
        foreach (ExpenseManager::getInstance()->getExpenses() as $expense) $expenses[] = $expense->getProperties();
        return new RestResponse('200', HttpCodes::HTTP_200, $expenses);
    }
    
    private static function expense(Rest $request, int $id) : RestResponse
    {
        $request->allowMethods('GET');
        
        $expense = ExpenseManager::getInstance()->getExpense($id);
        
        if($request->getUser() && $request->getUser()->getId() == $expense->getClient())
        {
            $request->needPermissions(Perm::CAFET_ME_EXPENSES);
        }
        else $request->needPermissions(Perm::CAFET_ADMIN_GET_EXPENSES);
        
        if($expense) return new RestResponse('200', HttpCodes::HTTP_200, $expense->getProperties());
        else return ClientError::resourceNotFound('Unknown expense with id ' . $id);
    }
    
    private static function expenseDetails(Rest $request, int $id) : RestResponse
    {
        $request->allowMethods('GET');
        
        $expense = ExpenseManager::getInstance()->getExpense($id);
        if (!$expense) return ClientError::resourceNotFound('Unknown expense with id ' . $id);
        
        if($request->getUser() && $request->getUser()->getId() == $expense->getClient())
        {
            $request->needPermissions(Perm::CAFET_ME_EXPENSES);
        }
        else $request->needPermissions(Perm::CAFET_ADMIN_GET_EXPENSES);;
        
        $details = array();
        foreach (ExpenseManager::getInstance()->getExpenseDetails($id) as $detail) $details[] = $detail->getProperties();
        return new RestResponse('200', HttpCodes::HTTP_200, $details);
    }
}

