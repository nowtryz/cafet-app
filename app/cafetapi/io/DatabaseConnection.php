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
    private static $driver;
    private static $host;
    private static $database;
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
            self::checkTables();
            self::checkTriggers();
        } catch (PDOException $e) {
            $backtrace = debug_backtrace()[1];
            cafet_log($e->getMessage());
            cafet_throw_error('01-001', 'unable to connect to the database: ' . $e->getMessage(), $backtrace['file'], $backtrace['line']);
        }
    }

    /**
     * Cheks the existence of every cafetAPI tables an create those doesn't exist
     *
     * @since API 1.0.0 (2018)
     */
    private static final function checkTables()
    {
        $stmt = self::$globalConnection->query('SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = "BASE TABLE" AND TABLE_SCHEMA = "' . self::$database . '"');
        $tables = array();
        while ($row = $stmt->fetch()) $tables[] = $row[0];
        $stmt->closeCursor();

        $needed_tables = array(
            self::CONFIG,
            self::RELOADS,
            self::EXPENSES,
            self::FORMULAS,
            self::FORMULAS_BOUGHT,
            self::FORMULAS_BOUGHT_PRODUCTS,
            self::FORMULAS_CHOICES,
            self::FORMULAS_CHOICES_PRODUCTS,
            self::FORMULAS_EDITS,
            self::PRODUCTS,
            self::PRODUCTS_BOUGHT,
            self::PRODUCTS_EDITS,
            self::PRODUCTS_GROUPS,
            self::REPLENISHMENTS,
            self::USERS
        );

        if(count(array_intersect($tables, $needed_tables)) >= count($needed_tables)) return;

        self::query('SET foreign_key_checks = 0');

        $CONFIG = self::CONFIG;
        $RELOADS = self::RELOADS;
        $EXPENSES = self::EXPENSES;
        $FORMULAS = self::FORMULAS;
        $FORMULAS_BOUGHT = self::FORMULAS_BOUGHT;
        $FORMULAS_BOUGHT_PRODUCTS = self::FORMULAS_BOUGHT_PRODUCTS;
        $FORMULAS_CHOICES = self::FORMULAS_CHOICES;
        $FORMULAS_CHOICES_PRODUCTS = self::FORMULAS_CHOICES_PRODUCTS;
        $FORMULAS_EDITS = self::FORMULAS_EDITS;
        $PRODUCTS = self::PRODUCTS;
        $PRODUCTS_BOUGHT = self::PRODUCTS_BOUGHT;
        $PRODUCTS_EDITS = self::PRODUCTS_EDITS;
        $PRODUCTS_GROUPS = self::PRODUCTS_GROUPS;
        $REPLENISHMENTS = self::REPLENISHMENTS;
        $USERS = self::USERS;
        $CHARSET='utf8mb4';
        $COLLATE='utf8mb4_unicode_ci';

        if (! in_array(self::RELOADS, $tables)) {
            self::query(
<<<EOSQL
CREATE TABLE IF NOT EXISTS `$RELOADS` (
    `id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT "reload\'s id",
    `user_id` bigint(11) NOT NULL COMMENT "user\'s id",
    `amount` float(10,2) NOT NULL COMMENT "reload\'s amount",
    `user_balance` float(10,2) NOT NULL DEFAULT "0.00" COMMENT " user\'s balance after transaction",
    `details` varchar(255) NOT NULL DEFAULT "APP/UNKNOW" COMMENT "reload method",
    `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "transaction\'s date",
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `$USERS`(`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=`$CHARSET` COLLATE `$COLLATE`
EOSQL
            );
        }

        if (! in_array(self::CONFIG, $tables)) {
            self::query(
<<<EOSQL
CREATE TABLE IF NOT EXISTS `$CONFIG` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT "id",
    `name` varchar(255) NOT NULL COMMENT "configuration key",
    `value` text NOT NULL COMMENT "configuration value",
    `edit` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "last edition",
    PRIMARY KEY (`id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=`$CHARSET` COLLATE `$COLLATE`
EOSQL
            );
        }

        if (! in_array(self::EXPENSES, $tables)) {
            self::query(
<<<EOSQL
CREATE TABLE IF NOT EXISTS `$EXPENSES` (
    `id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT "transaction\'s id",
    `user_id` bigint(11) NOT NULL COMMENT "user\'s id",
    `user_balance` float(10,2) NOT NULL DEFAULT "0.00" COMMENT "user\'s balance after transaction",
    `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "transaction\'s date",
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `$USERS`(`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=`$CHARSET` COLLATE `$COLLATE`
EOSQL
            );
        }

        if (! in_array(self::FORMULAS, $tables)) {
            self::query(
<<<EOSQL
CREATE TABLE IF NOT EXISTS `$FORMULAS` (
    `id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT "formula\'s id",
    `image` longtext COMMENT "formula\'s image",
    `viewable` tinyint(1) NOT NULL DEFAULT "1" COMMENT "is the formula viewable",
    `last_edit` bigint(20) NOT NULL DEFAULT "0" COMMENT "last formula edit id",
    PRIMARY KEY (`id`),
    FOREIGN KEY (`last_edit`) REFERENCES `$FORMULAS_EDITS`(`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=`$CHARSET` COLLATE `$COLLATE`
EOSQL
            );
        }

        if (! in_array(self::FORMULAS_BOUGHT, $tables)) {
            self::query(
<<<EOSQL
CREATE TABLE IF NOT EXISTS `$FORMULAS_BOUGHT` (
    `id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT "transaction id",
    `expense_id` bigint(11) NOT NULL COMMENT "expense id",
    `formula_id` bigint(11) NOT NULL COMMENT "formula id",
    `edit_id` bigint(20) NULL DEFAULT NULL COMMENT "formula edit id at the transaction date",
    `user_id` bigint(11) NOT NULL COMMENT "user id",
    `quantity` int(11) NOT NULL DEFAULT "1" COMMENT "quantity",
    `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "transaction date",
    PRIMARY KEY (`id`),
    FOREIGN KEY (`expense_id`) REFERENCES `$EXPENSES`(`id`),
    FOREIGN KEY (`edit_id`) REFERENCES `$FORMULAS_EDITS`(`id`),
    FOREIGN KEY (`user_id`) REFERENCES `$USERS`(`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=`$CHARSET` COLLATE `$COLLATE`
EOSQL
            );
        }

        if (! in_array(self::FORMULAS_BOUGHT_PRODUCTS, $tables)) {
            self::query(
<<<EOSQL
CREATE TABLE IF NOT EXISTS `$FORMULAS_BOUGHT_PRODUCTS` (
    `id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT "id",
     `transaction_id` bigint(11) NOT NULL COMMENT "id in formulas bought",
     `product_id` bigint(11) NOT NULL COMMENT "product id",
     `product_edit` BIGINT(20) NULL COMMENT "the product edit",
     `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "transaction date",
     PRIMARY KEY (`id`),
     FOREIGN KEY (`transaction_id`) REFERENCES `$FORMULAS_BOUGHT`(`id`),
     FOREIGN KEY (`product_edit`) REFERENCES `cafet_products_edits`(`id`)
   ) ENGINE=InnoDB DEFAULT CHARSET=`$CHARSET` COLLATE `$COLLATE`
EOSQL
            );
        }

        if (! in_array(self::FORMULAS_CHOICES, $tables)) {
            self::query(
<<<EOSQL
CREATE TABLE IF NOT EXISTS `$FORMULAS_CHOICES` (
    `id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT "formula choice id",
    `formula` bigint(11) NOT NULL COMMENT "formula id",
    `name` varchar(255) NOT NULL COMMENT "choice name",
    `edit` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "edit time",
    PRIMARY KEY (`id`),
    FOREIGN KEY (`formula`) REFERENCES `$FORMULAS`(`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=`$CHARSET` COLLATE `$COLLATE`
EOSQL
            );
        }

        if (! in_array(self::FORMULAS_CHOICES_PRODUCTS, $tables)) {
            self::query(
<<<EOSQL
CREATE TABLE IF NOT EXISTS `$FORMULAS_CHOICES_PRODUCTS` (
    `id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT "id",
    `choice` bigint(11) NOT NULL COMMENT "formula choice id",
    `product` bigint(11) NOT NULL COMMENT "product id",
    `edit` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "edit time",
    PRIMARY KEY (`id`),
    FOREIGN KEY (`choice`) REFERENCES `$FORMULAS_CHOICES`(`id`),
    FOREIGN KEY (`product`) REFERENCES `cafet_products`(`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=`$CHARSET` COLLATE `$COLLATE`
EOSQL
            );
        }

        if (! in_array(self::FORMULAS_EDITS, $tables)) {
            self::query(
<<<EOSQL
CREATE TABLE IF NOT EXISTS `$FORMULAS_EDITS` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT "id",
    `formula` bigint(20) NOT NULL COMMENT "formula id",
    `name` varchar(255) NULL DEFAULT NULL COMMENT "new formula name",
    `price` float(10,2) NULL DEFAULT NULL  COMMENT "new formula price",
    `edit` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "edition",
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=`$CHARSET` COLLATE `$COLLATE`
EOSQL
            );
        }

        if (! in_array(self::PRODUCTS, $tables)) {
            self::query(
<<<EOSQL
CREATE TABLE IF NOT EXISTS `$PRODUCTS` (
    `id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT "product id",
    `product_group` bigint(11) NOT NULL DEFAULT "0" COMMENT "product group",
    `image` longtext COMMENT "product image",
    `stock` BIGINT NOT NULL DEFAULT "0" COMMENT "amount in stock",
    `viewable` tinyint(1) NOT NULL DEFAULT "1" COMMENT "does the product have to be shown",
    `last_edit` bigint(20) NOT NULL DEFAULT "0" COMMENT "last product edit id",
    PRIMARY KEY (`id`),
    FOREIGN KEY (`last_edit`) REFERENCES `$PRODUCTS_EDITS`(`id`),
    FOREIGN KEY (`product_group`) REFERENCES `$PRODUCTS_GROUPS`(`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=`$CHARSET` COLLATE `$COLLATE`
EOSQL
            );
        }

        if (! in_array(self::PRODUCTS_BOUGHT, $tables)) {
            self::query(
<<<EOSQL
CREATE TABLE IF NOT EXISTS `$PRODUCTS_BOUGHT` (
    `id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT "transaction id",
    `expense_id` bigint(11) NOT NULL COMMENT "expense id",
    `product_id` bigint(11) NOT NULL COMMENT "product bought id",
    `edit_id` bigint(20) NULL DEFAULT NULL COMMENT "product edit id at the transaction date",
    `user_id` bigint(11) NOT NULL COMMENT "user id",
    `quantity` int(11) NOT NULL DEFAULT "1" COMMENT "product quantity",
    `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "transaction date",
    PRIMARY KEY (`id`),
    FOREIGN KEY (`expense_id`) REFERENCES `$EXPENSES`(`id`),
    FOREIGN KEY (`edit_id`) REFERENCES `$PRODUCTS_EDITS`(`id`),
    FOREIGN KEY (`user_id`) REFERENCES `$USERS`(`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=`$CHARSET` COLLATE `$COLLATE`
EOSQL
            );
        }

        if (! in_array(self::PRODUCTS_EDITS, $tables)) {
            self::query(
<<<EOSQL
CREATE TABLE IF NOT EXISTS `$PRODUCTS_EDITS` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT "edit id",
    `product` bigint(20) NOT NULL COMMENT "product id",
    `name` varchar(255) NULL DEFAULT NULL COMMENT "new product name",
    `price` float(10,2) NULL DEFAULT NULL COMMENT "new product price",
    `edit` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "edition",
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=`$CHARSET` COLLATE `$COLLATE`
EOSQL
            );
        }

        if (! in_array(self::PRODUCTS_GROUPS, $tables)) {
            self::query(
<<<EOSQL
CREATE TABLE IF NOT EXISTS `$PRODUCTS_GROUPS` (
    `id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT "product group id",
    `name` varchar(30) NOT NULL COMMENT "product group name",
    `display_name` varchar(255) NOT NULL COMMENT "product group display name",
    `edit` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "edit time",
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=`$CHARSET` COLLATE `$COLLATE`
EOSQL
            );
        }

        if (! in_array(self::REPLENISHMENTS, $tables)) {
            self::query(
<<<EOSQL
CREATE TABLE IF NOT EXISTS `$REPLENISHMENTS` (
    `id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT "replenishment id",
    `product_id` bigint(11) NOT NULL COMMENT "product id",
    `quantity` int(11) NOT NULL COMMENT "replenishment quantity",
    `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "replenishment date",
    PRIMARY KEY (`id`),
    FOREIGN KEY (`product_id`) REFERENCES `$PRODUCTS`(`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=`$CHARSET` COLLATE `$COLLATE`
EOSQL
            );
        }
        
        self::query('SET foreign_key_checks=1;');
    }

    /**
     * Check the existence of cafetapi triggers
     *
     * @since API 1.0.0 (2018)
     */
    private static final function checkTriggers()
    {
        $stmt = self::$globalConnection->prepare('SHOW TRIGGERS FROM `' . self::$database . '`');
        if (! $stmt->execute())
            self::registerErrorOccurence($stmt);

        $triggers = array();
        while ($result = $stmt->fetch())
            $triggers[] = $result['trigger'];

        if (! in_array('cafet_new_balance_reload', $triggers)) {
            self::query('CREATE TRIGGER `cafet_new_balance_reload` '
                . 'BEFORE INSERT ON `' . self::RELOADS . '` '
                . 'FOR EACH ROW '
                . 'BEGIN '
                    . 'SET @user = NEW.user_id;' . "\n"
                    . 'UPDATE `' . self::USERS . '` SET balance = balance + NEW.amount WHERE id = @user;' . "\n"
                    . 'SET NEW.user_balance = (SELECT balance FROM `users` WHERE id = @user); '
                . 'END');
        }

        if (! in_array('cafet_formula_deleted', $triggers)) {
            self::query('CREATE TRIGGER `cafet_formula_deleted` '
                . 'AFTER DELETE ON `' . self::FORMULAS . '` '
                . 'FOR EACH ROW '
                . 'BEGIN '
                    . 'DELETE FROM `' . self::FORMULAS_CHOICES . '` WHERE formula = OLD.id; '
                . 'END');
        }

        if (! in_array('cafet_new_formula_bought', $triggers)) {
            self::query('CREATE TRIGGER `cafet_new_formula_bought` '
                . 'BEFORE INSERT ON `' . self::FORMULAS_BOUGHT . '` '
                . 'FOR EACH ROW '
                . 'BEGIN '
                    . 'SET NEW.edit_id = (SELECT last_edit FROM `cafet_formulas` WHERE id = NEW.formula_id);' . "\n"
                    . 'SET @price = (SELECT price FROM `cafet_formulas_edits` WHERE id = NEW.edit_id);' . "\n"
                    . 'UPDATE `' . self::USERS . '` SET balance = balance-@price*NEW.quantity WHERE id = NEW.user_id;' . "\n"
                    . 'UPDATE `' . self::EXPENSES . '` SET user_balance = (SELECT balance FROM `users` WHERE id = NEW.user_id) WHERE id = NEW.expense_id; '
                . 'END');
        }
        
        if (! in_array('cafet_new_formula_bought_product', $triggers)) {
            $FORMULAS_BOUGHT_PRODUCTS = self::FORMULAS_BOUGHT_PRODUCTS;
            $PRODUCTS = self::PRODUCTS;
            self::query(<<<EOSQL
CREATE TRIGGER `cafet_new_formula_bought_product` 
BEFORE INSERT ON `$FORMULAS_BOUGHT_PRODUCTS` 
FOR EACH ROW 
BEGIN 
	SET NEW.product_edit = (SELECT last_edit FROM `$PRODUCTS` WHERE id = NEW.product_id);
END
EOSQL
            );
        }

        if (! in_array('cafet_save_formula_bought_product', $triggers)) {
            self::query('CREATE TRIGGER `cafet_save_formula_bought_product` '
                . 'AFTER INSERT ON `' . self::FORMULAS_BOUGHT_PRODUCTS . '` '
                . 'FOR EACH ROW '
                . 'BEGIN '
                    . 'UPDATE `' . self::PRODUCTS . '` SET stock = stock - 1 WHERE id = NEW.product_id; '
                . 'END');
        }

        if (! in_array('cafet_formula_choice_deleted', $triggers)) {
            self::query('CREATE TRIGGER `cafet_formula_choice_deleted` '
                . 'AFTER DELETE ON `' . self::FORMULAS_CHOICES . '` '
                . 'FOR EACH ROW '
                . 'BEGIN '
                    . 'DELETE FROM `' . self::FORMULAS_CHOICES_PRODUCTS . '` WHERE choice = OLD.id; '
                . 'END');
        }

        if (! in_array('cafet_new_formula_edition', $triggers)) {
            self::query('CREATE TRIGGER `cafet_new_formula_edition` '
                . 'BEFORE INSERT ON `' . self::FORMULAS_EDITS . '` '
                . 'FOR EACH ROW '
                . 'BEGIN '
                    . 'SET @count = (SELECT COUNT(*) FROM `' . self::FORMULAS_EDITS . '` WHERE formula = NEW.formula);' . "\n"
                    . 'IF NEW.price IS NULL THEN' . "\n" . '	IF @count <> 0 THEN' . "\n"
                    . '    SET NEW.price = (SELECT price FROM `' . self::FORMULAS_EDITS . '` WHERE formula = NEW.formula ORDER BY id DESC LIMIT 1);' . "\n"
                    . 'ELSE' . "\n"
                    . '    SET NEW.price = 0;' . "\n"
                    . 'END IF;' . "\n"
                    . 'END IF;' . "\n"
                    . 'IF NEW.price < 0 THEN' . "\n"
                    . '    SET NEW.price = 0;' . "\n"
                    . 'END IF;' . "\n"
                    . 'IF NEW.name IS NULL AND @count <> 0 THEN' . "\n"
                    . '    SET NEW.name = (SELECT name FROM `' . self::FORMULAS_EDITS . '` WHERE formula = NEW.formula ORDER BY id DESC LIMIT 1);' . "\n"
                    . 'END IF; '
                . 'END');
        }

        if (! in_array('cafet_save_formula_edition', $triggers)) {
            self::query('CREATE TRIGGER `cafet_save_formula_edition` '
                . 'AFTER INSERT ON `' . self::FORMULAS_EDITS . '` '
                . 'FOR EACH ROW '
                . 'BEGIN '
                    . 'UPDATE `' . self::FORMULAS . '` SET last_edit = NEW.id WHERE id = NEW.formula; '
                . 'END');
        }

        if (! in_array('cafet_new_product_bought', $triggers)) {
            self::query('CREATE TRIGGER `cafet_new_product_bought` '
                . 'BEFORE INSERT ON `' . self::PRODUCTS_BOUGHT . '` '
                . 'FOR EACH ROW '
                . 'BEGIN '
                    . 'SET NEW.edit_id = (SELECT last_edit FROM `cafet_products` WHERE id = NEW.product_id);' . "\n"
                    . 'SET @price = (SELECT price FROM `cafet_products_edits` WHERE id = NEW.edit_id);' . "\n"
                    . 'UPDATE `' . self::PRODUCTS . '` SET stock = stock - NEW.quantity WHERE id = NEW.product_id;' . "\n"
                    . 'UPDATE `' . self::USERS . '` SET balance = balance-@price*NEW.quantity WHERE ID = NEW.user_id;' . "\n"
                    . 'UPDATE `' . self::EXPENSES . '` SET user_balance = (SELECT balance FROM `users` WHERE id = NEW.user_id) WHERE id = NEW.expense_id; '
                . 'END');
        }

        if (! in_array('cafet_new_product_edition', $triggers)) {
            self::query('CREATE TRIGGER `cafet_new_product_edition` '
                . 'BEFORE INSERT ON `' . self::PRODUCTS_EDITS . '` '
                . 'FOR EACH ROW '
                . 'BEGIN '
                    . 'SET @count = (SELECT COUNT(*) FROM `' . self::PRODUCTS_EDITS . '` WHERE product = NEW.product);' . "\n"
                    . 'IF NEW.price IS NULL THEN' . "\n" . '	IF @count <> 0 THEN' . "\n"
                    . '		SET NEW.price = (SELECT price FROM `' . self::PRODUCTS_EDITS . '` WHERE product = NEW.product ORDER BY id DESC LIMIT 1);' . "\n"
                    . 'ELSE' . "\n"
                    . '   	SET NEW.price = 0;' . "\n"
                    . '   END IF;' . "\n"
                    . 'END IF;' . "\n"
                    . 'IF NEW.price < 0 THEN' . "\n"
                    . '	SET NEW.price = 0;' . "\n"
                    . 'END IF;' . "\n"
                    . 'IF NEW.name IS NULL AND @count <> 0 THEN' . "\n"
                    . '	SET NEW.name = (SELECT name FROM `' . self::PRODUCTS_EDITS . '` WHERE product = NEW.product ORDER BY id DESC LIMIT 1);' . "\n"
                    . 'END IF; '
                . 'END');
        }

        if (! in_array('cafet_save_product_edition', $triggers)) {
            self::query('CREATE TRIGGER `cafet_save_product_edition` '
                . 'AFTER INSERT ON `' . self::PRODUCTS_EDITS . '` '
                . 'FOR EACH ROW '
                . 'BEGIN '
                    . 'UPDATE `' . self::PRODUCTS . '` SET last_edit = NEW.id WHERE id = NEW.product; '
                . 'END');
        }

        if (! in_array('cafet_product_group_deleted', $triggers)) {
            self::query('CREATE TRIGGER `cafet_product_group_deleted` '
                . 'AFTER DELETE ON `' . self::PRODUCTS_GROUPS . '` '
                    . 'FOR EACH ROW ' . 'BEGIN ' . 'DELETE FROM `' . self::PRODUCTS . '` WHERE product_group = OLD.id; '
                . 'END');
        }

        if (! in_array('cafet_new_replenishment', $triggers)) {
            self::query('CREATE TRIGGER `cafet_new_replenishment` '
                . 'AFTER INSERT ON `' . self::REPLENISHMENTS . '` '
                . 'FOR EACH ROW '
                . 'BEGIN '
                    . 'SET @quantity = NEW.quantity;' . "\n"
                    . 'SET @product = NEW.product_id;' . "\n"
                    . 'UPDATE `' . self::PRODUCTS . '` SET stock = stock + @quantity WHERE id = @product; '
                . 'END');
        }
    }

    protected static final function registerErrorOccurence(PDOStatement $stmt)
    {
        $array = $stmt->errorInfo();
        $array[] = $stmt->queryString;
        self::$lastQueryErrors[] = $array;
        cafet_throw_error('01-500', 'SQL error: [' . $array[0] . '] ' . $array[2] . ' (with query:' . $array[3] . ')');
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

    /**
     * Returns every configuration stored in the database
     *
     * @return array - an array of key-value
     * @since API 1.0.0 (2018)
     */
    public final function getConfigurations(): array
    {
        $conf = array();
        $statement = $this->connection->query('SELECT name, value FROM ' . self::CONFIG . ' ORDER BY name');

        while ($data = $statement->fetch()) {
            $conf[$data['name']] = $data['value'];
        }

        $statement->closeCursor();

        return $conf;
    }

    /**
     * Return the configuration value stored in the database for the give key
     *
     * @param String $name
     *            the key
     * @return string the value
     * @since API 1.0.0 (2018)
     */
    public final function getConfiguration(String $name): string
    {
        $statement = $this->connection->prepare('SELECT value FROM ' . self::CONFIG . ' WHERE name = :name ORDER BY edit DESK LIMIT 1');
        $statement->execute(array(
            'name' => $name
        ));

        $conf = null;

        if ($data = $statement->fetch())
            $conf = $data['value'];

        $statement->closeCursor();

        return $conf;
    }

    /**
     * Fetch user information for the given username/email
     *
     * @param string $mail_or_pseudo
     *            the mail/pseudo of the user
     * @return NULL|User A User object that represents the user
     * @since API 1.0.0 (2018)
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
}

