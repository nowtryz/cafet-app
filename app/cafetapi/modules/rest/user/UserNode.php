<?php
namespace cafetapi\modules\rest\user;

use cafetapi\io\UserManager;
use cafetapi\modules\rest\HttpCodes;
use cafetapi\modules\rest\Rest;
use cafetapi\modules\rest\RestNode;
use cafetapi\modules\rest\RestResponse;
use cafetapi\modules\rest\errors\ClientError;
use cafetapi\modules\rest\errors\ServerError;

/**
 *
 * @author damie
 *        
 */
class UserNode implements RestNode
{
    const LOGIN   = 'login';
    const LOGOUT  = 'logout';
    const CURRENT = 'current';

    /**
     * (non-PHPdoc)
     *
     * @see \cafetapi\modules\rest\RestNode::handle()
     */
    public static function handle(Rest $request) : RestResponse
    {
        $dir = $request->shiftPath();
        
        
        switch ($dir) {
            case self::LOGIN:   return self::login($request);
            case self::LOGOUT:  return self::logout($request);
            case self::CURRENT: return self::current($request);
            
            case null: return ClientError::forbidden();
            default:   return ClientError::resourceNotFound('Unknown user/' . $dir . ' node');
        }
    }
    
    private static function login(Rest $request) : RestResponse
    {
        if ($request->getSession() && $request->getUser())
        {
            $session = $request->getSession();
            $user = $request->getUser();
        }
        elseif (!empty($_SERVER['PHP_AUTH_USER']))
        {
            $user = cafet_check_login($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
        
            if(!$user) {
                return ClientError::unauthorized(array(), 'Authorization failed');
            }
            
            $session = cafet_init_session();
            cafet_set_logged_user($user);
        }
        else
        {
            return ClientError::unauthorized();
        }
        
        $result = array(
            'message' => 'Logged as ' . $user->getPseudo(),
            'session' => $session,
            'user' => $user->getProperties(),
            'expiration' => intval(ini_get('session.gc_maxlifetime'))
        );
        
        $header = array(
            'Session' => $session
        );
        
        
        if (isset($_REQUEST['after'])) return new RestResponse(302, HttpCodes::HTTP_302, $result, array_merge($header, array(
            'Location' => urldecode($_REQUEST['after'])
        )));
        else return new RestResponse(200, HttpCodes::HTTP_200, $result, $header);
        
        
    }
    
    private static function logout(Rest $request) : RestResponse
    {
        if (isset($_COOKIE[cafet_get_configuration('session_name')])) {
            $session = cafet_init_session();
            cafet_destroy_session();
        } elseif (isset($request->getHeaders()['Session'])) {
            $session = $request->getHeaders()['Session'];
            cafet_init_session(true, $session);
            cafet_destroy_session($session);
        } else {
            return ClientError::unauthorized();
        }
        
        return new RestResponse(200, HttpCodes::HTTP_200, array(
            'session_destroyed' => $session,
            'message' => cafet_get_configuration('logout_message')
        ));
    }
    
    private static function current(Rest $request) : RestResponse
    {
        $request->allowMethods(array('GET', 'PUT', 'PATCH'));
        $request->needLogin();
        
        switch ($request->getMethod())
        {
            case 'GET' :  return new RestResponse(200, HttpCodes::HTTP_200, $request->getUser()->getProperties());
            case 'PATCH': return self::patchCurrent($request);
            default: return ClientError::imATeapot();
        }
    }
    
    private static function patchCurrent(Rest $request) : RestResponse
    {
        if (!UserManager::getInstance()->getUser($request->getUser()->getPseudo())) {
            return ClientError::resourceNotFound('Unknown user with pseudo ' . $request->getUser()->getPseudo());
        }
        
        $updater = UserManager::getInstance();
        $updater->createTransaction();
        
        $conflicts = array();
        $user = $request->getUser();
        
        try {
            foreach ($request->getBody() as $field => $value) switch ($field)
            {
                case 'pseudo':
                    if($value == $user->getPseudo()) break;
                    elseif($updater->getUser($value)) $conflicts[$field] = 'duplicated';
                    else {
                        $updater->setPseudo($user->getId(), strval($value));
                        $request->getUser()->setPseudo($value);
                    }
                    break;
                    
                case 'email':
                    if($value == $user->getEmail()) break;
                    elseif (!filter_var($value, FILTER_VALIDATE_EMAIL)) $conflicts[$field] = 'not valid';
                    elseif($updater->getUser($value)) $conflicts[$field] = 'duplicated';
                    else {
                        $updater->setEmail($user->getId(), strval($value));
                        $request->getUser()->setEmail($value);
                    }
                    break;
                        
                case 'firstname':
                    if($value == $user->getFirstname()) break;
                    $updater->setFirstname($user->getId(), strval($value));
                    $request->getUser()->setFirstname($value);
                    break;
                    
                case 'name':
                    if($value == $user->getName()) break;
                    $updater->setName($user->getId(), strval($value));
                    $request->getUser()->setName($value);
                    break;
                    
                case 'phone':
                    if($value == $user->getPhone()) break;
                    $updater->setPhone($user->getId(), strval($value));
                    $request->getUser()->setPhone($value);
                    break;
                    
                case 'password':
                    $updater->setPassword($user->getId(), strval($value));
                    break;
            }
            
            if($conflicts)
            {
                $updater->cancelTransaction();
                return ClientError::conflict(null, $conflicts);
            }
            
            $updater->confirmTransaction();
        } catch (\Error | \Exception $e) {
            $updater->cancelTransaction();
            cafet_log($e->__toString());
            return ServerError::internalServerError();
        }
        
        return new RestResponse('204', HttpCodes::HTTP_204, null);
    }
}

