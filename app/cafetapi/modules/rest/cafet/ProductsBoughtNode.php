<?php
namespace cafetapi\modules\rest\cafet;

use cafetapi\modules\rest\HttpCodes;
use cafetapi\modules\rest\Rest;
use cafetapi\modules\rest\RestNode;
use cafetapi\modules\rest\RestResponse;
use cafetapi\modules\rest\errors\ClientError;
use cafetapi\user\Perm;
use cafetapi\io\ExpenseManager;

/**
 *
 * @author damie
 *        
 */
class ProductsBoughtNode implements RestNode
{
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
                if(intval($dir, 0)) return self::productBought($request, intval($dir, 0));
                else return ClientError::resourceNotFound('Unknown cafet/product_bought/' . $dir . ' node');
        }
    }
    
    private static function list(Rest $request) : RestResponse
    {
        $request->allowMethods(array('GET'));
        $request->needPermissions(Perm::CAFET_ADMIN_GET_EXPENSES);
        
        $productsBought = array();
        foreach (ExpenseManager::getInstance()->getProductsBought() as $productBought) $productsBought[] = $productBought->getProperties();
        return new RestResponse('200', HttpCodes::HTTP_200, $productsBought);
    }
    
    private static function productBought(Rest $request, int $id) : RestResponse
    {
        $request->allowMethods(array('GET'));
        $request->needPermissions(Perm::CAFET_ADMIN_GET_EXPENSES);
        
        $productBought = ExpenseManager::getInstance()->getProductBought($id);
        if($productBought) return new RestResponse('200', HttpCodes::HTTP_200, $productBought->getProperties());
        else return ClientError::resourceNotFound('Unknown product bought with id ' . $id);
    }
}

