<?php
namespace cafetapi\modules\rest\server;

use cafetapi\Logger;
use cafetapi\io\ClientManager;
use cafetapi\io\UserManager;
use cafetapi\modules\rest\HttpCodes;
use cafetapi\modules\rest\Rest;
use cafetapi\modules\rest\RestNode;
use cafetapi\modules\rest\RestResponse;
use cafetapi\modules\rest\errors\ClientError;
use cafetapi\modules\rest\errors\ServerError;
use cafetapi\user\Group;
use cafetapi\user\Perm;

/**
 *
 * @author damie
 *
 */
class UsersNode implements RestNode
{
    const CREATE_CUSTOMER = 'create-customer';

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
                    if ($request->shiftPath() == self::CREATE_CUSTOMER) return self::createCustomer($request, intval($dir, 0));
                    return self::user($request, intval($dir, 0));
                }
                else return ClientError::resourceNotFound('Unknown server/user/' . $dir . ' node');
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
        $request->needPermissions(Perm::SITE_GET_USERS);

        $users = array();
        foreach (UserManager::getInstance()->getUsers() as $user) $users[] = $user->getProperties();
        return new RestResponse('200', HttpCodes::HTTP_200, $users);
    }

    private static function new(Rest $request) : RestResponse
    {
        $request->needPermissions(Perm::SITE_MANAGE_USERS);
        $request->checkBody(array(
            'pseudo' => Rest::PARAM_STR,
            'email' => Rest::PARAM_STR,
            'password' => Rest::PARAM_STR,
            'firstname' => Rest::PARAM_STR,
            'name' => Rest::PARAM_STR,
            'group' => Rest::PARAM_INT
        ));

        $username = $request->getBody()['pseudo'];
        $password = $request->getBody()['password'];
        $firstname = $request->getBody()['firstname'];
        $name = $request->getBody()['name'];
        $email = $request->getBody()['email'];
        $group = intval($request->getBody()['group'], 0);

        $manager = UserManager::getInstance();
        $conflicts = array();

        if (!array_key_exists($group, Group::GROUPS)) $conflicts['group'] = Rest::CONFLICT_NOT_VALID;
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $conflicts['email'] = Rest::CONFLICT_NOT_VALID;
        if ($manager->getUser($username)) $conflicts['pseudo'] = Rest::CONFLICT_DUPLICATED;
        if ($manager->getUser($email)) $conflicts['email'] = Rest::CONFLICT_DUPLICATED;

        if($conflicts) return ClientError::conflict(null, $conflicts);
        unset($conflicts);

        $user = $manager->addUser($username, $email, $firstname, $name, $password, $group);
        if($user) return new RestResponse(201, HttpCodes::HTTP_201, $user->getProperties());
        else return ServerError::internalServerError();
    }

    private static function user(Rest $request, int $id) : RestResponse
    {
        $request->allowMethods('GET', 'PUT', 'PATCH', 'DELETE');
        $request->needLogin();

        switch ($request->getMethod())
        {
            case 'GET' :   return self::get($request, $id);
            case 'PUT':    return self::put($request, $id);
            case 'PATCH':  return self::patch($request, $id);
            case 'DELETE': return self::delete($request, $id);
        }
    }


    private static function get(Rest $request, int $id) : RestResponse
    {
        $request->needPermissions(Perm::SITE_GET_USERS);
        $user = UserManager::getInstance()->getUserById($id);
        if (!$user) return ClientError::resourceNotFound('Unknown user with id ' . $id);
        return new RestResponse('200', HttpCodes::HTTP_200, $user->getProperties());
    }

    private static function put(Rest $request, int $id) : RestResponse
    {
        $request->needPermissions(Perm::SITE_MANAGE_USERS);

        $user = UserManager::getInstance()->getUserById($id);
        if (!$user) return ClientError::resourceNotFound('Unknown user with id ' . $id);

        //body checks
        $request->checkBody(array(
            'id' => Rest::PARAM_INT,
            'type' => Rest::PARAM_STR,
            'pseudo' => Rest::PARAM_STR,
            'password' => Rest::PARAM_STR,
            'firstname' => Rest::PARAM_STR,
            'name' => Rest::PARAM_STR,
            'email' => Rest::PARAM_STR,
            'phone' => Rest::PARAM_ANY,
            'group' => Rest::PARAM_INT
        ));

        $pseudo = $request->getBody()['pseudo'];
        $password = $request->getBody()['password'];
        $firstname = $request->getBody()['firstname'];
        $name = $request->getBody()['name'];
        $email = $request->getBody()['email'];
        $phone = $request->getBody()['phone'];
        $group = intval($request->getBody()['group'], 0);

        $manager = UserManager::getInstance();
        $conflicts = array();

        if ($user->getId() != intval($request->getBody()['id']))        $conflicts['id'] = Rest::CONFLICT_DIFFERENT;
        if (get_simple_classname($user) != $request->getBody()['type']) $conflicts['type'] = Rest::CONFLICT_DIFFERENT;
        if (! array_key_exists($group, Group::GROUPS)) $conflicts['group'] = Rest::CONFLICT_NOT_VALID;
        if ($pseudo != $user->getPseudo() && $manager->getUser($pseudo)) $conflicts['pseudo'] = Rest::CONFLICT_DUPLICATED;
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $conflicts['email'] = Rest::CONFLICT_NOT_VALID;
        if ($email != $user->getEmail() && $manager->getUser($email)) $conflicts['email'] = Rest::CONFLICT_DUPLICATED;

        if($conflicts) return ClientError::conflict(null, $conflicts);
        unset($conflicts);

        $manager->createTransaction();

        try {
            $manager->setPseudo($id, $pseudo);
            $manager->setPassword($id, $password);
            $manager->setFirstname($id, $firstname);
            $manager->setName($id, $name);
            $manager->setEmail($id, $email);
            $manager->setPhone($id, $phone);
            $manager->setGroup($id, $group);

            $manager->confirmTransaction();
        } catch (\Error | \Exception $e) {
            $manager->cancelTransaction();
            Logger::log($e->__toString());
            return ServerError::internalServerError();
        }

        $user = $manager->getUserById($id);

        return new RestResponse('200', HttpCodes::HTTP_200, $user->getProperties());
    }

    private static function patch(Rest $request, int $id) : RestResponse
    {
        $request->needPermissions(Perm::SITE_MANAGE_USERS);

        $manager = UserManager::getInstance();
        $user = $manager->getUserById($id);

        if (!$user) {
            return ClientError::resourceNotFound('Unknown user with id ' . $id);
        }

        $manager->createTransaction();
        $conflicts = array();

        try {
            foreach ($request->getBody() as $field => $value) switch ($field)
            {
                case 'pseudo':
                    if ($value == $user->getPseudo()) break;
                    elseif ($manager->getUser($value)) $conflicts[$field] = Rest::CONFLICT_DUPLICATED;
                    else $manager->setPseudo($id, strval($value));
                    break;

                case 'email':
                    if($value == $user->getEmail()) break;
                    elseif (!filter_var($value, FILTER_VALIDATE_EMAIL)) $conflicts[$field] = Rest::CONFLICT_NOT_VALID;
                    elseif($manager->getUser($value)) $conflicts[$field] = Rest::CONFLICT_DUPLICATED;
                    else $manager->setEmail($id, strval($value));
                    break;

                case 'firstName':
                    if($value == $user->getFirstName()) break;
                    $manager->setFirstname($id, strval($value));
                    break;

                case 'familyName':
                    if($value == $user->getFamilyName()) break;
                    $manager->setName($id, strval($value));
                    break;

                case 'phone':
                    if($value == $user->getPhone()) break;
                    $manager->setPhone($id, strval($value));
                    break;

                case 'password':
                    $manager->setPassword($id, strval($value));
                    break;

                case 'group':
                    if(!intval($value, 0))
                    {
                        $manager->cancelTransaction();
                        return ClientError::badRequest('Expected `group` field to be an integer');
                    }

                    $manager->setGroup($id, intval($value, 0));
                    break;
            }

            if($conflicts)
            {
                $manager->cancelTransaction();
                return ClientError::conflict(null, $conflicts);
            }

            $manager->confirmTransaction();
        } catch (\Error | \Exception $e) {
            $manager->cancelTransaction();
            Logger::log($e->__toString());
            return ServerError::internalServerError();
        }

        return new RestResponse('204', HttpCodes::HTTP_204, null);
    }

    private static function delete(Rest $request, int $id) : RestResponse
    {
        $request->needPermissions(Perm::SITE_MANAGE_USERS);

        if (!UserManager::getInstance()->getUserById($id)) return ClientError::resourceNotFound('Unknown user with id ' . $id);

        if (UserManager::getInstance()->deleteUser($id)) return new RestResponse('204', HttpCodes::HTTP_204, null);
        else return ServerError::internalServerError();
    }


    private static function createCustomer(Rest $request, int $user_id) : RestResponse
    {
        $request->allowMethods('POST');
        $request->needPermissions(Perm::CAFET_ADMIN_MANAGE_CLIENTS);

        ClientManager::getInstance()->createCustomer($user_id);

        return new RestResponse(201, HttpCodes::HTTP_201, null, [
            'Location' => $request->getRoot_url() . '/api/v' . $request->getVersion() . '/cafet/clients/' . $user_id
        ]);
    }
}

