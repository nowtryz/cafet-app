<?php
namespace cafetapi\modules\rest\cafet;

use cafetapi\io\DataFetcher;
use cafetapi\io\DataUpdater;
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
    const NEW = 'new';
    
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
            case self::NEW: return self::new($request);
            case null: return self::list($request);
            
            default:
                if (intval($dir, 0)) {
                    if (!count($request->getPath())) return self::product($request, intval($dir, 0));
                    else {
                        $subdir = $request->shiftPath();
                        if ($subdir == self::REPLENISHMENTS) return self::productReplenishments($request, intval($dir, 0));
                        else return ClientError::resourceNotFound('Unknown ' . $subdir . ' node for a product');
                    }
                }
                
                else return ClientError::resourceNotFound('Unknown cafet/product/' . $dir . ' node');
        }
    }
    
    private static function list(Rest $request) : RestResponse
    {
        $request->allowMethods(array('GET'));
        $request->needPermissions(array(Perm::CAFET_ADMIN_GET_PRODUCTS));
        
        $products = array();
        foreach (DataFetcher::getInstance()->getProducts(isset($_REQUEST['hidden'])) as $product){
            $properties = $product->getProperties();
            if (isset($_REQUEST['noimage'])) unset($properties['image']);
            $products[] = $properties;
        }
        return new RestResponse('200', HttpCodes::HTTP_200, $products);
    }
    
    private static function new(Rest $request) : RestResponse
    {
        $request->allowMethods(array('POST'));
        $request->needPermissions(array(Perm::CAFET_ADMIN_MANAGE_PRODUCTS));
        
        //body checks
        if (!$request->getBody())                      return ClientError::badRequest('Empty body');
        if (!isset($request->getBody()['group']))      return ClientError::badRequest('Missing `group` field');
        if (!isset($request->getBody()['name']))       return ClientError::badRequest('Missing `name` field');
        if (!isset($request->getBody()['price']))      return ClientError::badRequest('Missing `price` field');
        if (!isset($request->getBody()['viewable']))   return ClientError::badRequest('Missing `viewable` field');
        if (!intval($request->getBody()['group'], 0))  return ClientError::badRequest('Expected `group` field to be an integer');
        if (!is_scalar($request->getBody()['price']))  return ClientError::badRequest('Expected `price` field to be a float');
        if (!is_bool($request->getBody()['viewable'])) return ClientError::badRequest('Expected `viewable` field to be a boolean');
        
        $name = $request->getBody()['name'];
        $group = intval($request->getBody()['group'], 0);
        $price = floatval($request->getBody()['price']);
        $visibility = boolval($request->getBody()['viewable']);
        
        if (!DataFetcher::getInstance()->getProductGroup($group)) ClientError::conflict('group ' . $group . ' does not exist');
        
        $updater = DataUpdater::getInstance();
        $updater->createTransaction();
        $product = null;
        
        try {
            $p = $updater->addProduct($name, $group);
            
            $updater->setProductPrice($p->getId(), $price);
            $updater->setProductViewable($p->getId(), $visibility);
            
            $product = DataFetcher::getInstance()->getProduct($p->getId());
            
            $updater->confirmTransaction();
        } catch (\Error | \Exception $e) {
            $updater->cancelTransaction();
            cafet_log($e->__toString());
            return ServerError::internalServerError();
        }
        
        if ($product) return new RestResponse('201', HttpCodes::HTTP_201, $product->getProperties());
        else return ServerError::internalServerError();
    }
    
    private static function product(Rest $request, int $id) : RestResponse
    {
        $request->allowMethods(array('GET','PUT','PATCH','DELETE'));
        
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
        $request->allowMethods(array('GET'));
        $request->needPermissions(array(Perm::CAFET_ADMIN_GET_PRODUCTS));
        
        return ClientError::imATeapot();
    }
    
    
    private static function getProduct(Rest $request, int $id) : RestResponse
    {
        $request->needPermissions(array(Perm::CAFET_ADMIN_GET_PRODUCTS));
        
        $product = DataFetcher::getInstance()->getProduct($id);
        
        if (!$product) return ClientError::resourceNotFound('Unknown product with id ' . $id);
        
        $properties = $product->getProperties();
        if (isset($_REQUEST['noimage'])) unset($properties['image']);
        return new RestResponse('200', HttpCodes::HTTP_200, $properties);
    }
    
    private static function putProduct(Rest $request, int $id) : RestResponse
    {
        $request->needPermissions(array(Perm::CAFET_ADMIN_MANAGE_PRODUCTS));
        
        $product = DataFetcher::getInstance()->getProduct($id);
        if (!$product) return ClientError::resourceNotFound('Unknown product with id ' . $id);
        
        //body checks
        if (!$request->getBody())                      return ClientError::badRequest('Empty body');
        if (!isset($request->getBody()['id']))         return ClientError::badRequest('Missing `id` field');
        if (!isset($request->getBody()['type']))       return ClientError::badRequest('Missing `type` field');
        if (!isset($request->getBody()['name']))       return ClientError::badRequest('Missing `name` field');
        if (!isset($request->getBody()['group']))      return ClientError::badRequest('Missing `group` field');
        if (!isset($request->getBody()['price']))      return ClientError::badRequest('Missing `price` field');
        if (!isset($request->getBody()['viewable']))   return ClientError::badRequest('Missing `viewable` field');
        if (!intval($request->getBody()['id'], 0))     return ClientError::badRequest('Expected `id` field to be an integer');
        if (!intval($request->getBody()['group'], 0))  return ClientError::badRequest('Expected `group` field to be an integer');
        if (!is_scalar($request->getBody()['price']))  return ClientError::badRequest('Expected `price` field to be a float');
        if (!is_bool($request->getBody()['viewable'])) return ClientError::badRequest('Expected `viewable` field to be a boolean');
        
        if ($product->getId() != intval($request->getBody()['id']))        return ClientError::conflict('different id');
        if (get_simple_classname($product) != $request->getBody()['type']) return ClientError::conflict('different type');
        
        $name = $request->getBody()['name'];
        $group = intval($request->getBody()['group'], 0);
        $price = floatval($request->getBody()['price']);
        $visibility = boolval($request->getBody()['viewable']);
        
        if (!DataFetcher::getInstance()->getProductGroup($group)) ClientError::conflict('group ' . $group . ' does not exist');
        
        $updater = DataUpdater::getInstance();
        $updater->createTransaction();
        
        try {
            $updater->setProductGroup($id, $group);
            $updater->setProductInformation($id, $name, $price);
            $updater->setProductViewable($id, $visibility);
            
            $updater->confirmTransaction();
        } catch (\Error | \Exception $e) {
            $updater->cancelTransaction();
            cafet_log($e->__toString());
            return ServerError::internalServerError();
        }
        
        $product = DataFetcher::getInstance()->getProduct($id);
        
        return new RestResponse('200', HttpCodes::HTTP_200, $product->getProperties());
    }
    
    private static function patchProduct(Rest $request, int $id) : RestResponse
    {
        $request->needPermissions(array(Perm::CAFET_ADMIN_MANAGE_PRODUCTS));
        
        if (!DataFetcher::getInstance()->getProduct($id)) return ClientError::resourceNotFound('Unknown product with id ' . $id);
        
        $updater = DataUpdater::getInstance();
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
                        return ClientError::badRequest('Expected `image` field to be a string representing the image as base64');
                    }
                    
                    $updater->setProductImage($id, $value);
                    break;
                    
                case 'group':
                    if(!intval($value, 0))
                    {
                        $updater->cancelTransaction();
                        return ClientError::badRequest('Expected `group` field to be an integer');
                    }
                    
                    $updater->setProductGroup($id, intval($value, 0));
                    break;
                    
                case 'price':
                    if (!is_scalar($value))
                    {
                        $updater->cancelTransaction();
                        return ClientError::badRequest('Expected `price` field to be a float');
                    }
                    
                    $updater->setProductPrice($id, floatval($value));
                    break;
                    
                case 'viewable':
                    if (!is_bool($value))
                    {
                        $updater->cancelTransaction();
                        return ClientError::badRequest('Expected `viewable` field to be a boolean');
                    }
                    
                    $updater->setProductViewable($id, boolval($value));
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
    
    private static function deleteProduct(Rest $request, int $id) : RestResponse
    {
        $request->needPermissions(array(Perm::CAFET_ADMIN_MANAGE_PRODUCTS));
        
        if (!DataFetcher::getInstance()->getProduct($id)) return ClientError::resourceNotFound('Unknown product with id ' . $id);
        
        if (DataUpdater::getInstance()->deleteProduct($id)) return new RestResponse('204', HttpCodes::HTTP_204, null);
        else return ServerError::internalServerError();
    }
}

