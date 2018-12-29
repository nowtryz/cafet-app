<?php
namespace cafetapi\io;

use PDO;
use PDOException;
use PDOStatement;

/**
 *
 * @author Damien
 * @since API 1.0.0 (2018)
 */
class DatabaseConnection
{
    private static $instance = null;

    /**
     * Common connection for all objects
     *
     * @var PDO
     * @since API 1.0.0 (2018)
     */
    protected static $globalConnection;

    /**
     * Object specifique connection
     *
     * @var PDO
     * @since API 1.0.0 (2018)
     */
    protected $connection;
    protected static $database;
    private static $driver;
    private static $host;
    private static $username;
    private static $password;
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
     * @param array $db_infos
     *            the array containing login information
     * @since API 1.0.0 (2018)
     */
    private static final function init(array $db_infos)
    {
        if (! (isset($db_infos['driver']) && isset($db_infos['host']) && isset($db_infos['database']) && isset($db_infos['username']) && isset($db_infos['password'])))
            cafet_throw_error('01-001');

        self::$driver = $db_infos['driver'];
        self::$host = $db_infos['host'];
        self::$database = $db_infos['database'];
        self::$username = $db_infos['username'];
        self::$password = $db_infos['password'];

        $dsn  = self::$driver . ':host=' . self::$host . ';dbname=' . self::$database . ';charset=utf8';
        $dsn .= isset($db_infos['port']) ? ';port=' . $db_infos['port'] : '';

        try {
            self::$globalConnection = new PDO($dsn, self::$username, self::$password);
            self::$globalConnection->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_TO_STRING);
            self::$globalConnection->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
            DatabaseIntegrity::checkTables();
            DatabaseIntegrity::checkTriggers();
        } catch (PDOException $e) {
            $backtrace = debug_backtrace()[1];
            cafet_log($e->getMessage());
            cafet_throw_error('01-001', 'unable to connect to the database: ' . $e->getMessage(), $backtrace['file'], $backtrace['line']);
        }
    }

    

    protected static final function registerErrorOccurence(PDOStatement $stmt)
    {
        $array = $stmt->errorInfo();
        $array[] = $stmt->queryString;
        self::$lastQueryErrors[] = $array;
        cafet_log('SQL error: [' . $array[0] . '] ' . $array[2] . ' (with query:' . $array[3] . ')');
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
     * @since API 1.0.0 (2018)
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
     * @since API 1.0.0 (2018)
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
     * @since API 1.0.0 (2018)
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
     * @since API 1.0.0 (2018)
     */
    public static final function getPDOObject(): PDO
    {
        return self::$globalConnection;
    }

    /**
     * Inialises connection if it haven't been initialised yet
     *
     * @since API 1.0.0 (2018)
     */
    public static final function initIfNotAlready()
    {
        if (! defined('DB_INFO'))
            cafet_load_conf_file();
        if (! isset(self::$globalConnection))
            self::init(DB_INFO);
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
     * @param array $db_infos
     *            an array containing needed information to connect to the database
     * @param bool $force_new_connection
     *            if this object must create a new connection with given configuration
     * @since API 1.0.0 (2018)
     */
    protected function __construct(array $db_infos = DB_INFO, bool $force_new_connection = false)
    {
        if (! $force_new_connection) {
            if (! isset(self::$globalConnection))
                self::init($db_infos);
            $this->connection = self::$globalConnection;
        } else {
            if (! (isset($db_infos['driver']) && isset($db_infos['host']) && isset($db_infos['database']) && isset($db_infos['username']) && isset($db_infos['password'])))
                cafet_throw_error('01-001');

            $dsn  = self::$driver . ':host=' . self::$host . ';dbname=' . self::$database . ';charset=utf8';
            $dsn .= isset($db_infos['port']) ? ';port=' . $db_infos['port'] : '';
    
            try {
                $this->connection = new PDO($dsn, self::$username, self::$password);
                $this->connection->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_TO_STRING);
                $this->connection->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
            } catch (PDOException $e) {
                $backtrace = debug_backtrace()[0];
                cafet_log($e->getMessage());
                cafet_throw_error('01-001', 'unable to connect to the database', $backtrace['file'], $backtrace['line']);
            }
        }
    }

    /**
     * Execute a query directly on the database with this object specific connection.
     * While an error occured, it can be get with the getLastQueryError() function.
     *
     * @param string $sql
     *            the query
     * @return PDOStatement|null PDOStatement on succes, null on faillur
     * @see DatabaseConnection::getLastQueryError()
     * @since API 1.0.0 (2018)
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

