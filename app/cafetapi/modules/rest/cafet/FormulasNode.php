<?php
namespace cafetapi\modules\rest\cafet;

use cafetapi\Logger;
use cafetapi\io\FormulaManager;
use cafetapi\io\ProductManager;
use cafetapi\modules\rest\HttpCodes;
use cafetapi\modules\rest\Rest;
use cafetapi\modules\rest\RestNode;
use cafetapi\modules\rest\RestResponse;
use cafetapi\modules\rest\errors\ClientError;
use cafetapi\modules\rest\errors\ServerError;
use cafetapi\user\Perm;

/**
 *
 * @author damie
 *        
 */
class FormulasNode implements RestNode
{
    const CHOICES = 'choices';
    const CHOICE = 'choice';

    /**
     * (non-PHPdoc)
     *
     * @see \cafetapi\modules\rest\RestNode::handle()
     */
    public static function handle(Rest $request) : RestResponse
    {
        $dir = $request->shiftPath();
        
        switch ($dir) {
            case null: return self::index($request);
            
            default:
                if (intval($dir, 0)) {
                    if (!count($request->getPath())) return self::formula($request, intval($dir, 0));
                    else {
                        $subdir = $request->shiftPath();
                        switch ($subdir)
                        {
                            case self::CHOICES:  return self::choices($request, intval($dir, 0));
                            default: return ClientError::resourceNotFound('Unknown ' . $subdir . ' node for a formula');
                        }
                        
                        
                    }
                }
                
                else return ClientError::resourceNotFound('Unknown cafet/formula/' . $dir . ' node');
        }
    }
    
    private static function index(Rest $request) : RestResponse
    {
        $request->allowMethods('GET','POST');
        
        switch ($request->getMethod()) {
            case 'GET': return self::list($request);
            case 'POST': return self::new($request);
        }
    }

    private static function list(Rest $request) : RestResponse
    {
        $request->needPermissions(Perm::CAFET_ADMIN_GET_FORMULAS);
        
        $formulas = array();
        foreach (FormulaManager::getInstance()->getFormulas(isset($_REQUEST['hidden'])) as $formula){
            $properties = $formula->getProperties();
            if (isset($_REQUEST['noimage'])) unset($properties['image']);
            $formulas[] = $properties;
        }
        return new RestResponse('200', HttpCodes::HTTP_200, $formulas);
    }
    
    private static function new(Rest $request) : RestResponse
    {
        $request->needPermissions(Perm::CAFET_ADMIN_MANAGE_FORMULAS);
        
        //body checks
        $request->checkBody(array(
            'name' => Rest::PARAM_STR,
            'price' => Rest::PARAM_SCALAR,
            'viewable' => Rest::PARAM_BOOL
        ));

        $name = $request->getBody()['name'];
        $price = floatval($request->getBody()['price']);
        $visibility = boolval($request->getBody()['viewable']);
        
        $updater = FormulaManager::getInstance();
        $updater->createTransaction();
        $formula = null;
        
        try {
            $f = $updater->addFormula($name);
            
            $updater->setFormulaPrice($f->getId(), $price);
            $updater->setFormulaViewable($f->getId(), $visibility);
            
            $formula = FormulaManager::getInstance()->getFormula($f->getId());
            
            $updater->confirmTransaction();
        } catch (\Error | \Exception $e) {
            $updater->cancelTransaction();
            Logger::log($e->__toString());
            return ServerError::internalServerError();
        }
        
        if ($formula) return new RestResponse('201', HttpCodes::HTTP_201, $formula->getProperties());
        else return ServerError::internalServerError();
    }
    
    
    
    
    private static function formula(Rest $request, int $id) : RestResponse
    {
        $request->allowMethods('GET','PUT','PATCH','DELETE');
        
        switch ($request->getMethod())
        {
            case 'GET':    return self::getFormula($request, $id);
            case 'PUT':    return self::putFormula($request, $id);
            case 'PATCH':  return self::patchFormula($request, $id);
            case 'DELETE': return self::deleteFormula($request, $id);
        }
    }
    
    private static function getFormula(Rest $request, int $id) : RestResponse
    {
        $request->needPermissions(Perm::CAFET_ADMIN_GET_FORMULAS);
        
        $formula = FormulaManager::getInstance()->getFormula($id);
        
        if (!$formula) return ClientError::resourceNotFound('Unknown formula with id ' . $id);
        
        $properties = $formula->getProperties();
        if (isset($_REQUEST['noimage'])) unset($properties['image']);
        return new RestResponse('200', HttpCodes::HTTP_200, $properties);
    }
    
