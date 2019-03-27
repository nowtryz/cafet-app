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
    
    const TABLE_NAME = self::USERS;
    
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
    
    /**
     * Fetch user information for the given username/email
     *
     * @param string $mail_or_pseudo
     *            the mail/pseudo of the user
     * @return NULL|User A User object that represents the user
     * @since API 0.1.0 (2018)
     */
    //     public function getUser(string $mail_or_pseudo): ?User
    //     {
    //         $statement = $this->connection->prepare('SELECT '
    //             . 'ID id, '
    //             . 'Pseudo pseudo, '
    //             . 'MDP hash, '
    //             . 'SU super_user, '
    //             . 'admin admin, '
    //             . 'res_cafet cafet_manager, '
    //             . 'adm_cafet cafet_admin, '
    //             . 'Nom name, '
    //             . 'Prenom firstname, '
    //             . 'Email mail, '
    //             . 'Tel phone '
    //             . 'FROM ' . self::USERS . ' '
    //             . 'WHERE Pseudo = :param1 '
    //             . 'OR Email = :param2');
    
    //         $id = 0;
    //         $super_user = $admin = $cafet_manager = $cafet_admin = false;
    
    //         $statement->bindColumn('id', $id, PDO::PARAM_INT);
    //         $statement->bindColumn('super_user', $super_user, PDO::PARAM_BOOL);
    //         $statement->bindColumn('admin', $admin, PDO::PARAM_BOOL);
    //         $statement->bindColumn('cafet_manager', $cafet_manager, PDO::PARAM_BOOL);
    //         $statement->bindColumn('cafet_admin', $cafet_admin, PDO::PARAM_BOOL);
    
    //         $statement->execute(array(
    //             'param1' => $mail_or_pseudo,
    //             'param2' => $mail_or_pseudo
    //         ));
    
    //         if ($statement->errorCode() != '00000')
        //             self::registerErrorOccurence($statement);
    
        //         $result = $statement->fetch();
    
        //         if (! $result)
            //             return null;
        
            //         // support for old website
            //         if ($super_user) $group = new Group('super user', Group::SUPER_USER);
            //         elseif ($admin) $group = new Group('administrator', Group::ADMIN);
            //         elseif ($cafet_admin) $group = new Group('cafet admin', Group::CAFET_ADMIN);
            //         elseif ($cafet_manager) $group = new Group('cafet manager', Group::CAFET_MANAGER);
            //         else $group = new Group('consumer', Group::CONSUMER);
        
            //         $user = new User($id, $result['pseudo'], $result['firstname'], $result['name'], $result['hash'], $result['mail'], $result['phone'], new Calendar(2018, 1, 1, 0, 0, 0), new Calendar(2018, 1, 1, 0, 0, 1), 0, $group);
            //         $statement->closeCursor();
        
            //         return $user;
            //     }
    
    public final function count() : int
    {
        $count = 0;
        $statement = $this->connection->prepare("SELECT COUNT(*) count FROM " . self::USERS);
        $statement->bindColumn('count', $count, PDO::PARAM_INT);
        $statement->execute();
        
        $this->check_fetch_errors($statement);
        
        $statement->fetch();
        return $count;
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
        
        $statement->execute([
            'param1' => $mail_or_pseudo,
            'param2' => $mail_or_pseudo
        ]);
        
        $this->check_fetch_errors($statement);

        $result = $statement->fetch();

        if (! $result) return null;
        
        $last_signin = get_calendar_from_datetime($_last_signin);
        $registration = get_calendar_from_datetime($_registration);
        $permissions = @unserialize($_permissions) ?? [];
        
        $group = isset(Group::GROUPS[$_group]) ? new Group($_group, Group::GROUPS[$_group]) : cafet_get_guest_group();

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
        
        $statement->execute([
            'id' => $user_id
        ]);
        
        $this->check_fetch_errors($statement);
        
        $result = $statement->fetch();
        
        if (! $result) return null;
        
        $last_signin = get_calendar_from_datetime($_last_signin);
        $registration = get_calendar_from_datetime($_registration);
        $permissions = @unserialize($_permissions) ?: [];
        
        $group = isset(Group::GROUPS[$_group]) ? new Group($_group, Group::GROUPS[$_group]) : cafet_get_guest_group();
        
        $user = new User($id, $username, $firstname, $name, $hash, $mail, $phone, $last_signin, $registration, $signin_count, $group, $permissions);
        $statement->closeCursor();
        
        return $user;
    }
    
    public final function getUsers(): array
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
            . 'FROM ' . self::USERS);
        
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
        
        $statement->execute();
        $this->check_fetch_errors($statement);
        
        $result = [];
        
        while ($statement->fetch()) {
            $last_signin = get_calendar_from_datetime($_last_signin);
            $registration = get_calendar_from_datetime($_registration);
            $permissions = @unserialize($_permissions) ?: [];
            
            $group = isset(Group::GROUPS[$_group]) ? new Group($_group, Group::GROUPS[$_group]) : cafet_get_guest_group();
            
            $result[] = new User($id, $username, $firstname, $name, $hash, $mail, $phone, $last_signin, $registration, $signin_count, $group, $permissions);
        }
        
        $statement->closeCursor();
        return $result;
    }
    
    public final function getPermissions(int $user_id) : array
    {
        $_permissions = 'a:0:{}';
        
        $statement = $this->connection->prepare('SELECT '
            . self::FIELD_PERMISSIONS . ' permissions '
            . 'FROM ' . self::USERS . ' '
            . 'WHERE ' . self::FIELD_ID . ' = :id');
        $statement->bindColumn('permissions', $_permissions, PDO::PARAM_STR);
        $statement->execute(['id' => $user_id]);
        
        $this->check_fetch_errors($statement);
        $result = $statement->fetch();
        $statement->closeCursor();
        
        if (! $result) return [];
        else return @unserialize($_permissions) ?: [];
    }
    
    /**
     * Insert a user into the database
     *
     * @param string $name
     * @param int $group_id
     * @return User the user inserted
     * @throws EmailFormatException if the email is not valid
     * @since API 0.1.0 (2018)
     */
    public final function addUser(string $username, string $email, string $firstname, string $name, string $password, int $group_id = 1): ?User
    {
        $this->beginTransaction();
        
        $stmt = $this->connection->prepare('INSERT '
            . 'INTO ' . self::USERS . ' (`' . self::FIELD_USERNAME . '`, `' . self::FIELD_EMAIL . '`, `' . self::FIELD_FIRSTNAME . '`, `' . self::FIELD_FAMILYNAME . '`, `' . self::FIELD_PASSWORD . '`, `' . self::FIELD_GROUP_ID . '`, `' . self::FIELD_PERMISSIONS . '`) '
            . 'VALUES (:username, :email, :firstname, :familyname, :password, :group_id, :permissions)'
        );
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new EmailFormatException('"' . $email . '" is not valid!');
        
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'firstname' => $firstname,
            'familyname' => $name,
            'password' => cafet_generate_hashed_pwd($password),
            'group_id' => $group_id,
            'permissions' => 'a:0:{}'
        ]);
        
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
        return $this->updateValue(self::FIELD_USERNAME, $user_id, $new_pseudo);
    }
    
    public final function setEmail(int $user_id, string $new_email) : bool
    {
        if (!filter_var($new_email, FILTER_VALIDATE_EMAIL))
        {
            throw new EmailFormatException('"' . $new_email . '" is not valid!');
        }
        return $this->updateValue(self::FIELD_EMAIL, $user_id, $new_email);
    }
    
    public final function setFirstname(int $user_id, string $new_firstname) : bool
    {
        return $this->updateValue(self::FIELD_FIRSTNAME, $user_id, $new_firstname);
    }
    
    public final function setName(int $user_id, string $new_name) : bool
    {
        return $this->updateValue(self::FIELD_FAMILYNAME, $user_id, $new_name);
    }
    
    public final function setPhone(int $user_id, string $new_phone) : bool
    {
        return $this->updateValue(self::FIELD_PHONE, $user_id, $new_phone);
    }
    
    public final function setGroup(int $user_id, int $group_id) : bool
    {
        return $this->updateValue(self::FIELD_GROUP_ID, $user_id, $group_id, PDO::PARAM_INT);
    }
    
    public final function setPassword(int $user_id , string $new_password) : bool
    {
        return $this->updateValue(self::FIELD_PASSWORD, $user_id, cafet_generate_hashed_pwd($new_password));
    }
    
    public final function setPermission(int $user_id, string $permission, bool $value) : bool
    {
        $permissions = $this->getPermissions($user_id);
        $permissions[$permission] = $value;
        $_permissions = serialize($permissions);
        
        return $this->updateValue(self::FIELD_PERMISSIONS, $user_id, $_permissions);
    }
    
    public final function unsetPermission(int $user_id, string $permission) : bool
    {
        $permissions = $this->getPermissions($user_id);
        
        unset($permissions[$permission]);
        
        $_permissions = serialize($permissions);
        
        return $this->updateValue(self::FIELD_PERMISSIONS, $user_id, $_permissions);
    }
    
    public final function clearPermissions(int $user_id) : bool
    {
        return $this->updateValue(self::FIELD_PERMISSIONS, $user_id, 'a:0:{}');
    }
    
    /**
     * Delete a product from the database
     *
     * @param int $id
     * @return bool if the query have correctly been completed
     * @since API 0.1.0 (2018)
     */
    public final function deleteUser(int $id): bool
    {
        $this->beginTransaction();
        
        $stmt = $this->connection->prepare('DELETE FROM ' . self::USERS . ' WHERE id = :id');
        $stmt->execute([
            'id' => $id
        ]);
        $this->checkUpdate($stmt, 'unable to delete the user');
        
        $this->commit();
        return true;
    }
}

