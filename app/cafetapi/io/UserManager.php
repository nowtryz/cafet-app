<?php
namespace cafetapi\io;

use cafetapi\user\Group;
use cafetapi\user\User;
use PDO;

/**
 *
 * @author Damien
 *        
 */
class UserManager extends Updater
{
    private static $instance;
    
    /**
     * Get singleton object
     * @return UserManager the singleton of this class
     */
    public static function getInstance() : UserManager
    {
        if(self::$instance === null) self::$instance = new UserManager();
        return self::$instance;
    }
    public final function getUser(string $mail_or_pseudo): ?User
    {
        $statement = $this->connection->prepare('SELECT '
            . 'id id, '
            . 'username username, '
            . 'email mail, '
            . 'password hash, '
            . 'firstname firstname, '
            . 'familyname name, '
            . 'phone phone, '
            . 'group_id group, '
            . 'registration registration, '
            . 'last_signin last_signin, '
            . 'signin_count signin_count '
            . 'FROM cafet_users '
            . 'WHERE username = :param1 '
            . 'OR email = :param2');
        
        $id = $_group = $signin_count = 0;
        $username = $mail = $hash = $firstname = $name = $phone = '';
        $_last_signin = $_registration = '2018-01-01 00:00:00';
        
        $statement->bindColumn('id', $id, PDO::PARAM_INT);
        $statement->bindColumn('username', $username, PDO::PARAM_STR);
        $statement->bindColumn('mail', $mail, PDO::PARAM_STR);
        $statement->bindColumn('hash', $hash, PDO::PARAM_STR);
        $statement->bindColumn('firstname', $firstname, PDO::PARAM_STR);
        $statement->bindColumn('name', $name, PDO::PARAM_STR);
        $statement->bindColumn('phone', $phone, PDO::PARAM_STR);
        $statement->bindColumn('group', $_group, PDO::PARAM_INT);
        $statement->bindColumn('last_signin', $_last_signin, PDO::PARAM_STR);
        $statement->bindColumn('registration', $_registration, PDO::PARAM_STR);
        $statement->bindColumn('signin_count', $signin_count, PDO::PARAM_INT);
        
        $statement->execute(array(
            'param1' => $mail_or_pseudo,
            'param2' => $mail_or_pseudo
        ));
        
        if ($statement->errorCode() != '00000') self::registerErrorOccurence($statement);

        $result = $statement->fetch();

        if (! $result) return null;
        
        $last_signin = get_calendar_from_datetime($_last_signin);
        $registration = get_calendar_from_datetime($_registration);
        
        if (isset(Group::GROUPS[$_group])){
            $group = new Group($_group, Group::GROUPS[$_group]);
        } else {
            $group = new Group(0, Group::GUEST);
        }

        $user = new User($id, $username, $firstname, $name, $hash, $mail, $phone, $last_signin, $registration, $signin_count, $group);
        $statement->closeCursor();

        return $user;
    }
    
    /**
     * Insert a user into the database
     *
     * @param string $name
     * @param int $group_id
     * @return User the user inserted
     * @since API 1.0.0 (2018)
     */
    public final function addUser(string $username, string $email, string $firstname, string $name, string $password, int $group_id = 0): ?User
    {
        $this->beginTransaction();
        
        $stmt = $this->connection->prepare('INSERT INTO ...');
        $stmt->execute(array(
            '...' => '...'
        ));
        
        $this->checkUpdate($stmt, 'unable to add the user');
        
        $stmt->closeCursor();
        $this->commit();
        return null;
    }
}

