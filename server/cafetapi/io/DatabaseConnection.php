<?php
namespace cafetapi\io;

use cafetapi\config\Database;
use Exception;
use PDO;
use PDOException;
use PDOStatement;
use cafetapi\Logger;

/**
 *
 * @author Damien
 * @since API 0.1.0 (2018)
 */
class DatabaseConnection
{
    private static $instance = null;

    /**
     * Common connection for all objects
     *
     * @var PDO
     * @since API 0.1.0 (2018)
     */
    protected static $globalConnection;

    /**
     * Object specifique connection
     *
     * @var PDO
     * @since API 0.1.0 (2018)
     */
    protected $connection;
    protected static $database;
    private static $driver;
    private static $host;
    private static $username;
    private static $password;
    private static $port;
    private static $lastQueryErrors = array();

    const CONFIG = 'cafet_config';
    const RELOADS = 'cafet_balance_reloads';
    const EXPENSES = 'cafet_expenses';
    const FORMULAS = 'cafet_formulas';
    const FORMULAS_BOUGHT = 'cafet_formulas_bought';
    const FORMULAS_BOUGHT_PRODUCTS = 'cafet_formulas_bought_products';
    const FORMULAS_CHOICES = 'cafet_formulas_choices';
    const FORMULAS_CHOICES_PRODUCTS = 'cafet_formulas_choices_products';
    const FORMULAS_EDITS = 'cafet_formulas_edits';
    const PRODUCTS = 'cafet_products';
    const PRODUCTS_BOUGHT = 'cafet_products_bought';
    const PRODUCTS_EDITS = 'cafet_products_edits';
    const PRODUCTS_GROUPS = 'cafet_products_groups';
    const REPLENISHMENTS = 'cafet_replenishments';
    const CLIENTS = 'cafet_customers';
    const USERS = 'cafet_users';

    /**
     * Initialise connection for this class and all its children
     *
     * @since API 0.1.0 (2018)
     */
    private static final function init()
    {
        try {
            self::$driver = Database::driver;
            self::$host = Database::host;
            self::$database = Database::database;
            self::$username = Database::username;
            self::$password = Database::password;
            self::$port = Database::port;
        } catch (Exception $e) {
            Logger::throwError('01-001', $e->getMessage());
        }

        $dsn  = self::$driver . ':host=' . self::$host . ';dbname=' . self::$database . ';charset=utf8mb4';
        $dsn .= isset(self::$port) ? ';port=' . self::$port : '';

        try {
            self::$globalConnection = new PDO($dsn, self::$username, self::$password);
            self::$globalConnection->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_TO_STRING);
            self::$globalConnection->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);

            $integrityCheck = new DatabaseIntegrity();
            $integrityCheck->checkTables();
            $integrityCheck->checkTriggers();
        } catch (PDOException $e) {
            $backtrace = debug_backtrace()[1];
            Logger::log($e->getMessage());
            Logger::throwError('01-001', 'unable to connect to the database: ' . $e->getMessage(), $backtrace['file'], $backtrace['line']);
        }
    }



    protected static final function registerErrorOccurence(PDOStatement $stmt)
    {
        $array = $stmt->errorInfo();
        $array[] = $stmt->queryString;
        self::$lastQueryErrors[] = $array;
        if (Config::production) Logger::log('The following error occurred during an SQL query, your database may be outdated.');
        Logger::log('SQL error: [' . $array[0] . '] ' . $array[2] . ' (with query:' . $array[3] . ')');
    }

    protected final function check_fetch_errors(PDOStatement $stmt)
    {
        if ($stmt->errorCode() != '00000') self::registerErrorOccurence($stmt);
    }

    /**
     * Execute a query directly on the database.
     * No need to use an object. While an error occured, it can be get with the getLastQueryError() function
     *
     * @param string $sql
     *            the query
     * @return bool true if success, false on failure
     * @see DatabaseConnection::getLastQueryError()
     * @since API 0.1.0 (2018)
     */
    public final static function query(string $sql, $parameters = array()): bool
    {
        $stmt = self::$globalConnection->prepare($sql);
        $bool = $stmt->execute($parameters);
        if (! $bool)
            self::registerErrorOccurence($stmt);
        $stmt->closeCursor();

        return $bool;
    }

    /**
     * Return the last query errors :
     *
     * <br />0 SQLSTATE error code (a five characters alphanumeric identifier defined in the ANSI SQL standard).
     * <br />1 Driver specific error code.
     * <br />2 Driver specific error message.
     * <br />3 Request.
     *
     * @return array the error array (empty if no error occured)
     * @since API 0.1.0 (2018)
     */
    public static final function getLastQueryErrors(): array
    {
        return self::$lastQueryErrors;
    }

    /**
     * Return the last query error :
     *
     * <br />0 SQLSTATE error code (a five characters alphanumeric identifier defined in the ANSI SQL standard).
     * <br />1 Driver specific error code.
     * <br />2 Driver specific error message.
     * <br />3 Request.
     *
     * @return array the error array (empty if no error occured)
     * @since API 0.1.0 (2018)
     */
    public static final function getLastQueryError(): array
    {
        $count = count(self::$lastQueryErrors);
        return $count > 0 ? self::$lastQueryErrors[$count - 1] : array();
    }

    /**
     * Get the PDO object used by the class
     *
     * @return PDO the PDO object
     * @since API 0.1.0 (2018)
     */
    public static final function getPDOObject(): PDO
    {
        return self::$globalConnection;
    }

    /**
     * Inialises connection if it haven't been initialised yet
     *
     * @since API 0.1.0 (2018)
     */
    public static final function initIfNotAlready()
    {
        if (! isset(self::$globalConnection))
            self::init();
    }

    /**
     * Get singleton object
     * @return DatabaseConnection the singleton of this class
     */
    public static function getDatabaseConnectionInstance() : DatabaseConnection
    {
        if(self::$instance === null) self::$instance = new DatabaseConnection();
        return self::$instance;
    }

    /**
     * Constructor for DatabaseConnection
     *
     * @since API 0.1.0 (2018)
     */
    protected function __construct()
    {
        if (! isset(self::$globalConnection)) self::init();
        $this->connection = self::$globalConnection;
    }

    /**
     * Execute a query directly on the database with this object specific connection.
     * While an error occured, it can be get with the getLastQueryError() function.
     *
     * @param string $sql
     *            the query
     * @return PDOStatement|null PDOStatement on succes, null on faillur
     * @see DatabaseConnection::getLastQueryError()
     * @since API 0.1.0 (2018)
     */
    public final function execute(string $sql, $parametters = array()): ?PDOStatement
    {
        $stmt = $this->connection->prepare($sql);
        $bool = $stmt->execute($parametters);

        if (! $bool) {
            self::registerErrorOccurence($stmt);
            return null;
        }

        $stmt->closeCursor();

        return $stmt;
    }
}

