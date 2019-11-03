<?php
/**
 * Function file for authentication functions
 *
 * @package cafetapi
 * @since API 1.0
 */

use cafetapi\Logger;
use cafetapi\user\User;
use cafetapi\io\UserManager;
use cafetapi\config\Config;

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
     * @since API 0.1.0 (2018)
     */
    function cafet_init_session(bool $no_cookie = false, string $session_id = null): string
    {
        // Check if a session was already created and save it in case
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_commit();
            session_unset();
        }

        // Set session name and cookie name
        session_name(Config::session_name);

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
     * @since API 0.1.0 (2018)
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
     * Return the hash version of a password according to the hash algorithm specified in the conjurations
     *
     * @param string $password
     *            the password to hash
     * @return string the hash version of the password
     * @throws Exception
     * @since API 0.1.0 (2018)
     */
    function cafet_generate_hashed_pwd(string $password): string
    {
        $salt = base64_encode(random_bytes(32));
        $algo = in_array(Config::hash_algo, hash_algos()) ? Config::hash_algo : 'sha256';

        return $algo . '.' . $salt . '.' . cafet_digest($algo, $salt, $password);
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
     * @since API 0.1.0 (2018)
     */
    function cafet_verify_password(string $password, string $hash, string $pseudo = null): bool
    {
        Logger::log("PWD comparison: db=$hash; got=" . $hash));
        if($hash == '') return false;

        $hash_info = explode('.', $hash);

        if (count($hash_info) == 1 && isset($pseudo)) {
            return sha1(Config::salt . $password . $pseudo) === $hash;
        }

        if (count($hash_info) < 3) {
            throw new InvalidArgumentException('Wrong password hash format');
        }

        [$algo, $salt, $hashed] = $hash_info;

        Logger::log("PWD comparison, hashes: db=$hashed; got=" . cafet_digest($algo, $salt, $password));

        return cafet_digest($algo, $salt, $password) === $hashed;
    }

    function cafet_digest(string $algo, string $salt, string $password) : string
    {
        $hash1 = base64_encode(hash($algo, $salt . $password, true));
        $hash2 = base64_encode(hash($algo, $password . $salt, true));
        return base64_encode(hash($algo, $hash1 . $salt . $password . $hash2, true));
    }

    /**
     * Checks given login information
     *
     * @param string $pseudo_or_name
     *            the pseudo or the email entered
     * @param string $password
     *            the password entered
     * @return User a User object if logins are correct, false on failure
     * @since API 0.1.0 (2018)
     */
    function cafet_check_login(string $pseudo_or_name, $password): ?User
    {
        $user = UserManager::getInstance()->getUser($pseudo_or_name);

        if (! $user) return NULL;

        if (cafet_verify_password($password, $user->getHash(), $user->getPseudo())) {
            UserManager::getInstance()->registerLogin($user->getId());
            return $user;
        }
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
