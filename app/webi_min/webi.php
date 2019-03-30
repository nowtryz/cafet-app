<?php
use function webi_min\includes\controller_account_reset;
use function webi_min\includes\controller_edit_password;
use function webi_min\includes\controller_edit_profile;
use function webi_min\includes\controller_home_page;
use function webi_min\includes\controller_members_reset;
use function webi_min\includes\controller_show_profile;
use function webi_min\includes\controller_signin;
use function webi_min\includes\controller_signout;
use function webi_min\includes\controller_signup;
use webi_min\pages\controllers\ManageUsersController;

require_once '../cafetapi_load.php';

define('WEBI_DIR', CAFET_DIR .'webi_min' . DIRECTORY_SEPARATOR);
define('WEBI_INCLUDES', WEBI_DIR . 'includes' . DIRECTORY_SEPARATOR);
define('WEBI_PAGE_VIEWS', WEBI_DIR . 'pages' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR);
define('WEBI_PAGE_CONTROLLERS', WEBI_DIR . 'pages' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR);

require_once WEBI_INCLUDES . 'PageBuilder.php';
require_once WEBI_INCLUDES . 'pages_controllers.php';
require_once WEBI_INCLUDES . 'home_page.php';
require_once WEBI_PAGE_CONTROLLERS . 'ManageUsersController.php';

switch (@$_REQUEST['action']) {
    case 'me':
        controller_show_profile();
        break;
    case 'me/edit':
        controller_edit_profile();
        break;
    case 'me/edit/pwd':
        controller_edit_password();
        break;

    case 'signout':
        controller_signout();
        break;

    case 'signin':
        controller_signin();
        break;

    case 'signup':
        controller_signup();
        break;
    
    case 'account/reset':
        controller_account_reset();
        break;

    case 'manage':
        $c = new ManageUsersController();
        $c->buildPage();
        break;
        
    case 'manage/reset-members':
        controller_members_reset();
        break;

    default:
        controller_home_page();
        break;
}