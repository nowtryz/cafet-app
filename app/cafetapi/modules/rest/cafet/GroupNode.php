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
use function cafetapi\modules\rest\errors\ClientError\resourceNotFound;
use cafetapi\user\Perm;

/**
 *
 * @author damie
 *        
 */
class GroupNode implements RestNode
{
    const LIST = 'list';
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
            case self::LIST: return self::list($request);
            case self::NEW: return self::new($request);
            
            case null: return ClientError::forbidden();
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
        if($request->getMethod() !== 'GET') return ClientError::methodNotAllowed($request->getMethod(), array('GET'));
        if(!$request->isClientAbleTo(Perm::CAFET_ADMIN_GET_PRODUCTS)) return ClientError::forbidden();
        
        $groups = array();
        foreach (DataFetcher::getInstance()->getProductGroups() as $group) $groups[] = $group->getProperties();
        return new RestResponse('200', HttpCodes::HTTP_200, $groups);
    }
    
    private static function new(Rest $request) : RestResponse
    {
        if($request->getMethod() !== 'POST') return ClientError::methodNotAllowed($request->getMethod(), array('POST'));
        if(!$request->isClientAbleTo(Perm::CAFET_ADMIN_MANAGE_PRODUCTS)) return ClientError::forbidden();
        
        //body checks
        if(!$request->getBody())                return ClientError::badRequest('Empty body');
        if(!isset($request->getBody()['name'])) return ClientError::badRequest('Missing `name` field');
//         if(!isset($request->getBody()['displayName'])) return ClientError::badRequest('Missing `displayName` field');
        
        $name = $request->getBody()['name'];
//         $displayName = $request->getBody()['displayName'];
        
        $group = DataUpdater::getInstance()->addProductGroup($name);
        if($group) return new RestResponse('201', HttpCodes::HTTP_201, $group->getProperties());
        else return ServerError::internalServerError();
    }
    
    private static function group(Rest $request, int $id) : RestResponse
    {
        $request->allowMethods(array('GET','PUT','PATCH','DELETE'));
        
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
        if($request->getMethod() !== 'GET') return ClientError::methodNotAllowed($request->getMethod(), array('GET'));
        if(!$request->isClientAbleTo(Perm::CAFET_ADMIN_GET_PRODUCTS)) return ClientError::forbidden();
        
        $products = array();
        foreach (DataFetcher::getInstance()->getGroupProducts($id, isset($_REQUEST['hidden'])) as $product) $products[] = $product->getProperties();
        if($products) return new RestResponse('200', HttpCodes::HTTP_200, $products);
        elseif (DataFetcher::getInstance()->getProductGroup($id)) return new RestResponse('200', HttpCodes::HTTP_200, array());
        else return ClientError::resourceNotFound('Unknown group with id ' . $id);
    }
    
    
    private static function getGroup(Rest $request, int $id) : RestResponse
    {
        $request->needPermissions(array(Perm::CAFET_ADMIN_GET_PRODUCTS));
        
        $group = DataFetcher::getInstance()->getProductGroup($id);
        if($group) return new RestResponse('200', HttpCodes::HTTP_200, $group->getProperties());
        else return ClientError::resourceNotFound('Unknown group with id ' . $id);
    }
    
    private static function putGroup(Rest $request, int $id) : RestResponse
    {
        $request->needPermissions(array(Perm::CAFET_ADMIN_MANAGE_PRODUCTS));
        
        $group = DataFetcher::getInstance()->getProductGroup($id);
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
        
        $updater = DataUpdater::getInstance();
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
        
        $group = DataFetcher::getInstance()->getProductGroup($id);
        
        return new RestResponse('200', HttpCodes::HTTP_200, $group->getProperties());
    }
    
    private static function patchGroup(Rest $request, int $id) : RestResponse
    {
        $request->needPermissions(array(Perm::CAFET_ADMIN_MANAGE_PRODUCTS));
        
        if (!DataFetcher::getInstance()->getProductGroup($id)) return ClientError::resourceNotFound('Unknown group with id ' . $id);
        
        $updater = DataUpdater::getInstance();
        $updater->createTransaction();
        
        try {
            foreach ($request->getBody() as $field => $value) switch ($field)
            {
                case 'name':
                    $updater->setProductGroupName($id, $value);
                    break;
                case 'displayName':
                    $updater->setProductGroupDisplayName($id, $value);
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
        $request->needPermissions(array(Perm::CAFET_ADMIN_MANAGE_PRODUCTS));
        
        if (!DataFetcher::getInstance()->getProductGroup($id)) return ClientError::resourceNotFound('Unknown group with id ' . $id);
        
        if (DataUpdater::getInstance()->deleteProductGroup($id)) return new RestResponse('204', HttpCodes::HTTP_204, null);
        else return ServerError::internalServerError();
    }
}

