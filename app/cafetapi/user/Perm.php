<?php
namespace cafetapi\user;

/**
 *
 * @author Damien
 *        
 */
class Perm
{

    // ALL (*)
    const ALL = 'a';

    // GLOBAL
    const GLOBAL = 'b';
    const GLOBAL_HIDDEN = 'b_a';
    const GLOBAL_UNTOUCHABLE = 'b_b';
    const GLOBAL_CONNECT = 'b_c';
    const GLOBAL_DEBUG = 'b_d';

    // SITE
    const SITE = 'c';
    const SITE_GET = 'c_a';
    const SITE_GET_USERS = 'c_a_a';
    const SITE_MANAGE = 'c_b';
    const SITE_MANAGE_USERS = 'c_b_a';

    // CAFET
    const CAFET = 'd';
    const CAFET_ME = 'd_a';
    const CAFET_ME_CLIENT = 'd_a_a';
    const CAFET_ME_RELOADS = 'd_a_b';
    const CAFET_ME_EXPENSES = 'd_a_c';
    const CAFET_PURCHASE = 'd_b';
    const CAFET_ADMIN = 'd_c';
    const CAFET_ADMIN_PANELACCESS = 'd_c_a';
    const CAFET_ADMIN_ORDER = 'd_c_b';
    const CAFET_ADMIN_RELOAD = 'd_c_c';
    const CAFET_ADMIN_NEGATIVERELOAD = 'd_c_d';
    const CAFET_ADMIN_STATS = 'd_c_e';
    const CAFET_ADMIN_GET = 'd_c_f';
    const CAFET_ADMIN_GET_CLIENTS = 'd_c_f_a';
    const CAFET_ADMIN_GET_RELOADS = 'd_c_f_b';
    const CAFET_ADMIN_GET_EXPENSES = 'd_c_f_c';
    const CAFET_ADMIN_GET_PRODUCTS = 'd_c_f_d';
    const CAFET_ADMIN_GET_FORMULAS = 'd_c_f_e';
    const CAFET_ADMIN_MANAGE = 'd_c_g';
    const CAFET_ADMIN_MANAGE_PRODUCTS = 'd_c_g_a';
    const CAFET_ADMIN_MANAGE_FORMULAS = 'd_c_g_b';
    const CAFET_ADMIN_MANAGE_SETTINGS = 'd_c_g_c';
    const CAFET_ADMIN_MANAGE_STOCKS = 'd_c_g_d';

    public static function checkPermission(string $permission, Permissible $permissible): bool
    {
        $permissions = $permissible->getPermissions();
        $defined_permissions = array_keys($permissions);
        $result = in_array(self::ALL, $defined_permissions, true) ? $permissions[self::ALL] : false;

        if ($permission == '' && ! $result)
            return false;

        $perm_path = explode('_', $permission);
        $path_length = count($perm_path);
        $perm_check = '';
        $i = 0;

        do {
            $perm_check .= $perm_path[$i];
            if (in_array($perm_check, $defined_permissions, true))
                $result = $permissions[$perm_check];

            $i ++;
            $perm_check .= '_';
        } while ($i < $path_length);

        return $result;
    }
}

