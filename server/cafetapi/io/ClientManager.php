<?php
namespace cafetapi\io;

use cafetapi\data\Client;
use PDO;

/**
 *
 * @author Damien
 *        
 */
class ClientManager extends Updater
{
    const TABLE_NAME = self::CLIENTS;
    
    const FIELD_ID = 'id';
    const FIELD_USER_ID = 'user_id';
    const FIELD_MEMBER = 'member';
    const FIELD_BALANCE = 'balance';

    private static $instance;
    
    /**
     * Get singleton object
     * @return ClientManager the singleton of this class
     */
    public static function getInstance() : ClientManager
    {
        if(self::$instance === null) self::$instance = new ClientManager();
        return self::$instance;
    }
    
    public final function getClients(): array
    {
        $stmt = $this->connection->prepare('SELECT '
            . 'u.'. UserManager::FIELD_ID . ' id, '
            . 'u.'. UserManager::FIELD_EMAIL . ' email, '
            . 'u.'. UserManager::FIELD_USERNAME . ' alias, '
            . 'u.'. UserManager::FIELD_FAMILYNAME . ' fname, '
            . 'u.'. UserManager::FIELD_FIRSTNAME . ' sname, '
            . 'c.'. self::FIELD_MEMBER . ' member, '
            . 'c.'. self::FIELD_BALANCE . ' balance, '
            . 'DATE_FORMAT(u.'. UserManager::FIELD_REGISTRATION . ', "%Y") regyear '
            . 'FROM ' . parent::CLIENTS . ' c '
            . 'INNER JOIN ' . parent::USERS . ' u '
            . 'ON c.' . self::FIELD_USER_ID . ' = u.' . UserManager::FIELD_ID . ' '
            . 'ORDER BY u.' . UserManager::FIELD_FIRSTNAME);
        
        $member = false;
        $id = $registrationYear = 0;
        $email = $alias = $familyNane = $surname = $balance = '';
        
        $stmt->bindColumn('id', $id, PDO::PARAM_INT);
        $stmt->bindColumn('email', $email, PDO::PARAM_STR);
        $stmt->bindColumn('alias', $alias, PDO::PARAM_STR);
        $stmt->bindColumn('fname', $familyNane, PDO::PARAM_STR);
        $stmt->bindColumn('sname', $surname, PDO::PARAM_STR);
        $stmt->bindColumn('member', $member, PDO::PARAM_BOOL);
        $stmt->bindColumn('balance', $balance, PDO::PARAM_STR);
        $stmt->bindColumn('regyear', $registrationYear, PDO::PARAM_INT);
        
        $stmt->execute();
        $this->check_fetch_errors($stmt);
        
        $result = [];
        
        while ($stmt->fetch()) $result[] = new Client($id, $email, $alias, $familyNane, $surname, $member, floatval($balance), $registrationYear);
        
        $stmt->closeCursor();
        return $result;
    }
    
    public final function getClient(int $id): ?Client
    {
        $stmt = $this->connection->prepare('SELECT '
            . 'u.'. UserManager::FIELD_ID . ' id, '
            . 'u.'. UserManager::FIELD_EMAIL . ' email, '
            . 'u.'. UserManager::FIELD_USERNAME . ' alias, '
            . 'u.'. UserManager::FIELD_FAMILYNAME . ' fname, '
            . 'u.'. UserManager::FIELD_FIRSTNAME . ' sname, '
            . 'c.'. self::FIELD_MEMBER . ' member, '
            . 'c.'. self::FIELD_BALANCE . ' balance, '
            . 'DATE_FORMAT(u.'. UserManager::FIELD_REGISTRATION . ', "%Y") regyear '
            . 'FROM ' . parent::CLIENTS . ' c '
            . 'INNER JOIN ' . parent::USERS . ' u '
            . 'ON c.' . self::FIELD_USER_ID . ' = u.' . UserManager::FIELD_ID . ' '
            . 'WHERE u.' . UserManager::FIELD_ID . ' = :id ');
        
        $member = false;
        $registrationYear = 0;
        $email = $alias = $familyNane = $surname = $balance = '';
        
        $stmt->bindColumn('id', $id, PDO::PARAM_INT);
        $stmt->bindColumn('email', $email, PDO::PARAM_STR);
        $stmt->bindColumn('alias', $alias, PDO::PARAM_STR);
        $stmt->bindColumn('fname', $familyNane, PDO::PARAM_STR);
        $stmt->bindColumn('sname', $surname, PDO::PARAM_STR);
        $stmt->bindColumn('member', $member, PDO::PARAM_BOOL);
        $stmt->bindColumn('balance', $balance, PDO::PARAM_STR);
        $stmt->bindColumn('regyear', $registrationYear, PDO::PARAM_INT);
        
        $stmt->execute([
            'id' => $id
        ]);
        $this->check_fetch_errors($stmt);
        
        if ($stmt->fetch()) return new Client($id, $email, $alias, $familyNane, $surname, $member, floatval($balance), $registrationYear);
            
        else return NULL;
    }
    
