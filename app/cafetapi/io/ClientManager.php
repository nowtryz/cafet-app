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
    private static $instance;
    
    /**
     * Get singleton object
     * @return DataUpdater the singleton of this class
     */
    public static function getInstance() : ClientManager
    {
        if(self::$instance === null) self::$instance = new ClientManager();
        return self::$instance;
    }
    
    public final function getClients(): array
    {
        $stmt = $this->connection->prepare('SELECT '
            . 'ID id, '
            . 'Email email, '
            . 'Pseudo alias, '
            . 'Nom fname, '
            . 'Prenom sname, '
            . 'adherent member, '
            . 'Credit balance, '
            . 'Annee regyear '
            . 'FROM ' . self::USERS . ' u '
            . 'WHERE u.SU = 0 '
            . 'ORDER BY u.Prenom');
        
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
        
        $result = array();
        
        while ($stmt->fetch()) $result[] = new Client($id, $email, $alias, $familyNane, $surname, $member, floatval($balance), $registrationYear);
            
        return $result;
    }
    
    public final function getClient(int $id): ?Client
    {
        $stmt = $this->connection->prepare('SELECT '
            . 'ID id, '
            . 'Email email, '
            . 'Pseudo alias, '
            . 'Nom fname, '
            . 'Prenom sname, '
            . 'adherent member, '
            . 'Credit balance, '
            . 'Annee regyear '
            . 'FROM ' . self::USERS . ' '
            . 'WHERE id = :id ');
        
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
        
        $stmt->execute(array(
            'id' => $id
        ));
        $this->check_fetch_errors($stmt);
        
        if ($stmt->fetch()) return new Client($id, $email, $alias, $familyNane, $surname, $member, floatval($balance), $registrationYear);
            
        else return NULL;
    }
    
    public final function searchClient(string $expression): array
    {
        $stmt = $this->connection->prepare('SELECT '
            . 'ID id, '
            . 'Email email, '
            . 'Pseudo alias, '
            . 'Nom fname, '
            . 'Prenom sname, '
            . 'adherent member, '
            . 'Credit balance, '
            . 'Annee regyear '
            . 'FROM ' . self::USERS . ' u '
            . 'WHERE u.SU = 0 '
            . 'AND (u.Pseudo LIKE :expression '
            . 'OR u.Nom LIKE :expression '
            . 'OR u.Prenom LIKE :expression) '
            . 'ORDER BY u.Prenom');
        
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
        
        $stmt->execute(array(
            'expression' =>  $search
        ));
        $this->check_fetch_errors($stmt);
        
        $result = array();
        
        while ($stmt->fetch()) $result[] = new Client($id, $email, $alias, $familyNane, $surname, $member, floatval($balance), $registrationYear);
            
        return $result;
    }
}

