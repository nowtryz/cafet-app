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
class FormulaBoughtNode implements RestNode
{
    const LIST = 'list';
    
    const PRODUCTS = 'products';

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
            
            case null: return ClientError::forbidden();
            default:
                if (intval($dir, 0)) {
                    if (!count($request->getPath())) return self::formulaBought($request, intval($dir, 0));
                    else {
                        $subdir = $request->shiftPath();
                        if ($subdir == self::PRODUCTS) return self::products($request, intval($dir, 0));
                        else return ClientError::resourceNotFound('Unknown ' . $subdir . ' node for a formula bought');
                    }
                }
                
                else return ClientError::resourceNotFound('Unknown cafet/formula_bought/' . $dir . ' node');
        }
    }
    
    private static function list(Rest $request) : RestResponse
    {
        if($request->getMethod() !== 'GET') return ClientError::methodNotAllowed($request->getMethod(), array('GET'));
        if(!$request->isClientAbleTo(Perm::CAFET_ADMIN_GET_EXPENSES)) return ClientError::forbidden();
        
        $formulasBought = array();
        foreach (DataFetcher::getInstance()->getFormulasBought() as $formulaBought) $formulasBought[] = $formulaBought->getProperties();
        return new RestResponse('200', HttpCodes::HTTP_200, $formulasBought);
    }
    
    private static function formulaBought(Rest $request, int $id) : RestResponse
    {
        if($request->getMethod() !== 'GET') return ClientError::methodNotAllowed($request->getMethod(), array('GET'));
        if(!$request->isClientAbleTo(Perm::CAFET_ADMIN_GET_EXPENSES)) return ClientError::forbidden();
        
        $formulaBought = DataFetcher::getInstance()->getFormulaBought($id);
        if($formulaBought) return new RestResponse('200', HttpCodes::HTTP_200, $formulaBought->getProperties());
        else return ClientError::resourceNotFound('Unknown formula bought with id ' . $id);
    }
    
    private static function products(Rest $request, int $id) : RestResponse
    {
        if($request->getMethod() !== 'GET') return ClientError::methodNotAllowed($request->getMethod(), array('GET'));
        if(!$request->isClientAbleTo(Perm::CAFET_ADMIN_GET_EXPENSES)) return ClientError::forbidden();
        
        $products = array();
        foreach (DataFetcher::getInstance()->getFormulaBoughtProducts($id) as $product) $products[] = $product->getProperties();
        if($products || DataFetcher::getInstance()->getClient($id)) return new RestResponse('200', HttpCodes::HTTP_200, $products);
        elseif (DataFetcher::getInstance()->getFormulaBought($id)) return new RestResponse('200', HttpCodes::HTTP_200, array());
        else return ClientError::resourceNotFound('Unknown formula bought with id ' . $id);
    }
}

