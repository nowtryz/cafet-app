<?php
namespace cafetapi\io;

use cafetapi\exceptions\EmailFormatException;
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
    const FIELD_ID = 'id';
    const FIELD_USERNAME = 'username';
    const FIELD_EMAIL = 'email';
    const FIELD_FIRSTNAME = 'firstname';
    const FIELD_FAMILYNAME = 'familyname';
    const FIELD_PHONE = 'phone';
    const FIELD_GROUP_ID = 'group_id';
    const FIELD_REGISTRATION = 'registration';
    const FIELD_LAST_SIGNIN = 'last_signin';
    const FIELD_SIGNIN_COUNT = 'signin_count';
    const FIELD_PASSWORD = 'password';
    const FIELD_PERMISSIONS = 'permissions';
    
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
            . self::FIELD_ID . ' id, '
            . self::FIELD_USERNAME . ' username, '
            . self::FIELD_EMAIL . ' mail, '
            . self::FIELD_PASSWORD . ' hash, '
            . self::FIELD_FIRSTNAME . ' firstname, '
            . self::FIELD_FAMILYNAME . ' name, '
            . self::FIELD_PHONE . ' phone, '
            . self::FIELD_GROUP_ID . ' group_id, '
            . self::FIELD_REGISTRATION . ' registration, '
            . self::FIELD_LAST_SIGNIN . ' last_signin, '
            . self::FIELD_SIGNIN_COUNT . ' signin_count, '
            . self::FIELD_PERMISSIONS . ' permissions '
            . 'FROM ' . self::USERS . ' '
            . 'WHERE ' . self::FIELD_USERNAME . ' = :param1 '
            . 'OR ' . self::FIELD_EMAIL . ' = :param2');
        
        $id = $_group = $signin_count = 0;
        $username = $mail = $hash = $firstname = $name = $phone = '';
        $_last_signin = $_registration = '2018-01-01 00:00:00';
        $_permissions = 'a:0:{}';
        
        $statement->bindColumn('id', $id, PDO::PARAM_INT);
        $statement->bindColumn('username', $username, PDO::PARAM_STR);
        $statement->bindColumn('mail', $mail, PDO::PARAM_STR);
        $statement->bindColumn('hash', $hash, PDO::PARAM_STR);
        $statement->bindColumn('firstname', $firstname, PDO::PARAM_STR);
        $statement->bindColumn('name', $name, PDO::PARAM_STR);
        $statement->bindColumn('phone', $phone, PDO::PARAM_STR);
        $statement->bindColumn('group_id', $_group, PDO::PARAM_INT);
        $statement->bindColumn('last_signin', $_last_signin, PDO::PARAM_STR);
        $statement->bindColumn('registration', $_registration, PDO::PARAM_STR);
        $statement->bindColumn('signin_count', $signin_count, PDO::PARAM_INT);
        $statement->bindColumn('permissions', $_permissions, PDO::PARAM_STR);
        
        $statement->execute(array(
            'param1' => $mail_or_pseudo,
            'param2' => $mail_or_pseudo
        ));
        
        if ($statement->errorCode() != '00000') self::registerErrorOccurence($statement);

        $result = $statement->fetch();

        if (! $result) return null;
        
        $last_signin = get_calendar_from_datetime($_last_signin);
        $registration = get_calendar_from_datetime($_registration);
        $permissions = @unserialize($_permissions) ?: array();
        
        if (isset(Group::GROUPS[$_group])){
            $group = new Group($_group, Group::GROUPS[$_group]);
        } else {
            $group = new Group(0, Group::GUEST);
        }

        $user = new User($id, $username, $firstname, $name, $hash, $mail, $phone, $last_signin, $registration, $signin_count, $group, $permissions);
        $statement->closeCursor();

        return $user;
    }
    
    public final function getUserById(int $user_id): ?User
    {
        $statement = $this->connection->prepare('SELECT '
            . self::FIELD_ID . ' id, '
            . self::FIELD_USERNAME . ' username, '
            . self::FIELD_EMAIL . ' mail, '
            . self::FIELD_PASSWORD . ' hash, '
            . self::FIELD_FIRSTNAME . ' firstname, '
            . self::FIELD_FAMILYNAME . ' name, '
            . self::FIELD_PHONE . ' phone, '
            . self::FIELD_GROUP_ID . ' group_id, '
            . self::FIELD_REGISTRATION . ' registration, '
            . self::FIELD_LAST_SIGNIN . ' last_signin, '
            . self::FIELD_SIGNIN_COUNT . ' signin_count, '
            . self::FIELD_PERMISSIONS . ' permissions '
            . 'FROM ' . self::USERS . ' '
            . 'WHERE ' . self::FIELD_ID . ' = :id');
        
        $id = $_group = $signin_count = 0;
        $username = $mail = $hash = $firstname = $name = $phone = '';
        $_last_signin = $_registration = '2018-01-01 00:00:00';
        $_permissions = 'a:0:{}';
        
        $statement->bindColumn('id', $id, PDO::PARAM_INT);
        $statement->bindColumn('username', $username, PDO::PARAM_STR);
        $statement->bindColumn('mail', $mail, PDO::PARAM_STR);
        $statement->bindColumn('hash', $hash, PDO::PARAM_STR);
        $statement->bindColumn('firstname', $firstname, PDO::PARAM_STR);
        $statement->bindColumn('name', $name, PDO::PARAM_STR);
        $statement->bindColumn('phone', $phone, PDO::PARAM_STR);
        $statement->bindColumn('group_id', $_group, PDO::PARAM_INT);
        $statement->bindColumn('last_signin', $_last_signin, PDO::PARAM_STR);
        $statement->bindColumn('registration', $_registration, PDO::PARAM_STR);
        $statement->bindColumn('signin_count', $signin_count, PDO::PARAM_INT);
        $statement->bindColumn('permissions', $_permissions, PDO::PARAM_STR);
        
        $statement->execute(array(
            'id' => $user_id
        ));
        
        if ($statement->errorCode() != '00000') self::registerErrorOccurence($statement);
        
        $result = $statement->fetch();
        
        if (! $result) return null;
        
        $last_signin = get_calendar_from_datetime($_last_signin);
        $registration = get_calendar_from_datetime($_registration);
        $permissions = @unserialize($_permissions) ?: array();
        
        if (isset(Group::GROUPS[$_group])){
            $group = new Group($_group, Group::GROUPS[$_group]);
        } else {
            $group = new Group(0, Group::GUEST);
        }
        
        $user = new User($id, $username, $firstname, $name, $hash, $mail, $phone, $last_signin, $registration, $signin_count, $group, $permissions);
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
    public final function addUser(string $username, string $email, string $firstname, string $name, string $password, int $group_id = 1): ?User
    {
        $this->beginTransaction();
        
        $stmt = $this->connection->prepare('INSERT '
            . 'INTO ' . self::USERS . ' (`' . self::FIELD_USERNAME . '`, `' . self::FIELD_EMAIL . '`, `' . self::FIELD_FIRSTNAME . '`, `' . self::FIELD_FAMILYNAME . '`, `' . self::FIELD_PASSWORD . '`, `' . self::FIELD_GROUP_ID . '`, `' . self::FIELD_PERMISSIONS . '`) '
            . 'VALUES (:username, :email, :firstname, :familyname, :password, :group_id, :permissions)'
        );
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new EmailFormatException('"' . $email . '" is not valid!');
        
        $stmt->execute(array(
            'username' => $username,
            'email' => $email,
            'firstname' => $firstname,
            'familyname' => $name,
            'password' => cafet_generate_hashed_pwd($password),
            'group_id' => $group_id,
            'permissions' => 'a:0:{}'
        ));
        
        $this->checkUpdate($stmt, 'unable to add the user');
        
        $user_id = $this->connection->lastInsertId();
        
        $stmt->closeCursor();
        $this->commit();
        
        return $this->getUserById($user_id);
    }
    
    public final function registerLogin($id)
    {
        $this->beginTransaction();
        
        $stmt = $this->connection->prepare('UPDATE `' . self::USERS . '` '
            . 'SET 	' . self::FIELD_LAST_SIGNIN . ' = NOW(), ' . self::FIELD_SIGNIN_COUNT . ' = ' . self::FIELD_SIGNIN_COUNT . ' + 1 '
            . 'WHERE ' . self::FIELD_ID . ' = ?'
            );
        
        $stmt->execute(array($id));
        
        $this->checkUpdate($stmt, 'unable update user');
        
        $stmt->closeCursor();
        $this->commit();
    }
    
    public final function setPseudo(int $user_id, string $new_pseudo) : bool
    {
        
    }
    
    public final function setFirstname(int $user_id, string $new_firstname) : bool
    {
        
    }
    
    public final function setName(int $user_id, string $new_name) : bool
    {
        
    }
    
    public final function setEmail(int $user_id, string $new_email) : bool
    {
        
    }
    
    public final function setPhone(int $user_id, string $new_pseudo) : bool
    {
        
    }
    
    public final function setGroup(int $user_id, int $group_id) : bool
    {
        
    }
}