    public final function searchClient(string $expression): array
    {
        $stmt = $this->connection->prepare('SELECT '
            . 'u.'. UserManager::FIELD_ID . ' id, '
            . 'u.'. UserManager::FIELD_EMAIL . ' email, '
            . 'u.'. UserManager::FIELD_USERNAME . ' alias, '
            . 'u.'. UserManager::FIELD_FAMILYNAME . ' fname, '
            . 'u.'. UserManager::FIELD_FIRSTNAME . ' sname, '
            . 'c.'. self::FIELD_MEMBER . ' member, '
            . 'c.'. self::FIELD_BALANCE . ' balance, '
            . 'DATE_FORMAT(u.'. UserManager::FIELD_REGISTRATION . ', "%Y") regyear '
            . 'FROM ' . parent::CLIENTS . ' c '
            . 'INNER JOIN ' . parent::USERS . ' u '
            . 'ON c.' . self::FIELD_USER_ID . ' = u.' . UserManager::FIELD_ID . ' '
            . 'WHERE (u.' . UserManager::FIELD_USERNAME . ' LIKE :expression '
            . 'OR u.' . UserManager::FIELD_FAMILYNAME . ' LIKE :expression '
            . 'OR u.' . UserManager::FIELD_FIRSTNAME . ' LIKE :expression) '
            . 'ORDER BY u.' . UserManager::FIELD_FIRSTNAME);
        
        $id = $registrationYear = 0;
        $email = $alias = $familyNane = $surname = $balance = '';
        $member = false;
        
        $stmt->bindColumn('id', $id, PDO::PARAM_INT);
        $stmt->bindColumn('email', $email, PDO::PARAM_STR);
        $stmt->bindColumn('alias', $alias, PDO::PARAM_STR);
        $stmt->bindColumn('fname', $familyNane, PDO::PARAM_STR);
        $stmt->bindColumn('sname', $surname, PDO::PARAM_STR);
        $stmt->bindColumn('member', $member, PDO::PARAM_BOOL);
        $stmt->bindColumn('balance', $balance, PDO::PARAM_STR);
        $stmt->bindColumn('regyear', $registrationYear, PDO::PARAM_INT);
        
        $search = "%$expression%";
        
        $stmt->execute([
            'expression' =>  $search
        ]);
        $this->check_fetch_errors($stmt);
        
        $result = [];
        
        while ($stmt->fetch()) $result[] = new Client($id, $email, $alias, $familyNane, $surname, $member, floatval($balance), $registrationYear);
            
        return $result;
    }
    
    public final function setMember(int $client_id, bool $member)
    {
        $this->updateValue(self::FIELD_MEMBER, $client_id, $member, PDO::PARAM_BOOL);
    }
    
    public final function createCustomer(int $user_id)
    {
        $this->beginTransaction();
        $statement = $this->connection->prepare('INSERT INTO ' . self::CLIENTS . ' (' . self::FIELD_USER_ID . ') VALUES (?)');
        $statement->execute([$user_id]);
        $this->checkUpdate($statement, 'unable to create customer');
        $statement->closeCursor();
        $this->commit();
        return true;
    }
}

