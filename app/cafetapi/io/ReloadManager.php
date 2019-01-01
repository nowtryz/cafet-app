<?php
namespace cafetapi\io;

use cafetapi\data\Calendar;
use cafetapi\data\Reload;
use PDO;

/**
 *
 * @author Damien
 *        
 */
class ReloadManager extends Updater
{
    const FIELD_USER_ID = 'user_id';
    const FIELD_AMOUNT = 'amount';
    const FIELD_USER_BALANCE = 'user_balance';
    const FIELD_DETAILS = 'details';
    const FIELD_DATE= 'date';
    
    private static $instance;
    
    /**
     * Get singleton object
     * @return ReloadManager the singleton of this class
     */
    public static function getInstance() : ReloadManager
    {
        if(self::$instance === null) self::$instance = new ReloadManager();
        return self::$instance;
    }
    
    public final function getClientReloads(int $client_id): array
    {
        $stmt = $this->connection->prepare('SELECT '
            . 'id id, '
            . 'user_balance balance, '
            . 'amount amount, '
            . 'details details, '
            . 'DATE_FORMAT(date, "%H") hour, '
            . 'DATE_FORMAT(date, "%i") mins, '
            . 'DATE_FORMAT(date, "%s") secs, '
            . 'DATE_FORMAT(date, "%d") day, '
            . 'DATE_FORMAT(date, "%c") month, '
            . 'DATE_FORMAT(date, "%Y") year '
            . 'FROM ' . self::RELOADS . ' '
            . 'WHERE user_id = :id '
            . 'ORDER BY date DESC');
        
        $id = $hour = $mins = $secs = $day = $month = $year = 0;
        $balance = $amount = $details = '';
        
        $stmt->bindColumn('id', $id, PDO::PARAM_INT);
        $stmt->bindColumn('balance', $balance, PDO::PARAM_STR);
        $stmt->bindColumn('amount', $amount, PDO::PARAM_STR);
        $stmt->bindColumn('details', $details, PDO::PARAM_STR);
        $stmt->bindColumn('hour', $hour, PDO::PARAM_INT);
        $stmt->bindColumn('mins', $mins, PDO::PARAM_INT);
        $stmt->bindColumn('secs', $secs, PDO::PARAM_INT);
        $stmt->bindColumn('day', $day, PDO::PARAM_INT);
        $stmt->bindColumn('month', $month, PDO::PARAM_INT);
        $stmt->bindColumn('year', $year, PDO::PARAM_INT);
        
        $stmt->execute(array(
            'id' => $client_id
        ));
        $this->check_fetch_errors($stmt);
        
        $result = array();
        
        while ($stmt->fetch()) $result[] = new Reload($id, $client_id, $details, new Calendar($year, $month, $day, $hour, $mins, $secs), floatval($amount), floatval($balance));
            
        return $result;
    }
    
    public final function getReload(int $reload_id): ?Reload
    {
        $stmt = $this->connection->prepare('SELECT '
            . 'id id, '
            . 'user_id client_id, '
            . 'user_balance balance, '
            . 'amount amount, '
            . 'details details, '
            . 'DATE_FORMAT(date, "%H") hour, '
            . 'DATE_FORMAT(date, "%i") mins, '
            . 'DATE_FORMAT(date, "%s") secs, '
            . 'DATE_FORMAT(date, "%d") day, '
            . 'DATE_FORMAT(date, "%c") month, '
            . 'DATE_FORMAT(date, "%Y") year '
            . 'FROM ' . self::RELOADS . ' '
            . 'WHERE id = :id '
            . 'ORDER BY date DESC');
        
        $id = $client_id = $hour = $mins = $secs = $day = $month = $year = 0;
        $balance = $amount = $details = '';
        
        $stmt->bindColumn('id', $id, PDO::PARAM_INT);
        $stmt->bindColumn('client_id', $client_id, PDO::PARAM_INT);
        $stmt->bindColumn('balance', $balance, PDO::PARAM_STR);
        $stmt->bindColumn('amount', $amount, PDO::PARAM_STR);
        $stmt->bindColumn('details', $details, PDO::PARAM_STR);
        $stmt->bindColumn('hour', $hour, PDO::PARAM_INT);
        $stmt->bindColumn('mins', $mins, PDO::PARAM_INT);
        $stmt->bindColumn('secs', $secs, PDO::PARAM_INT);
        $stmt->bindColumn('day', $day, PDO::PARAM_INT);
        $stmt->bindColumn('month', $month, PDO::PARAM_INT);
        $stmt->bindColumn('year', $year, PDO::PARAM_INT);
        
        $stmt->execute(array(
            'id' => $reload_id
        ));
        $this->check_fetch_errors($stmt);
        
        if ($stmt->fetch()) return new Reload($id, $client_id, $details, new Calendar($year, $month, $day, $hour, $mins, $secs), floatval($amount), floatval($balance));
        else return null;
    }
    
    public final function getReloads(): array
    {
        $stmt = $this->connection->prepare('SELECT '
            . 'id id, '
            . 'user_id client_id, '
            . 'user_balance balance, '
            . 'amount amount, '
            . 'details details, '
            . 'DATE_FORMAT(date, "%H") hour, '
            . 'DATE_FORMAT(date, "%i") mins, '
            . 'DATE_FORMAT(date, "%s") secs, '
            . 'DATE_FORMAT(date, "%d") day, '
            . 'DATE_FORMAT(date, "%c") month, '
            . 'DATE_FORMAT(date, "%Y") year '
            . 'FROM ' . self::RELOADS . ' '
            . 'ORDER BY date DESC');
        
        $id = $client_id = $hour = $mins = $secs = $day = $month = $year = 0;
        $balance = $amount = $details = '';
        
        $stmt->bindColumn('id', $id, PDO::PARAM_INT);
        $stmt->bindColumn('client_id', $client_id, PDO::PARAM_INT);
        $stmt->bindColumn('balance', $balance, PDO::PARAM_STR);
        $stmt->bindColumn('amount', $amount, PDO::PARAM_STR);
        $stmt->bindColumn('details', $details, PDO::PARAM_STR);
        $stmt->bindColumn('hour', $hour, PDO::PARAM_INT);
        $stmt->bindColumn('mins', $mins, PDO::PARAM_INT);
        $stmt->bindColumn('secs', $secs, PDO::PARAM_INT);
        $stmt->bindColumn('day', $day, PDO::PARAM_INT);
        $stmt->bindColumn('month', $month, PDO::PARAM_INT);
        $stmt->bindColumn('year', $year, PDO::PARAM_INT);
        
        $stmt->execute();
        $this->check_fetch_errors($stmt);
        
        $result = array();
        
        while ($stmt->fetch()) $result[] = new Reload($id, $client_id, $details, new Calendar($year, $month, $day, $hour, $mins, $secs), floatval($amount), floatval($balance));
            
        return $result;
    }
    
    /**
     * Save a balance reloading for the specified client
     *
     * @param int $client_id
     * @param float $amount
     * @param string $comment
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function saveReload(int $client_id, float $amount, string $comment): bool
    {
        $this->beginTransaction();
        
        $stmt = $this->connection->prepare('INSERT INTO ' . self::RELOADS . '(user_id, amount, details) VALUES (:client,:amount,:details)');
        $stmt->execute(array(
            'client' => $client_id,
            'amount' => $amount,
            'details' => $comment
        ));
        $this->checkUpdate($stmt, 'unable to save balance reload');
        
        $this->commit();
        return true;
    }
}

