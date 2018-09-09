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
    const ALL = '*';

    // GLOBAL
    const GLOBAL = 'global';

    const GLOBAL_HIDDEN = 'global_hidden';

    const GLOBAL_UNTOUCHABLE = 'global_untouchable';

    const GLOBAL_CONNECT = 'global_connect';
    
    const GLOBAL_DEBUG = 'global_debug';

    // SITE
    const SITE = 'site';

    const SITE_POST = 'site_post';

    const SITE_MANAGE = 'site_manage';

    // CAFET
    const CAFET = 'cafet';

    const CAFET_PURCHASE = 'cafet_pruchase';

    const CAFET_ADMIN = 'cafet_admin';

    const CAFET_ADMIN_PANELACCESS = 'cafet_admin_panelaccess';

    const CAFET_ADMIN_ORDER = 'cafet_admin_order';

    const CAFET_ADMIN_RELOAD = 'cafet_admin_reload';

    const CAFET_ADMIN_NEGATIVERELOAD = 'cafet_admin_negativereload';

    const CAFET_ADMIN_STATS = 'cafet_admin_stats';

    const CAFET_ADMIN_GET = 'cafet_admin_get';

    const CAFET_ADMIN_GET_CLIENTS = 'cafet_admin_get_clients';

    const CAFET_ADMIN_GET_RELOADS = 'cafet_admin_get_reloads';

    const CAFET_ADMIN_GET_EXPENSES = 'cafet_admin_get_expenses';

    const CAFET_ADMIN_GET_PRODUCTS = 'cafet_admin_get_products';

    const CAFET_ADMIN_GET_FORMULAS = 'cafet_admin_get_formulas';

    const CAFET_ADMIN_MANAGE = 'cafet_admin_manage';

    const CAFET_ADMIN_MANAGE_PRODUCTS = 'cafet_admin_manage_products';

    const CAFET_ADMIN_MANAGE_FORMULAS = 'cafet_admin_manage_formulas';

    const CAFET_ADMIN_MANAGE_SETTINGS = 'cafet_admin_manage_settings';

    const CAFET_ADMIN_MANAGE_STOCKS = 'cafet_admin_manage_stocks';

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

