<?php
/**
 * Function file for authentication functions
 *
 * @package cafetapi
 * @since API 1.0
 */

use cafetapi\user\User;

if (!defined('authentication_functions_loaded')) {
    define('authentication_functions_loaded', true);
    
    /**
     * Initialise session
     *
     * @param bool $no_cookie
     *            if session cookie must be disabled
     * @param string $session_id
     *            if specified, the session id is set to the given one
     * @return string
     * @since API 1.0.0 (2018)
     */
    function cafet_init_session(bool $no_cookie = false, string $session_id = null): string
    {
        // Check if a session was already created and save it in case
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_commit();
            session_unset();
        }
        
        // Set session name and cookie name
        session_name(cafet_get_configurations()['session_name']);
        
        // construe arguments
        if (isset($session_id)) session_id($session_id);
        
        if ($no_cookie) ini_set('session.use_cookies', '0');
        
        // Start session
        session_start();
        
        // Check last activity and regenerate the session if timeout was reached
        if (isset($_SESSION['last_activity']) && $_SESSION['last_activity'] < time() - ini_get('session.gc_maxlifetime')) {
            session_unset();
        }
        
        // Save activity timestamp
        $_SESSION['last_activity'] = time();
        
        // Return the session id
        return session_id();
    }
    
    /**
     * Completly destroy the session
     *
     * @param string $session_id
     *            if specified, destroy the given session
     * @since API 1.0.0 (2018)
     */
    function cafet_destroy_session(string $session_id = null)
    {
        if (isset($session_id)) {
            if (session_status() == PHP_SESSION_ACTIVE) {
                $current_session = session_id();
                session_commit();
            }
            
            session_id($session_id);
            session_start();
        }
        
        session_unset();
        session_destroy();
        
        if (ini_get("session.use_cookies") && isset($_COOKIE[session_name()])) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }
        
        if (isset($current_session)) {
            session_id($current_session);
            session_start();
        }
    }
    
    /**
     * Return the hash version of a password according to the hash algorith specified in the congurations
     *
     * @param string $password
     *            the password to hash
     * @param string $pseudo
     *            [optional] the pseudo to hash with the old method
     * @return string the hash version of the password
     * @since API 1.0.0 (2018)
     */
    function cafet_generate_hashed_pwd(string $password, string $pseudo = null): string
    {
        $salt = bin2hex(random_bytes(16));
        $algo = cafet_get_configurations()['hash_algo'];
        
        return $algo . '.' . $salt . '.' . hash($algo, $salt . $password);
    }
    
    /**
     * Verify if a clear password corresponds to its hashed value
     *
     * @param string $password
     *            the password to check
     * @param string $hash
     *            the hashed password
     * @param string $pseudo
     *            [optional] the pseudo, to works with old site
     * @throws InvalidArgumentException if hash doesn't respect its synthax
     * @return bool if password is correct
     * @since API 1.0.0 (2018)
     */
    function cafet_verify_password(string $password, string $hash, string $pseudo = null): bool
    {
        if($hash == '')
            return false;
            
            $hash_info = explode('.', $hash);
            
            if (count($hash_info) == 1 && isset($pseudo)) {
                return sha1(cafet_get_configurations()['salt'] . $password . $pseudo) === $hash;
            }
            
            if (count($hash_info) < 3)
                throw new InvalidArgumentException('Wrong password hash format');
                
                return hash($hash_info[0], $hash_info[1] . $password) === $hash_info[2];
    }
    
    /**
     * Checks given login information
     *
     * @param string $pseudo_or_name
     *            the pseudo or the email entered
     * @param string $password
     *            the password entered
     * @return User a User object if logins are correct, false on failure
     * @since API 1.0.0 (2018)
     */
    function cafet_check_login(string $pseudo_or_name, $password): ?User
    {
        global $DB;
        
        $user = $DB->getUser($pseudo_or_name);
        
        if (! $user) return NULL;
            
        if (cafet_verify_password($password, $user->getHash(), $user->getPseudo())) return $user;
        else return NULL;
    }
    
    /**
     * Register a user for the sarted session
     * @param User $user the user to set
     */
    function cafet_set_logged_user(User $user)
    {
        $_SESSION['user'] = serialize($user);
    }
    
    /**
     * Returns the logged user for the started session
     * @return User|NULL the logged user
     */
    function cafet_get_logged_user(): ?User
    {
        if (!isset($_SESSION['user'])) return null;
        
        $user = (object) unserialize($_SESSION['user']);
        
        if ($user instanceof User) return $user;
        else return null;
    }

}
