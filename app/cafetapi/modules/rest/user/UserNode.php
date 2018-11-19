<?php
namespace cafetapi\modules\rest\user;

use cafetapi\modules\rest\HttpCodes;
use cafetapi\modules\rest\Rest;
use cafetapi\modules\rest\RestNode;
use cafetapi\modules\rest\RestResponse;
use cafetapi\modules\rest\errors\ClientError;

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
                return ClientError::badRequest('Authorization failed');
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
        
        
        $headers = array(
            'Session' => $session
        );
        
        if (isset($_REQUEST['after'])) $headers['Location'] = urldecode($_REQUEST['after']);
        
        return new RestResponse(200, HttpCodes::HTTP_200, array(
            'message' => 'Logged as ' . $user->getPseudo(),
            'session' => $session,
            'user' => $user->getProperties(),
            'expiration' => intval(ini_get('session.gc_maxlifetime'))
        ), $headers);
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
        if ($request->getUser()) {
            return new RestResponse(200, HttpCodes::HTTP_200, $request->getUser()->getProperties());
        } else {
            return ClientError::unauthorized();
        }
            
    }
}

