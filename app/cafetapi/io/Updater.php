<?php
namespace cafetapi\io;

use cafetapi\exceptions\DuplicateEntryException;
use cafetapi\exceptions\RequestFailureException;
use PDOStatement;

/**
 *
 * @author Damien
 *        
 */
abstract class Updater extends DatabaseConnection
{
    private $inTransaction;
    
    public final function createTransaction()
    {
        if (!$this->connection->inTransaction()) $this->connection->beginTransaction();
        $this->inTransaction = true;
    }
    
    public final function cancelTransaction()
    {
        if ($this->connection->inTransaction()) $this->connection->rollBack();
        if ($this->inTransaction) $this->inTransaction = false;
    }
    
    public final function confirmTransaction()
    {
        $this->connection->commit();
        $this->inTransaction = false;
    }
    
    protected final function beginTransaction()
    {
        if (!$this->inTransaction) $this->connection->beginTransaction();
    }
    
    protected final function commit()
    {
        if (!$this->inTransaction) $this->connection->commit();
    }
    
    /**
     * Check if data have been updated
     *
     * @param PDOStatement $stmt
     *            the statement used for the update
     * @param string $message
     *            the message to throw if no data have been updated
     * @throws RequestFailureException if no data have been updated
     * @since API 1.0.0 (2018)
     */
    protected final function checkUpdate(PDOStatement $stmt, string $message, bool $autorisation_error = false)
    {
        if ($stmt->errorCode() != '00000')
        {
            $backtrace = debug_backtrace()[1];
            
            if ($stmt->errorInfo()[0] == 23000 && $stmt->errorInfo()[1] == 1062)
            {
                throw new DuplicateEntryException($message, null, null, $backtrace['file'], $backtrace['line']);
            }
            
            parent::registerErrorOccurence($stmt);
            
            $sql_error = $stmt->errorInfo()[2];
            
            $this->cancelTransaction();
            
            throw new RequestFailureException($message . ' ' . $sql_error, null, null, $backtrace['file'], $backtrace['line']);
        }
    }
}

