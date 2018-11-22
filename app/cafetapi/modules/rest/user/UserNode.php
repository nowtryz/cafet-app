<?php
namespace cafetapi\modules\rest\user;

use cafetapi\io\DataUpdater;
use cafetapi\io\DatabaseConnection;
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
        if (!empty($_SERVER['PHP_AUTH_USER']))
        {
            $user = cafet_check_login($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
        
            if(!$user) {
                return ClientError::unauthorized(array(), 'Authorization failed');
            }
            
            $session = cafet_init_session();
            cafet_set_logged_user($user);
        }
        elseif ($request->getSession() && $request->getUser())
        {
            $session = $request->getSession();
            $user = $request->getUser();
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
        if (!$request->getUser()) return ClientError::unauthorized();
        
        switch ($request->getMethod())
        {
            case 'GET' : return new RestResponse(200, HttpCodes::HTTP_200, $request->getUser()->getProperties());
        } 
    }
    
    private static function patchCurrent(Rest $request) : RestResponse
    {
        if (!DatabaseConnection::getDatabaseConnectionInstance()->getUser($request->getUser()->getPseudo())) {
            return ClientError::resourceNotFound('Unknown user with pseudo ' . $request->getUser()->getPseudo());
        }
        
        $updater = DataUpdater::getInstance();
        $updater->createTransaction();
        
        $conflict = array();
        
        try {
            foreach ($request->getBody() as $field => $value) switch ($field)
            {
                case 'pseudo':
                    
                    break;
                    
                case 'firstname':
                    
                    break;
                    
                case 'name':
                    
                    break;
                    
                case 'email':
                    
                    break;
                    
                case 'phone':
                    
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
}

