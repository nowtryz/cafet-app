<?php
namespace cafetapi\modules\cafet_app;

use cafetapi\Logger;
use cafetapi\config\Config;
use cafetapi\exceptions\NotEnoughtMoneyException;
use cafetapi\exceptions\PermissionNotGrantedException;
use cafetapi\exceptions\RequestFailureException;
use cafetapi\io\DatabaseConnection;
use cafetapi\user\Perm;

/**
 *
 * @author Damien
 *        
 */
class CafetApp
{

    private $version;

    private $session_id;

    private $action;

    private $arguments;

    private $user;

    /**
     */
    public function __construct()
    {
        if (! isset($_POST['origin']) || $_POST['origin'] != 'cafet_app')
            return;

        if (! isset($_POST['action']) || ! $_POST['action'])
            Logger::throwError('03-001');
        if (! isset($_POST['version']) || ! $_POST['version'])
            Logger::throwError('03-002');
        if (! isset($_POST['arguments']) || ! $_POST['arguments'])
            Logger::throwError('03-004');

        $this->action = str_replace(' ', '_', $_POST['action']);
        $this->version = $_POST['version'];
        $this->arguments = json_decode($_POST['arguments'], true);

        $client_version = explode('.', explode('-', $this->version)[0]);
        $server_version = explode('.', explode('-', API_VERSION)[0]);
        if (count($client_version) != count($server_version))
            Logger::throwError('04-001');
        $n = count($server_version);
        for ($i = 0; $i < $n - 1; $i ++) {
            if ($client_version[$i] < $server_version[$i])
                Logger::throwError('04-001');
            elseif ($client_version[$i] > $server_version[$i])
                Logger::throwError('04-002');
        }

        $this->session_id = isset($_POST['session_id']) && $_POST['session_id'] ? cafet_init_session(true, $_POST['session_id']) : cafet_init_session(true);

        if (isset($_SESSION['user']))
            $this->user = cafet_get_logged_user();
        elseif ($this->action == 'login' && ! $this->arguments)
            Logger::throwError('03-401');
        elseif ($this->action == 'login')
            $this->login();
        elseif (isset($_POST['session_id']) && $_POST['session_id'])
            Logger::throwError('02-401');
        else
            Logger::throwError('03-003');

        if (isset($this->user) && $this->action == 'login')
            $this->returnLogedSession();

        switch ($this->action) {
            case 'logout':
            case 'updates':
            case 'changelog':
                $this->{$this->action}();
                break;
            default:
                if ($this->arguments === NULL)
                    Logger::throwError('03-401');

                if (in_array($this->action, get_class_methods(FetchHandler::class)))
                    $this->dataFunction(__NAMESPACE__ . '\FetchHandler');
                elseif (in_array($this->action, get_class_methods(UpdateHandler::class)))
                    $this->dataFunction(__NAMESPACE__ . '\UpdateHandler');
                else
                    Logger::throwError('03-008');
        }
    }

    private function login()
    {
        if (! isset($this->arguments['email']) || ! isset($this->arguments['password']))
            Logger::throwError('03-006');

        $this->user = cafet_check_login($this->arguments['email'], $this->arguments['password']);

        if (! $this->user) {
            if (! isset(DatabaseConnection::getLastQueryError()[2]))
                Logger::throwError('02-001');
            else
                Logger::throwError('02-001', DatabaseConnection::getLastQueryError()[2]);
        } else if (! Perm::checkPermission(PERM::GLOBAL_CONNECT, $this->user) || ! Perm::checkPermission(PERM::CAFET_ADMIN_PANELACCESS, $this->user)) {
            Logger::throwError("02-002");
        } else
            cafet_set_logged_user($this->user);

        $this->returnLogedSession();
    }

    private function returnLogedSession()
    {
        $array = json_decode($this->user->__toString(), true);

        if (! isset($array))
            Logger::throwError('01-002', 'the json has messed up!');

        $result = array(
            'session_id' => $this->session_id,
            'server_version' => API_VERSION,
            'user' => $array
        );

        $return = new ReturnStatement('ok', $result);
        $this->end();
        $return->print();
    }

    private function logout()
    {
        cafet_destroy_session();

        $result = array(
            'logout_message' => Config::logout_message
        );

        $return = new ReturnStatement('ok', $result);
        $this->end();
        $return->print();
    }

    private function updates()
    {
        if (! isset($this->arguments['app-version']))
            Logger::throwError('03-006', 'where is your version? I know mine but i don\'t your');

        $file = implode('', file(CONTENT_DIR . 'app_changelog.json'));
        $json = json_decode($file, true);
        if (! isset($json))
            Logger::throwError('01-002', 'the changelog json has messed up!');

        $last_version = explode('.', array_keys($json)[0]);
        $app_version = explode('.', $this->arguments['app-version']);

        if (count($last_version) != count($app_version)) {
            $this->sendUpdateInfos(true);
            return;
        }

        $n = count($last_version);
        for ($i = 0; $i < $n; $i ++)
            if ($last_version > $app_version) {
                $this->sendUpdateInfos();
                return;
            }

        $result = array(
            'need_update' => false
        );

        $return = new ReturnStatement('ok', $result);
        $this->end();
        $return->print();
    }

    private function sendUpdateInfos(bool $diferent_numbers = false)
    {
        $jar_url = (((bool) !Config::installer_external) ? Config::url : '') . Config::installer_jar_url;
        $win_url = (((bool) !Config::installer_external) ? Config::url : '') . Config::installer_url;

        if ($diferent_numbers) {
            $result = array(
                'need_update' => true,
                'message' => 'It\'s strange, it seems we do not have the same amount of identifiers... However here is what you asked for.',
                'jar_url' => $jar_url,
                'win_url' => $win_url
            );
        } else {
            $result = array(
                'need_update' => true,
                'jar_url' => $jar_url,
                'win_url' => $win_url
            );
        }

        $return = new ReturnStatement('ok', $result);
        $this->end();
        $return->print();
    }

    private function changelog()
    {
        $file = implode('', file(CONTENT_DIR . 'app_changelog.json'));

        $json = json_decode($file, true);

        if (! isset($json))
            Logger::throwError('01-002', 'the json has messed up!');

        $return = new ReturnStatement('ok', $json);
        $this->end();
        $return->print();
    }

    private function dataFunction(string $data_handler)
    {
        global $user;
        global $app;
        $user = $this->user;
        $app = 'cafet_app';

        // if( !in_array( $this->action, get_class_methods( $this ))) Logger::throwError( '01-500', 'It seems that this action is not completely coded' );

        // $handler = new $data_handler();

        try {
            $result = (new $data_handler())->{$this->action}($this->arguments);
        } catch (RequestFailureException $e) {
            Logger::throwError('01-002', $e->getMessage());
        } catch (PermissionNotGrantedException $e) {
            Logger::throwError('02-002', $e->getMessage());
        } catch (NotEnoughtMoneyException $e) {
            Logger::throwError('04-003', $e->getMessage());
        }

        if ($result === null)
            Logger::throwError('01-002');

        $return = new ReturnStatement('ok', $result);
        $this->end();
        $return->print();
    }

    private function end()
    {
        session_commit();
    }
}