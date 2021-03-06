<?php
namespace cafetapi\io;

use cafetapi\data\Calendar;
use cafetapi\data\Reload;
use cafetapi\exceptions\RequestFailureException;
use PDO;
use cafetapi\MailManager;
use cafetapi\Logger;

/**
 *
 * @author Damien
 *        
 */
class ReloadManager extends Updater
{
    const TABLE_NAME = self::RELOADS;
    
    const FIELD_CUSTOMER_ID = 'customer_id';
    /**
     * @deprecated field name change, constant keeped for backwards-compatible
     * FIELD_CUSTOMER_ID SOULD BE USED INSTED
     */
    const FIELD_USER_ID = 'customer_id';
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
            . self::FIELD_ID . ' id, '
            . self::FIELD_USER_BALANCE . ' balance, '
            . self::FIELD_AMOUNT . ' amount, '
            . self::FIELD_DETAILS . ' details, '
            . 'DATE_FORMAT(' . self::FIELD_DATE . ', "%H") hour, '
            . 'DATE_FORMAT(' . self::FIELD_DATE . ', "%i") mins, '
            . 'DATE_FORMAT(' . self::FIELD_DATE . ', "%s") secs, '
            . 'DATE_FORMAT(' . self::FIELD_DATE . ', "%d") day, '
            . 'DATE_FORMAT(' . self::FIELD_DATE . ', "%c") month, '
            . 'DATE_FORMAT(' . self::FIELD_DATE . ', "%Y") year '
            . 'FROM ' . self::RELOADS . ' '
            . 'WHERE ' . self::FIELD_CUSTOMER_ID . ' = :id '
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
        
        $stmt->execute([
            'id' => $client_id
        ]);
        $this->check_fetch_errors($stmt);
        
        $result = [];
        
        while ($stmt->fetch()) $result[] = new Reload($id, $client_id, $details, new Calendar($year, $month, $day, $hour, $mins, $secs), floatval($amount), floatval($balance));
            
        return $result;
    }
    
    public final function getReload(int $reload_id): ?Reload
    {
        $stmt = $this->connection->prepare('SELECT '
            . self::FIELD_ID . ' id, '
            . self::FIELD_CUSTOMER_ID . ' client_id, '
            . self::FIELD_USER_BALANCE . ' balance, '
            . self::FIELD_AMOUNT . ' amount, '
            . self::FIELD_DETAILS . ' details, '
            . 'DATE_FORMAT(' . self::FIELD_DATE . ', "%H") hour, '
            . 'DATE_FORMAT(' . self::FIELD_DATE . ', "%i") mins, '
            . 'DATE_FORMAT(' . self::FIELD_DATE . ', "%s") secs, '
            . 'DATE_FORMAT(' . self::FIELD_DATE . ', "%d") day, '
            . 'DATE_FORMAT(' . self::FIELD_DATE . ', "%c") month, '
            . 'DATE_FORMAT(' . self::FIELD_DATE . ', "%Y") year '
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
        
        $stmt->execute([
            'id' => $reload_id
        ]);
        $this->check_fetch_errors($stmt);
        
        if ($stmt->fetch()) return new Reload($id, $client_id, $details, new Calendar($year, $month, $day, $hour, $mins, $secs), floatval($amount), floatval($balance));
        else return null;
    }
    
    public final function getReloads(): array
    {
        $stmt = $this->connection->prepare('SELECT '
            . self::FIELD_ID . ' id, '
            . self::FIELD_CUSTOMER_ID . ' client_id, '
            . self::FIELD_USER_BALANCE . ' balance, '
            . self::FIELD_AMOUNT . ' amount, '
            . self::FIELD_DETAILS . ' details, '
            . 'DATE_FORMAT(' . self::FIELD_DATE . ', "%H") hour, '
            . 'DATE_FORMAT(' . self::FIELD_DATE . ', "%i") mins, '
            . 'DATE_FORMAT(' . self::FIELD_DATE . ', "%s") secs, '
            . 'DATE_FORMAT(' . self::FIELD_DATE . ', "%d") day, '
            . 'DATE_FORMAT(' . self::FIELD_DATE . ', "%c") month, '
            . 'DATE_FORMAT(' . self::FIELD_DATE . ', "%Y") year '
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
        
        $result = [];
        
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
     * @since API 0.1.0 (2018)
     */
    public final function saveReload(int $client_id, float $amount, string $comment): bool
    {
        $this->beginTransaction();
        
        $client = ClientManager::getInstance()->getClient($client_id);
        if (!$client) throw new RequestFailureException('Unexisting client');
        
        $stmt = $this->connection->prepare('INSERT INTO ' . self::RELOADS . '(' . self::FIELD_CUSTOMER_ID . ', ' . self::FIELD_AMOUNT . ', ' . self::FIELD_DETAILS . ') VALUES (:client,:amount,:details)');
        $stmt->execute([
            'client' => $client_id,
            'amount' => $amount,
            'details' => $comment
        ]);
        $this->checkUpdate($stmt, 'unable to save balance reload');
        $reload_id = $this->connection->lastInsertId();
        
        $this->commit();
        try {
            if ($client->getMailPreference('reload_notice')) MailManager::reloadNotice($client, $this->getReload($reload_id))->send();
        } catch (\Exception | \Error $e) {
            Logger::log($e);
        }
        return true;
    }
}