    private static function putFormula(Rest $request, int $id) : RestResponse
    {
        $request->needPermissions(Perm::CAFET_ADMIN_MANAGE_FORMULAS);
        
        $formula = FormulaManager::getInstance()->getFormula($id);
        if (!$formula) return ClientError::resourceNotFound('Unknown product with id ' . $id);
        
        //body checks
        $request->checkBody(array(
            'id' => Rest::PARAM_INT,
            'type' => Rest::PARAM_STR,
            'name' => Rest::PARAM_STR,
            'price' => Rest::PARAM_SCALAR,
            'viewable' => Rest::PARAM_BOOL
        ));

        if ($formula->getId() != intval($request->getBody()['id']))        return ClientError::conflict('different id');
        if (get_simple_classname($formula) != $request->getBody()['type']) return ClientError::conflict('different type');
        
        $name = $request->getBody()['name'];
        $price = floatval($request->getBody()['price']);
        $visibility = boolval($request->getBody()['viewable']);
        
        $updater = FormulaManager::getInstance();
        $updater->createTransaction();
        
        try {
            $updater->setFormulaName($id, $name);
            $updater->setFormulaPrice($id, $price);
            $updater->setFormulaViewable($id, $visibility);
            
            $updater->confirmTransaction();
        } catch (\Error | \Exception $e) {
            $updater->cancelTransaction();
            Logger::log($e->__toString());
            return ServerError::internalServerError();
        }
        
        $formula = $updater->getFormula($id);
        
        return new RestResponse('200', HttpCodes::HTTP_200, $formula->getProperties());
    }
    
    private static function patchFormula(Rest $request, int $id) : RestResponse
    {
        $request->needPermissions(Perm::CAFET_ADMIN_MANAGE_FORMULAS);
        
        if (!FormulaManager::getInstance()->getFormula($id)) return ClientError::resourceNotFound('Unknown formula with id ' . $id);
        
        $updater = FormulaManager::getInstance();
        $updater->createTransaction();
        
        try {
            foreach ($request->getBody() as $field => $value) switch ($field)
            {
                case 'name':
                    $updater->setFormulaName($id, strval($value));
                    break;
                    
                case 'image':
                    if (!is_string($value))
                    {
                        $updater->cancelTransaction();
                        return ClientError::badRequest('Expected `image` field to be a string representing the image as base64');
                    }
                    
                    $updater->setFormulaImage($id, $value);
                    break;
                    
                case 'price':
                    if (!is_scalar($value))
                    {
                        $updater->cancelTransaction();
                        return ClientError::badRequest('Expected `price` field to be a float');
                    }
                    
                    $updater->setFormulaPrice($id, floatval($value));
                    break;
                    
                case 'viewable':
                    if (!is_bool($value))
                    {
                        $updater->cancelTransaction();
                        return ClientError::badRequest('Expected `viewable` field to be a boolean');
                    }
                    
                    $updater->setFormulaViewable($id, boolval($value));
                    break;
            }
            
            $updater->confirmTransaction();
        } catch (\Error | \Exception $e) {
            $updater->cancelTransaction();
            Logger::log($e->__toString());
            return ServerError::internalServerError();
        }
        
        return new RestResponse('204', HttpCodes::HTTP_204, null);
    }
    
    private static function deleteFormula(Rest $request, int $id) : RestResponse
    {
        $request->needPermissions(Perm::CAFET_ADMIN_MANAGE_FORMULAS);
        
        if (!FormulaManager::getInstance()->getFormula($id)) return ClientError::resourceNotFound('Unknown formula with id ' . $id);
        
        if (FormulaManager::getInstance()->deleteFormula($id)) return new RestResponse('204', HttpCodes::HTTP_204, null);
        else return ServerError::internalServerError();
    }
    
    
    
    
    const ADD = 'add';
    
    private static function choices(Rest $request, int $formula_id) : RestResponse
    {
        if (!FormulaManager::getInstance()->getFormula($formula_id)) return ClientError::resourceNotFound('Unknown formula with id ' . $formula_id);
        $dir = $request->shiftPath();
        
        if (intval($dir, 0)) {
            $request->allowMethods('GET','PUT','PATCH','DELETE');
            $choice_id = intval($dir, 0);
            if(!in_array($choice_id, FormulaManager::getInstance()->getFormulaChoicesIDs($formula_id))) return ClientError::resourceNotFound('Unknown choice with id ' . $choice_id . ' for the formula ' . $formula_id);
            
            switch ($request->getMethod())
            {
                case 'GET':    return self::getChoice($request, $formula_id, $choice_id);
                case 'PUT':    return self::putChoice($request, $formula_id, $choice_id);
                case 'PATCH':  return self::patchChoice($request, $formula_id, $choice_id);
                case 'DELETE': return self::deleteChoice($request, $formula_id, $choice_id);
            }
        }
        elseif ($dir == self::ADD) return self::addChoice($request, $formula_id);
        elseif (!$dir) return self::listChoices($request, $formula_id);
        else return ClientError::resourceNotFound('Unknown cafet/formula/' . $formula_id . '/choice/' . $dir . ' node');
    }
    
