<?php
/**
 * Function file for basic functions
 * 
 * @package cafetapi
 * @since API 1.0
 */

use cafetapi\Mail;
use cafetapi\io\ClientManager;
use cafetapi\io\DatabaseConnection;
use cafetapi\io\FormulaManager;
use cafetapi\io\ProductManager;
use cafetapi\modules\cafet_app\CafetApp;
use cafetapi\user\Group;

if (! defined('basics_functions_loaded') ) {
    define('basics_functions_loaded', true);
    
    function cafet_is_app_request() {
        return isset($_POST['origin']) && $_POST['origin'] == 'cafet_app';
    }

    /**
     * Listen post request for app call
     *
     * @since API 1.0.0 (2018)
     */
    function cafet_listen_app_request()
    {
        if (cafet_is_app_request()) {
            error_reporting(-1);
            set_error_handler('cafet_error_handler');
            set_exception_handler('cafet_exception_handler');
            
            try {
                new CafetApp();
            } catch (Exception $e) {
                cafet_throw_error('01-003', $e->getMessage());
            }
            exit();
        }
    }
    
    function cafet_get_guest_group() : Group
    {
        return new Group('Guest', Group::GUEST);
    }

    function cafet_render_product_image(int $product_id, bool $dwl = false)
    {
        if (headers_sent()) return false;

        $product = ProductManager::getInstance()->getProduct($product_id);

        if (! $product) return false;

        header('content-type: ' . guess_image_mime($product->getImage()));

        if ($dwl) header('Content-Disposition: attachment; filename="' . $product->getName() . get_base64_image_format($product->getImage()) . '"');
        echo base64_decode($product->getImage());
        exit();
    }
    
    function cafet_render_formula_image(int $formula_id, bool $dwl = false)
    {
        if (headers_sent()) return false;
        
        $formula = FormulaManager::getInstance()->getFormula($formula_id);

        if (! $formula) return false;

        header('content-type: ' . guess_image_mime($formula->getImage()));

        if ($dwl) header('Content-Disposition: attachment; filename="' . $formula->getName() . get_base64_image_format($formula->getImage()) . '"');
        echo base64_decode($formula->getImage());
        exit();
    }

    function cafet_send_reload_request(int $client_id)
    {
        $c = ClientManager::getInstance()->getClient($client_id);
        $mail = new Mail('reload_request', $c->getEmail());
        $mail->setVar('surname', $c->getSurname());
        $mail->setVar('name', $c->getFamilyNane());
        $mail->setVar('balance', number_format($c->getBalance(), 2, ',', ' '));

        $expenses = '';

        foreach ($c->getLastExpenses() as $expense) {
            $expenses .= '<tr><td>' . 'Le ' . $expense->getDate()->getFormatedDate() . ' à ' . $expense->getDate()->getFormatedTime() . '</td><td>' . number_format($expense->getTotal(), 2, ',', ' ') . ' €' . '</td><td>' . number_format($expense->getBalanceAfterTransaction(), 2, ',', ' ') . ' €' . '</td></tr>';
        }

        $mail->setVar('expenses', $expenses);

        $mail->send();
    }

    

    /**
     * Checks headers before sending them to the client
     *
     * @since API 1.0.0 (2018)
     */
    function cafet_headers_check()
    {
        $list = headers_list();
        $headers_to_remove = array(
            'X-Powered'
        );

        foreach ($list as $header) {
            foreach ($headers_to_remove as $to_remove) {
                if (strpos($header, $to_remove) !== false) {
                    header_remove(explode(':', $header)[0]);
                }
            }
        }
    }

    /**
     * Return the time difference between launch and now
     *
     * @return float the duration
     * @since API 1.0.0 (2018)
     */
    function cafet_execution_duration(): float
    {
        return microtime(true) - START_TIME;
    }

    /**
     * Only for debug
     *
     * @since API 1.0.0 (2018)
     */
    function cafet_dump_server_vars()
    {
        if (session_status() == PHP_SESSION_ACTIVE) {
            echo '<h2>session_id</h2>';
            echo session_id();
            echo '<h2>$_SESSION</h2>';
            var_dump($_SESSION);
        }

        echo '<h2>$_COOKIE</h2>';
        var_dump($_COOKIE);
        
        echo '<h2>$_GET</h2>';
        var_dump($_GET);

        echo '<h2>$_REQUEST</h2>';
        var_dump($_REQUEST);

        echo '<h2>$_FILES</h2>';
        var_dump($_FILES);

        echo '<h2>$_SERVER</h2>';
        var_dump($_SERVER);

        echo '<h2>Last SQL Error</h2>';
        var_dump(DatabaseConnection::getLastQueryErrors());

        if (session_status() == PHP_SESSION_ACTIVE && isset($_SESSION['user'])) {
            echo '<h2>Logged user</h2>';
            var_dump(cafet_get_logged_user());
        }

        echo '<h2>Execution duration</h2>';
        echo 'Computed in ' . cafet_execution_duration() . ' seconds';
    }
}