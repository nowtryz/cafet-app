<?php
use cafetapi\ErrorPageBuilder;
use webi_min\includes\PageBuilder;
use function webi_min\includes\controller_account_reset;
use function webi_min\includes\controller_edit_password;
use function webi_min\includes\controller_manage_users;
use function webi_min\includes\controller_show_profile;
use function webi_min\includes\controller_signin;
use function webi_min\includes\controller_signout;
use function webi_min\includes\controller_signup;

require_once 'cafetapi_load.php';
require_once 'webi_min/includes/PageBuilder.php';
require_once 'webi_min/includes/pages_views.php';
require_once 'webi_min/includes/pages_controllers.php';
require_once 'webi_min/includes/manage_users.php';

switch (@$_REQUEST['action']) {
    case 'me':
        controller_show_profile();
        break;
    case 'me/edit':
        ontroller_edit_profile();
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
        controller_manage_users();
        break;
        
    case 'manage/reset-members':
        break;

    case 'pages':
        $b = new PageBuilder();
        if (function_exists('webi_min\includes\page_' . @$_REQUEST['p'])) {
            $b->build('webi_min\includes\page_' . @$_REQUEST['p']);
        } else {
            (new ErrorPageBuilder(404))->print();
        }

        break;

    default:
        $b = new PageBuilder();
        $b->build('webi_min\includes\maintenance');
        break;
}