    private static function listChoices(Rest $request, int $id) : RestResponse
    {
        $request->allowMethods('GET');
        $request->needPermissions(Perm::CAFET_ADMIN_GET_FORMULAS);
        
        $choices = array();
        
        foreach (FormulaManager::getInstance()->getFormulaChoices($id) as $choice) {
            if (isset($_REQUEST['noimage'])) {
                $vars = $choice->getProperties();
                foreach ($vars['choice'] as &$product) unset($product['image']);
                $choices[] = $vars;
            } else $choices[] = $choice->getProperties();
        }
        
        if($choices || FormulaManager::getInstance()->getFormula($id)) return new RestResponse('200', HttpCodes::HTTP_200, $choices);
        elseif (FormulaManager::getInstance()->getFormula($id)) return new RestResponse('200', HttpCodes::HTTP_200, array());
        else return ClientError::resourceNotFound('Unknown formula with id ' . $id);
    }
    
    private static function addChoice(Rest $request, int $formula_id) : RestResponse
    {
        $request->allowMethods('POST');
        $request->needPermissions(Perm::CAFET_ADMIN_MANAGE_FORMULAS);
        
        //body checks
        $request->checkBody(array(
            'name' => Rest::PARAM_ANY
        ));

        $name = strval($request->getBody()['name']);
        
        $updater = FormulaManager::getInstance();
        $updater->createTransaction();
        $choice = null;
        
        try {
            $choice = $updater->addChoice($name, $formula_id);
            
            $updater->confirmTransaction();
        } catch (\Error | \Exception $e) {
            $updater->cancelTransaction();
            Logger::log($e->__toString());
            return ServerError::internalServerError();
        }
        
        if ($choice)return new RestResponse('201', HttpCodes::HTTP_201, $choice->getProperties());
        else return ServerError::internalServerError();
    }
    
    private static function getChoice(Rest $request, int $formula_id, int $choice_id) : RestResponse
    {
        $request->needPermissions(Perm::CAFET_ADMIN_GET_FORMULAS);
        
        $choice = FormulaManager::getInstance()->getChoice($choice_id);
        
        if (isset($_REQUEST['noimage'])) {
            $properties = $choice->getProperties();
            foreach ($properties['choice'] as &$product) unset($product['image']);
            return new RestResponse('200', HttpCodes::HTTP_200, $properties);
        } else return new RestResponse('200', HttpCodes::HTTP_200, $choice->getProperties());
    }
    
    private static function putChoice(Rest $request, int $formula_id, int $choice_id) : RestResponse
    {
        $request->needPermissions(Perm::CAFET_ADMIN_MANAGE_FORMULAS);
        
        $choice = FormulaManager::getInstance()->getChoice($choice_id);
        
        //body checks
        $request->checkBody(array(
            'id' => Rest::PARAM_INT,
            'type' => Rest::PARAM_STR,
            'name' => Rest::PARAM_ANY,
            'choice' => Rest::PARAM_ARRAY
        ));

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
                if (!FormulaManager::getInstance()->getProduct(intval($product, 0)))
                {
                    $updater->cancelTransaction();
                    return ClientError::conflict('product ' . intval($product, 0) . ' does not exist');
                }
                
                $updater->addProductToChoice($choice_id, intval($product, 0));
            }
            
            $updater->confirmTransaction();
        } catch (\Error | \Exception $e) {
            $updater->cancelTransaction();
            Logger::log($e->__toString());
            return ServerError::internalServerError();
        }
        
        $choice = FormulaManager::getInstance()->getChoice($choice_id);
        
        
        
        if ($choice) if (isset($_REQUEST['noimage'])) {
            $properties = $choice->getProperties();
            foreach ($properties['choice'] as &$product) unset($product['image']);
            return new RestResponse('200', HttpCodes::HTTP_200, $properties);
        } else return new RestResponse('200', HttpCodes::HTTP_200, $choice->getProperties());
        else return ServerError::internalServerError();
    }
    
    private static function patchChoice(Rest $request, int $formula_id, int $choice_id) : RestResponse
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
            Logger::log($e->__toString());
            return ServerError::internalServerError();
        }
        
        return new RestResponse('204', HttpCodes::HTTP_204, null);
    }
    
    private static function deleteChoice(Rest $request, int $formula_id, int $choice_id) : RestResponse
    {
        $request->needPermissions(Perm::CAFET_ADMIN_MANAGE_FORMULAS);
        if (FormulaManager::getInstance()->deleteFormulaChoice($choice_id)) return new RestResponse('204', HttpCodes::HTTP_204, null);
        else return ServerError::internalServerError();
    }
}

