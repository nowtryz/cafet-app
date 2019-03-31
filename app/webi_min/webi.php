<?php
use function cafetapi\webi\Core\controller_account_reset;
use function cafetapi\webi\Core\controller_edit_password;
use function cafetapi\webi\Core\controller_edit_profile;
use function cafetapi\webi\Core\controller_home_page;
use function cafetapi\webi\Core\controller_members_reset;
use function cafetapi\webi\Core\controller_show_profile;
use function cafetapi\webi\Core\controller_signin;
use function cafetapi\webi\Core\controller_signout;
use function cafetapi\webi\Core\controller_signup;
use cafetapi\webi\pages\controllers\ManageUsersController;
use cafetapi\webi\pages\controllers\HomePageController;

require_once '../cafetapi_load.php';

define('WEBI_DIR', CAFET_DIR .'webi_min' . DIRECTORY_SEPARATOR);
define('WEBI_INCLUDES', WEBI_DIR . 'core' . DIRECTORY_SEPARATOR);
define('WEBI_PAGE_VIEWS', WEBI_DIR . 'pages' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR);
define('WEBI_PAGE_CONTROLLERS', WEBI_DIR . 'pages' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR);

$loader = cafet_get_class_autoloader();
$loader->addNamespace('cafetapi\webi', WEBI_DIR);
$loader->addNamespace('cafetapi\webi\Core', WEBI_INCLUDES, true);
$loader->addNamespace('cafetapi\webi\pages\views', WEBI_PAGE_VIEWS, true);
$loader->addNamespace('cafetapi\webi\pages\controllers', WEBI_PAGE_CONTROLLERS, true);

require_once WEBI_INCLUDES . 'pages_controllers.php';
require_once WEBI_INCLUDES . 'home_page.php';

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
        $c = new HomePageController();
        $c->buildPage();
        break;
}