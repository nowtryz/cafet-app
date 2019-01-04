<?php
namespace cafetapi\modules\rest\cafet;

use cafetapi\modules\rest\HttpCodes;
use cafetapi\modules\rest\Rest;
use cafetapi\modules\rest\RestNode;
use cafetapi\modules\rest\RestResponse;
use cafetapi\modules\rest\errors\ClientError;
use cafetapi\modules\rest\errors\ServerError;
use function cafetapi\modules\rest\errors\ClientError\resourceNotFound;
use cafetapi\user\Perm;
use cafetapi\io\ProductManager;

/**
 *
 * @author damie
 *        
 */
class GroupsNode implements RestNode
{
    const NEW = 'new';
    
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
            case self::NEW: return self::new($request);
            case null: return self::list($request);
            
            default:
                if (intval($dir, 0)) {
                    if (!count($request->getPath())) return self::group($request, intval($dir, 0));
                    else {
                        $subdir = $request->shiftPath();
                        if ($subdir == self::PRODUCTS) return self::groupProducts($request, intval($dir, 0));
                        else return ClientError::resourceNotFound('Unknown ' . $subdir . ' node for a group');
                    }
                }
                
                else return ClientError::resourceNotFound('Unknown cafet/group/' . $dir . ' node');
        }
    }
    
    private static function list(Rest $request) : RestResponse
    {
        $request->allowMethods('GET');
        $request->needPermissions(Perm::CAFET_ADMIN_GET_PRODUCTS);
        
        $groups = array();
        foreach (ProductManager::getInstance()->getProductGroups() as $group) $groups[] = $group->getProperties();
        return new RestResponse('200', HttpCodes::HTTP_200, $groups);
    }
    
    private static function new(Rest $request) : RestResponse
    {
        $request->allowMethods('POST');
        $request->needPermissions(Perm::CAFET_ADMIN_MANAGE_PRODUCTS);
        
        //body checks
        if(!$request->getBody())                return ClientError::badRequest('Empty body');
        if(!isset($request->getBody()['name'])) return ClientError::badRequest('Missing `name` field');
//         if(!isset($request->getBody()['displayName'])) return ClientError::badRequest('Missing `displayName` field');
        
        $name = $request->getBody()['name'];
//         $displayName = $request->getBody()['displayName'];
        
        $group = ProductManager::getInstance()->addProductGroup($name);
        if($group) return new RestResponse('201', HttpCodes::HTTP_201, $group->getProperties());
        else return ServerError::internalServerError();
    }
    
    private static function group(Rest $request, int $id) : RestResponse
    {
        $request->allowMethods('GET','PUT','PATCH','DELETE');
        
        switch ($request->getMethod())
        {
            case 'GET':    return self::getGroup($request, $id);
            case 'PUT':    return self::putGroup($request, $id);
            case 'PATCH':  return self::patchGroup($request, $id);
            case 'DELETE': return self::deleteGroup($request, $id);
        }
    }
    
    private static function groupProducts(Rest $request, int $id) : RestResponse
    {
        $request->allowMethods('GET');
        $request->needPermissions(Perm::CAFET_ADMIN_GET_PRODUCTS);
        
        $products = array();
        foreach (ProductManager::getInstance()->getGroupProducts($id, isset($_REQUEST['hidden'])) as $product)
        {
            $properties = $product->getProperties();
            if (isset($_REQUEST['noimage'])) unset($properties['image']);
            $products[] = $properties;
        }
        if($products) return new RestResponse('200', HttpCodes::HTTP_200, $products);
        elseif (ProductManager::getInstance()->getProductGroup($id)) return new RestResponse('200', HttpCodes::HTTP_200, array());
        else return ClientError::resourceNotFound('Unknown group with id ' . $id);
    }
    
    
    private static function getGroup(Rest $request, int $id) : RestResponse
    {
        $request->needPermissions(Perm::CAFET_ADMIN_GET_PRODUCTS);
        
        $group = ProductManager::getInstance()->getProductGroup($id);
        if($group) return new RestResponse('200', HttpCodes::HTTP_200, $group->getProperties());
        else return ClientError::resourceNotFound('Unknown group with id ' . $id);
    }
    
    private static function putGroup(Rest $request, int $id) : RestResponse
    {
        $request->needPermissions(Perm::CAFET_ADMIN_MANAGE_PRODUCTS);
        
        $group = ProductManager::getInstance()->getProductGroup($id);
        if (!$group) return ClientError::resourceNotFound('Unknown group with id ' . $id);
        
        //body checks
        if(!$request->getBody())                       return ClientError::badRequest('Empty body');
        if(!isset($request->getBody()['id']))          return ClientError::badRequest('Missing `id` field');
        if(!isset($request->getBody()['type']))        return ClientError::badRequest('Missing `type` field');
        if(!isset($request->getBody()['name']))        return ClientError::badRequest('Missing `name` field');
        if(!isset($request->getBody()['displayName'])) return ClientError::badRequest('Missing `displayName` field');
        if(!intval($request->getBody()['id'], 0))      return ClientError::badRequest('Expected `id` field to be an integer');
        
        if ($group->getId() != intval($request->getBody()['id']))        return ClientError::conflict('different id');
        if (get_simple_classname($group) != $request->getBody()['type']) return ClientError::conflict('different type');
        
        $name = $request->getBody()['name'];
        $displayName = $request->getBody()['displayName'];
        
        $updater = ProductManager::getInstance();
        $updater->createTransaction();
        
        try {
            $updater->setProductGroupName($id, $name);
            $updater->setProductGroupDisplayName($id, $displayName);
            
            $updater->confirmTransaction();
        } catch (\Error | \Exception $e) {
            $updater->cancelTransaction();
            cafet_log($e->__toString());
            return ServerError::internalServerError();
        }
        
        $group = $updater->getProductGroup($id);
        
        return new RestResponse('200', HttpCodes::HTTP_200, $group->getProperties());
    }
    
    private static function patchGroup(Rest $request, int $id) : RestResponse
    {
        $request->needPermissions(Perm::CAFET_ADMIN_MANAGE_PRODUCTS);
        
        if (!ProductManager::getInstance()->getProductGroup($id)) return ClientError::resourceNotFound('Unknown group with id ' . $id);
        
        $updater = ProductManager::getInstance();
        $updater->createTransaction();
        
        try {
            foreach ($request->getBody() as $field => $value) switch ($field)
            {
                case 'name':
                    $updater->setProductGroupName($id, strval($value));
                    break;
                case 'displayName':
                    $updater->setProductGroupDisplayName($id, strval($value));
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
    
    private static function deleteGroup(Rest $request, int $id) : RestResponse
    {
        $request->needPermissions(Perm::CAFET_ADMIN_MANAGE_PRODUCTS);
        
        if (!ProductManager::getInstance()->getProductGroup($id)) return ClientError::resourceNotFound('Unknown group with id ' . $id);
        
        if (ProductManager::getInstance()->deleteProductGroup($id)) return new RestResponse('204', HttpCodes::HTTP_204, null);
        else return ServerError::internalServerError();
    }
}

