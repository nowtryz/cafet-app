<?php
namespace cafetapi\modules\rest\cafet;

use cafetapi\modules\rest\Rest;
use cafetapi\modules\rest\RestNode;
use cafetapi\modules\rest\errors\ClientError;
use cafetapi\modules\rest\RestResponse;

/**
 *
 * @author damie
 *        
 */
class CafetNode implements RestNode
{
    const CLIENT = 'client';
    const RELOAD = 'reload';
    const EXPENSE = 'expense';
    const PRODUCT_BOUGHT = 'product_bought';
    const FORMULA_BOUGHT = 'formula_bought';
    const GROUP = 'group';
    const PRODUCT = 'product';
    const FORMULA = 'formula';
    const CHOICE = 'choice';
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
            case self::CLIENT:         return ClientNode::handle($request);
            case self::RELOAD:         return ReloadNode::handle($request);
            case self::EXPENSE:        return ExpenseNode::handle($request);
            case self::PRODUCT_BOUGHT: return ProductBoughtNode::handle($request);
            case self::FORMULA_BOUGHT: return FormulaBoughtNode::handle($request);
            case self::GROUP:          return GroupNode::handle($request);
            case self::PRODUCT:        return ProductNode::handle($request);
            case self::FORMULA:        return FormulaNode::handle($request);
            case self::CHOICE:         return ChoiceNode::handle($request);
            case self::ORDER:          return OrderNode::handle($request);
            
            case null: return ClientError::forbidden();
            default:   return ClientError::resourceNotFound('Unknown cafet/' . $dir . ' node');
        }
    }
}

