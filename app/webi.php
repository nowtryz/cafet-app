<?php
use cafetapi\ErrorPageBuilder;
use webi_min\includes\PageBuilder;

require_once 'cafetapi_load.php';
require_once 'webi_min/includes/PageBuilder.php';
require_once 'webi_min/includes/pages_views.php';
require_once 'webi_min/includes/pages_controllers.php';

switch (@$_REQUEST['action']) {
    case 'me':
        webi_controller_show_profile();
        break;
    case 'me/edit':
        webi_controller_edit_profile();
        break;
    case 'me/edit/pwd':
        webi_controller_edit_password();
        break;

    case 'signout':
        webi_controller_signout();
        break;

    case 'signin':
        webi_controller_signin();
        break;

    case 'signup':
        webi_controller_signup();
        break;
    
    case 'account/reset':
        webi_controller_account_reset();
        break;

    case 'manage':
        break;

    case 'pages':
        $b = new PageBuilder();
        if (function_exists('webi_page_' . @$_REQUEST['p'])) {
            $b->build('webi_page_' . @$_REQUEST['p']);
        } else {
            (new ErrorPageBuilder(404))->print();
        }

        break;

    default:
        $b = new PageBuilder();
        $b->build('webi_maintenance');
        break;
}