<?php
namespace cafetapi\io;

use cafetapi\exceptions\DuplicateEntryException;
use cafetapi\exceptions\RequestFailureException;
use PDO;
use PDOStatement;

/**
 *
 * @author Damien
 *        
 */
abstract class Updater extends DatabaseConnection
{
    const TABLE_NAME = '';
    const FIELD_ID = 'id';
    
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
    
    /**
     * @deprecated
     */
    protected final function beginTransaction()
    {
        if (!$this->inTransaction) $this->connection->beginTransaction();
    }
    
    /**
     * @deprecated
     */
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
     * @since API 0.1.0 (2018)
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
    
    protected final function updateValue($field, $id, $value, int $value_type = PDO::PARAM_STR)
    {
        if (!$this->inTransaction) $this->connection->beginTransaction();
        
        $statement = $this->connection->prepare('UPDATE ' . static::TABLE_NAME . ' SET `' . $field . '` = :value WHERE ' . static::FIELD_ID . ' = :id');
        $statement->bindValue(':id', $id);
        $statement->bindValue(':value', $value, $value_type);
        $statement->execute();
        $this->checkUpdate($statement, 'unable to update field ' . $field . ' in ' . static::TABLE_NAME);
        $statement->closeCursor();
        
        if (!$this->inTransaction) $this->connection->commit();
        return true;
    }
    
    protected final function updateValues($id, array $key_values_pairs, int $value_type = PDO::PARAM_STR)
    {
        if (!$this->inTransaction) $this->connection->beginTransaction();
        reset($key_values_pairs);
        
        $sql  = 'UPDATE ' . static::TABLE_NAME . ' SET ';
        
        foreach ($key_values_pairs as $key => $value) {
            $sql .= $key . ' = :' . $key;
            if (next($key_values_pairs) !== false) $sql .= ', ';
        }
        
        $sql .= ' WHERE ' . static::FIELD_ID . ' = :id';
        
        $statement = $this->connection->prepare($sql);
        $statement->bindValue(':id', $id);
        foreach ($key_values_pairs as $key => $value) $statement->bindValue(':' . $key, $value, $value_type);
        $statement->execute();
        $this->checkUpdate($statement, 'unable to update fields in ' . static::TABLE_NAME);
        $statement->closeCursor();
        
        if (!$this->inTransaction) $this->connection->commit();
        return true;
    }
    
    protected final function exists(int $id) : bool
    {
        $statement = $this->connection->prepare('SELECT 1 FROM ' . static::TABLE_NAME . ' WHERE ' . static::FIELD_ID . ' = ?');
        $statement->execute([$id]);
        $result = $statement->fetch();
        $statement->closeCursor();
        
        return $result;
    }
}

