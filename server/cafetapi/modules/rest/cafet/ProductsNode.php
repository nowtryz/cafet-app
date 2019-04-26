<?php
namespace cafetapi\modules\rest\cafet;

use cafetapi\Logger;
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
class ProductsNode implements RestNode
{
    const REPLENISHMENTS = 'replenishments ';

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
                    if (!count($request->getPath())) return self::product($request, intval($dir, 0));
                    else {
                        $subdir = $request->shiftPath();
                        if ($subdir == self::REPLENISHMENTS) return self::productReplenishments($request, intval($dir, 0));
                        else return ClientError::resourceNotFound('Unknown ' . $subdir . ' node for a product');
                    }
                }
                
                else return ClientError::resourceNotFound('Unknown cafet/products/' . $dir . ' node');
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
        $request->needPermissions(Perm::CAFET_ADMIN_GET_PRODUCTS);
        
        $products = array();
        foreach (ProductManager::getInstance()->getProducts(isset($_REQUEST['hidden'])) as $product){
            $properties = $product->getProperties();
            if (isset($_REQUEST['noimage'])) unset($properties['image']);
            $products[] = $properties;
        }
        return new RestResponse('200', HttpCodes::HTTP_200, $products);
    }
    
    private static function new(Rest $request) : RestResponse
    {
        $request->needPermissions(Perm::CAFET_ADMIN_MANAGE_PRODUCTS);
        
        //body checks
        $request->checkBody([
            'group' => Rest::PARAM_INT,
            'name' => Rest::PARAM_ANY,
            'price' => Rest::PARAM_SCALAR,
            'viewable' => Rest::PARAM_BOOL
        ]);

        $name = $request->getBody()['name'];
        $group = intval($request->getBody()['group'], 0);
        $price = floatval($request->getBody()['price']);
        $visibility = boolval($request->getBody()['viewable']);
        
        if (!ProductManager::getInstance()->getProductGroup($group)) return ClientError::conflict('group ' . $group . ' does not exist', [
            'on' => 'group',
            'problem' => 'not found'
        ]);
        
        $updater = ProductManager::getInstance();
        $updater->createTransaction();
        $product = null;
        
        try {
            $p = $updater->addProduct($name, $group);
            
            $updater->setProductPrice($p->getId(), $price);
            $updater->setProductViewable($p->getId(), $visibility);
            
            $product = $updater->getProduct($p->getId());
            
            $updater->confirmTransaction();
        } catch (\Error | \Exception $e) {
            $updater->cancelTransaction();
            Logger::log($e->__toString());
            return ServerError::internalServerError();
        }
        
        if ($product) return new RestResponse('201', HttpCodes::HTTP_201, $product->getProperties());
        else return ServerError::internalServerError();
    }
    
    private static function product(Rest $request, int $id) : RestResponse
    {
        $request->allowMethods('GET','PUT','PATCH','DELETE');
        
        switch ($request->getMethod())
        {
            case 'GET':    return self::getProduct($request, $id);
            case 'PUT':    return self::putProduct($request, $id);
            case 'PATCH':  return self::patchProduct($request, $id);
            case 'DELETE': return self::deleteProduct($request, $id);
        }
    }
    
    private static function productReplenishments(Rest $request, int $id) : RestResponse
    {
        $request->allowMethods('GET');
        $request->needPermissions(Perm::CAFET_ADMIN_GET_PRODUCTS);
        
        return ClientError::imATeapot();
    }
    
    
    private static function getProduct(Rest $request, int $id) : RestResponse
    {
        $request->needPermissions(Perm::CAFET_ADMIN_GET_PRODUCTS);
        
        $product = ProductManager::getInstance()->getProduct($id);
        
        if (!$product) return ClientError::resourceNotFound('Unknown product with id ' . $id);
        
        $properties = $product->getProperties();
        if (isset($_REQUEST['noimage'])) unset($properties['image']);
        return new RestResponse('200', HttpCodes::HTTP_200, $properties);
    }
    
    private static function putProduct(Rest $request, int $id) : RestResponse
    {
        $request->needPermissions(Perm::CAFET_ADMIN_MANAGE_PRODUCTS);
        
        $product = ProductManager::getInstance()->getProduct($id);
        if (!$product) return ClientError::resourceNotFound('Unknown product with id ' . $id);
        
        //body checks
        $request->checkBody([
            'id' => Rest::PARAM_INT,
            'type' => Rest::PARAM_STR,
            'group' => Rest::PARAM_INT,
            'name' => Rest::PARAM_ANY,
            'price' => Rest::PARAM_SCALAR,
            'viewable' => Rest::PARAM_BOOL
        ]);

        if ($product->getId() != intval($request->getBody()['id']))        return ClientError::conflict('different id');
        if (get_simple_classname($product) != $request->getBody()['type']) return ClientError::conflict('different type');
        
        $name = $request->getBody()['name'];
        $group = intval($request->getBody()['group'], 0);
        $price = floatval($request->getBody()['price']);
        $visibility = boolval($request->getBody()['viewable']);
        
        if (!ProductManager::getInstance()->getProductGroup($group)) return ClientError::conflict('group ' . $group . ' does not exist');
        
        $updater = ProductManager::getInstance();
        $updater->createTransaction();
        
        try {
            $updater->setProductGroup($id, $group);
            $updater->setProductInformation($id, $name, $price);
            $updater->setProductViewable($id, $visibility);
            
            $updater->confirmTransaction();
        } catch (\Error | \Exception $e) {
            $updater->cancelTransaction();
            Logger::log($e->__toString());
            return ServerError::internalServerError();
        }
        
        $product = $updater->getProduct($id);
        
        return new RestResponse('200', HttpCodes::HTTP_200, $product->getProperties());
    }
    
    private static function patchProduct(Rest $request, int $id) : RestResponse
    {
        $request->needPermissions(Perm::CAFET_ADMIN_MANAGE_PRODUCTS);
        
        if (!ProductManager::getInstance()->getProduct($id)) return ClientError::resourceNotFound('Unknown product with id ' . $id);
        
        $updater = ProductManager::getInstance();
        $updater->createTransaction();
        
        try {
            foreach ($request->getBody() as $field => $value) switch ($field)
            {
                case 'name':
                    $updater->setProductName($id, strval($value));
                    break;
                    
                case 'image':
                    if (!is_string($value))
                    {
                        $updater->cancelTransaction();
                        return ClientError::badRequest('Expected `image` field to be a string representing the image as base64', [
                            'image' => 'string'
                        ]);
                    }
                    
                    $updater->setProductImage($id, $value);
                    break;
                    
                case 'group':
                    if(!intval($value, 0))
                    {
                        $updater->cancelTransaction();
                        return ClientError::badRequest('Expected `group` field to be an integer', [
                            'group' => 'integer'
                        ]);
                    }
                    
                    $updater->setProductGroup($id, intval($value, 0));
                    break;
                    
                case 'price':
                    if (!is_scalar($value))
                    {
                        $updater->cancelTransaction();
                        return ClientError::badRequest('Expected `price` field to be a float', [
                            'price' => 'float'
                        ]);
                    }
                    
                    $updater->setProductPrice($id, floatval($value));
                    break;
                    
                case 'viewable':
                    if (!is_bool($value))
                    {
                        $updater->cancelTransaction();
                        return ClientError::badRequest('Expected `viewable` field to be a boolean', [
                            'viewable' => 'boolean'
                        ]);
                    }
                    
                    $updater->setProductViewable($id, boolval($value));
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
    
    private static function deleteProduct(Rest $request, int $id) : RestResponse
    {
        $request->needPermissions(Perm::CAFET_ADMIN_MANAGE_PRODUCTS);
        
        if (!ProductManager::getInstance()->getProduct($id)) return ClientError::resourceNotFound('Unknown product with id ' . $id);
        
        if (ProductManager::getInstance()->deleteProduct($id)) return new RestResponse('204', HttpCodes::HTTP_204, null);
        else return ServerError::internalServerError();
    }
}

