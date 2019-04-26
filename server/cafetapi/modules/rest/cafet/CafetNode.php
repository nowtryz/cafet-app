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
    const CLIENTS = 'clients';
    const RELOADS = 'reloads';
    const EXPENSES = 'expenses';
    const PRODUCTS_BOUGHT = 'products_bought';
    const FORMULAS_BOUGHT = 'formulas_bought';
    const GROUPS = 'groups';
    const PRODUCTS = 'products';
    const FORMULAS = 'formulas';
    const CHOICES = 'choices';
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
            case self::CLIENTS:         return ClientsNode::handle($request);
            case self::RELOADS:         return ReloadsNode::handle($request);
            case self::EXPENSES:        return ExpensesNode::handle($request);
            case self::PRODUCTS_BOUGHT: return ProductsBoughtNode::handle($request);
            case self::FORMULAS_BOUGHT: return FormulasBoughtNode::handle($request);
            case self::GROUPS:          return GroupsNode::handle($request);
            case self::PRODUCTS:        return ProductsNode::handle($request);
            case self::FORMULAS:        return FormulasNode::handle($request);
            case self::CHOICES:         return ChoicesNode::handle($request);
            
            case null: return ClientError::forbidden();
            default:   return ClientError::resourceNotFound('Unknown cafet/' . $dir . ' node');
        }
    }
}

