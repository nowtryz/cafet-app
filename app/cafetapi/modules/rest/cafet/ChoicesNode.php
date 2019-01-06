<?php
namespace cafetapi\modules\rest\cafet;

use cafetapi\modules\rest\HttpCodes;
use cafetapi\modules\rest\Rest;
use cafetapi\modules\rest\RestNode;
use cafetapi\modules\rest\RestResponse;
use cafetapi\modules\rest\errors\ClientError;
use cafetapi\modules\rest\errors\ServerError;
use cafetapi\user\Perm;
use cafetapi\io\FormulaManager;
use cafetapi\io\ProductManager;

/**
 *
 * @author damie
 *        
 */
class ChoicesNode implements RestNode
{
    const ADD = 'add';

    /**
     * (non-PHPdoc)
     *
     * @see \cafetapi\modules\rest\RestNode::handle()
     */
    public static function handle(Rest $request) : RestResponse
    {
        $dir = $request->shiftPath();
        
        if (intval($dir, 0)) {
            $request->allowMethods('GET', 'PUT', 'PATCH', 'DELETE');
            
            $choice_id = intval($dir, 0);
            
            switch ($request->getMethod())
            {
                case 'GET':    return self::getChoice($request, $choice_id);
                case 'PUT':    return self::putChoice($request, $choice_id);
                case 'PATCH':  return self::patchChoice($request, $choice_id);
                case 'DELETE': return self::deleteChoice($request, $choice_id);
            }
        }
        elseif (!$dir) return self::listChoices($request);
        else return ClientError::resourceNotFound('Unknown cafet/choices/' . $dir . ' node');
    }
    
    private static function listChoices(Rest $request) : RestResponse
    {
        $request->allowMethods('GET');
        $request->needPermissions(Perm::CAFET_ADMIN_GET_FORMULAS);
        
        $choices = array();
        
        foreach (FormulaManager::getInstance()->getChoices() as $choice) {
            if (isset($_REQUEST['noimage'])) {
                $vars = $choice->getProperties();
                foreach ($vars['choice'] as &$product) unset($product['image']);
                $choices[] = $vars;
            } else $choices[] = $choice->getProperties();
        }
        
        return new RestResponse('200', HttpCodes::HTTP_200, $choices);
    }
    
    private static function getChoice(Rest $request, int $choice_id) : RestResponse
    {
        $request->needPermissions(Perm::CAFET_ADMIN_GET_FORMULAS);
        
        $choice = FormulaManager::getInstance()->getChoice($choice_id);
        
        if (isset($_REQUEST['noimage'])) {
            $properties = $choice->getProperties();
            foreach ($properties['choice'] as &$product) unset($product['image']);
            return new RestResponse('200', HttpCodes::HTTP_200, $properties);
        } else return new RestResponse('200', HttpCodes::HTTP_200, $choice->getProperties());
    }
    
    private static function putChoice(Rest $request, int $choice_id) : RestResponse
    {
        $request->needPermissions(Perm::CAFET_ADMIN_MANAGE_FORMULAS);
        
        $choice = FormulaManager::getInstance()->getChoice($choice_id);
        
        //body checks
        if (!$request->getBody())                     return ClientError::badRequest('Empty body');
        if (!isset($request->getBody()['id']))        return ClientError::badRequest('Missing `id` field');
        if (!isset($request->getBody()['type']))      return ClientError::badRequest('Missing `type` field');
        if (!isset($request->getBody()['name']))      return ClientError::badRequest('Missing `name` field');
        if (!isset($request->getBody()['choice']))    return ClientError::badRequest('Missing `choice` field');
        if (!intval($request->getBody()['id'], 0))    return ClientError::badRequest('Expected `id` field to be an integer');
        if (!is_array($request->getBody()['choice'])) return ClientError::badRequest('Expected `choice` field to be an array');
        
        if ($choice->getId() != intval($request->getBody()['id']))        return ClientError::conflict('different id');
        if (get_simple_classname($choice) != $request->getBody()['type']) return ClientError::conflict('different type');
        
        $name = strval($request->getBody()['name']);
        $choice = $request->getBody()['choice'];
        
        $updater = FormulaManager::getInstance();
        $updater->createTransaction();
        
        try {
            $updater->setFormulaChoiceName($choice_id, $name);
            $updater->removeAllProductsFromChoice($choice_id);
            
            foreach ($choice as $product)
            {
                if (!ProductManager::getInstance()->getProduct(intval($product, 0)))
                {
                    $updater->cancelTransaction();
                    return ClientError::conflict('product ' . intval($product, 0) . ' does not exist');
                }
                
                $updater->addProductToChoice($choice_id, intval($product, 0));
            }
            
            $updater->confirmTransaction();
        } catch (\Error | \Exception $e) {
            $updater->cancelTransaction();
            cafet_log($e->__toString());
            return ServerError::internalServerError();
        }
        
        $choice = $updater->getChoice($choice_id);
        
        
        
        if ($choice) if (isset($_REQUEST['noimage'])) {
            $properties = $choice->getProperties();
            foreach ($properties['choice'] as &$product) unset($product['image']);
            return new RestResponse('200', HttpCodes::HTTP_200, $properties);
        } else return new RestResponse('200', HttpCodes::HTTP_200, $choice->getProperties());
        else return ServerError::internalServerError();
    }
    
    private static function patchChoice(Rest $request, int $choice_id) : RestResponse
    {
        $request->needPermissions(Perm::CAFET_ADMIN_MANAGE_FORMULAS);
        
        $updater = FormulaManager::getInstance();
        $updater->createTransaction();
        
        try {
            foreach ($request->getBody() as $field => $value) switch ($field)
            {
                case 'add':
                    if (!is_array($value))
                    {
                        $updater->cancelTransaction();
                        return ClientError::badRequest('Expected `add` field to be an array of products id to add');
                    }
                    
                    foreach ($value as $product)
                    {
                        if (!ProductManager::getInstance()->getProduct(intval($product, 0)))
                        {
                            $updater->cancelTransaction();
                            return ClientError::conflict('product ' . intval($product, 0) . ' does not exist');
                        }
                        
                        $updater->addProductToChoice($choice_id, intval($product, 0));
                    }
                    break;
                    
                case 'remove':
                    if (!is_array($value))
                    {
                        $updater->cancelTransaction();
                        return ClientError::badRequest('Expected `remove` field to be an array of products id to remove');
                    }
                    
                    foreach ($value as $product) $updater->removeProductFromChoice($choice_id, intval($product, 0));
                    break;
                    
                case 'name':
                    $updater->setFormulaChoiceName($choice_id, strval($value));
                    break;
            }
            
            $updater->confirmTransaction();
        } catch (\Error | \Exception $e) {
            $updater->cancelTransaction();
            cafet_log($e->__toString());
            return ServerError::internalServerError();
        }
        
        return new RestResponse('204', HttpCodes::HTTP_204, null);
    }
    
    private static function deleteChoice(Rest $request, int $choice_id) : RestResponse
    {
        $request->needPermissions(Perm::CAFET_ADMIN_MANAGE_FORMULAS);
        if (FormulaManager::getInstance()->deleteFormulaChoice($choice_id)) return new RestResponse('204', HttpCodes::HTTP_204, null);
        else return ServerError::internalServerError();
    }
}

