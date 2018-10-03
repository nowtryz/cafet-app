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
class ProductBoughtNode implements RestNode
{
    const LIST = 'list';

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
                if(intval($dir, 0)) return self::productBought($request, intval($dir, 0));
                else return ClientError::resourceNotFound('Unknown cafet/product_bought/' . $dir . ' node');
        }
    }
    
    private static function list(Rest $request) : RestResponse
    {
        if($request->getMethod() !== 'GET') return ClientError::methodNotAllowed($request->getMethod(), array('GET'));
        if(!$request->isClientAbleTo(Perm::CAFET_ADMIN_GET_EXPENSES)) return ClientError::Forbidden();
        
        $productsBought = array();
        foreach (DataFetcher::getInstance()->getProductsBought() as $productBought) $productsBought[] = $productBought->getProperties();
        return new RestResponse('200', HttpCodes::HTTP_200, $productsBought);
    }
    
    private static function productBought(Rest $request, int $id) : RestResponse
    {
        if($request->getMethod() !== 'GET') return ClientError::methodNotAllowed($request->getMethod(), array('GET'));
        if(!$request->isClientAbleTo(Perm::CAFET_ADMIN_GET_EXPENSES)) return ClientError::Forbidden();
        
        $productBought = DataFetcher::getInstance()->getProductBought($id);
        if($productBought) return new RestResponse('200', HttpCodes::HTTP_200, $productBought->getProperties());
        else return ClientError::resourceNotFound('Unknown product bought with id ' . $id);
    }
}

