<?php
namespace cafetapi\io;

class DatabaseIntegrity extends DatabaseConnection
{
    private $USERS = self::USERS;
    private $CLIENTS = self::CLIENTS;
    private $CONFIG = self::CONFIG;
    private $RELOADS = self::RELOADS;
    private $EXPENSES = self::EXPENSES;
    private $FORMULAS = self::FORMULAS;
    private $FORMULAS_BOUGHT = self::FORMULAS_BOUGHT;
    private $FORMULAS_BOUGHT_PRODUCTS = self::FORMULAS_BOUGHT_PRODUCTS;
    private $FORMULAS_CHOICES = self::FORMULAS_CHOICES;
    private $FORMULAS_CHOICES_PRODUCTS = self::FORMULAS_CHOICES_PRODUCTS;
    private $FORMULAS_EDITS = self::FORMULAS_EDITS;
    private $PRODUCTS = self::PRODUCTS;
    private $PRODUCTS_BOUGHT = self::PRODUCTS_BOUGHT;
    private $PRODUCTS_EDITS = self::PRODUCTS_EDITS;
    private $PRODUCTS_GROUPS = self::PRODUCTS_GROUPS;
    private $REPLENISHMENTS = self::REPLENISHMENTS;
    private $CHARSET='utf8mb4';
    private $COLLATE='utf8mb4_unicode_ci';

    /**
     * Cheks the existence of every cafetAPI tables an create those doesn't exist
     *
     * @since API 1.0.0 (2018)
     */
    public final function checkTables()
    {
        $stmt = self::$globalConnection->query('SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = "BASE TABLE" AND TABLE_SCHEMA = "' . self::$database . '"');
        $tables = [];
        while ($row = $stmt->fetch()) $tables[] = $row[0];
        $stmt->closeCursor();
        
        $needed_tables = [
            self::USERS,
            self::CLIENTS,
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
        ];
        
        if(count(array_intersect($tables, $needed_tables)) >= count($needed_tables)) return;
        
        self::query('SET foreign_key_checks = 0');
        
        if (! in_array($this->USERS, $tables)) {
            self::query(
                <<<EOSQL
                CREATE TABLE IF NOT EXISTS `$this->USERS` (
                    `id` bigint(20) NOT NULL AUTO_INCREMENT,
                    `username` varchar(255) NOT NULL,
                    `email` varchar(255) NOT NULL,
                    `firstname` varchar(255) NOT NULL,
                    `familyname` varchar(255) NOT NULL,
                    `phone` varchar(20) DEFAULT NULL,
                    `group_id` tinyint(11) NOT NULL DEFAULT '1',
                    `registration` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `last_signin` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `signin_count` bigint(20) NOT NULL DEFAULT '0',
                    `password` varchar(500) NOT NULL,
                    `permissions` longtext,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `username` (`username`),
                    UNIQUE KEY `email` (`email`)
                  ) ENGINE=InnoDB DEFAULT CHARSET=`$this->CHARSET` COLLATE `$this->COLLATE`;
EOSQL
                );
        }
        
        if (! in_array($this->CLIENTS, $tables)) {
            self::query(
                <<<EOSQL
                CREATE TABLE IF NOT EXISTS `$this->CLIENTS` (
                    `id` bigint(20) NOT NULL AUTO_INCREMENT,
                    `user_id` bigint(20) COMMENT "user\'s id",
                    `member` int(11) NOT NULL DEFAULT '0',
                    `balance` float(10,2) NOT NULL DEFAULT '0.00',
                    PRIMARY KEY (`id`),
                    FOREIGN KEY (`user_id`)
                      REFERENCES `$this->USERS`(`id`)
                      ON UPDATE CASCADE
                      ON DELETE SET NULL
                  ) ENGINE=InnoDB DEFAULT CHARSET=`$this->CHARSET` COLLATE `$this->COLLATE`;
EOSQL
                );
        }
        
        if (! in_array($this->RELOADS, $tables)) {
            self::query(
                <<<EOSQL
                CREATE TABLE IF NOT EXISTS `$this->RELOADS` (
                    `id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT "reload\'s id",
                    `user_id` bigint(20) NOT NULL COMMENT "user\'s id",
                    `amount` float(10,2) NOT NULL COMMENT "reload\'s amount",
                    `user_balance` float(10,2) NOT NULL DEFAULT "0.00" COMMENT " user\'s balance after transaction",
                    `details` varchar(255) NOT NULL DEFAULT "APP/UNKNOW" COMMENT "reload method",
                    `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "transaction\'s date",
                    PRIMARY KEY (`id`),
                    FOREIGN KEY (`user_id`)
                      REFERENCES `$this->CLIENTS`(`id`)
                      ON UPDATE CASCADE
                  ) ENGINE=InnoDB DEFAULT CHARSET=`$this->CHARSET` COLLATE `$this->COLLATE`;
EOSQL
                );
        }
        
        if (! in_array($this->CONFIG, $tables)) {
            self::query(
                <<<EOSQL
                CREATE TABLE IF NOT EXISTS `$this->CONFIG` (
                    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT "id",
                    `name` varchar(255) NOT NULL COMMENT "configuration key",
                    `value` text NOT NULL COMMENT "configuration value",
                    `edit` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "last edition",
                    PRIMARY KEY (`id`)
                  ) ENGINE=MyISAM DEFAULT CHARSET=`$this->CHARSET` COLLATE `$this->COLLATE`;
EOSQL
                );
        }
        
        if (! in_array($this->EXPENSES, $tables)) {
            self::query(
                <<<EOSQL
                CREATE TABLE IF NOT EXISTS `$this->EXPENSES` (
                    `id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT "transaction\'s id",
                    `user_id` bigint(20) NOT NULL COMMENT "user\'s id",
                    `user_balance` float(10,2) NOT NULL DEFAULT "0.00" COMMENT "user\'s balance after transaction",
                    `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "transaction\'s date",
                    PRIMARY KEY (`id`),
                    FOREIGN KEY (`user_id`)
                      REFERENCES `$this->CLIENTS`(`id`)
                      ON UPDATE CASCADE
                  ) ENGINE=InnoDB DEFAULT CHARSET=`$this->CHARSET` COLLATE `$this->COLLATE`;
EOSQL
                );
        }
        
        if (! in_array($this->PRODUCTS_GROUPS, $tables)) {
            self::query(
                <<<EOSQL
                CREATE TABLE IF NOT EXISTS `$this->PRODUCTS_GROUPS` (
                    `id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT "product group id",
                    `name` varchar(30) NOT NULL COMMENT "product group name",
                    `display_name` varchar(255) NOT NULL COMMENT "product group display name",
                    `edit` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "edit time",
                    PRIMARY KEY (`id`)
                  ) ENGINE=InnoDB DEFAULT CHARSET=`$this->CHARSET` COLLATE `$this->COLLATE`;
EOSQL
                );
        }
        
        if (! in_array($this->PRODUCTS_EDITS, $tables)) {
            self::query(
                <<<EOSQL
                CREATE TABLE IF NOT EXISTS `$this->PRODUCTS_EDITS` (
                    `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT "edit id",
                    `product` bigint(20) NOT NULL COMMENT "product id",
                    `name` varchar(255) NULL DEFAULT NULL COMMENT "new product name",
                    `price` float(10,2) NULL DEFAULT NULL COMMENT "new product price",
                    `edit` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "edition",
                    PRIMARY KEY (`id`)
                  ) ENGINE=InnoDB DEFAULT CHARSET=`$this->CHARSET` COLLATE `$this->COLLATE`;
EOSQL
                );
        }
        
        if (! in_array($this->PRODUCTS, $tables)) {
            self::query(
                <<<EOSQL
                CREATE TABLE IF NOT EXISTS `$this->PRODUCTS` (
                    `id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT "product id",
                    `product_group` bigint(11) NOT NULL DEFAULT "0" COMMENT "product group",
                    `image` longtext COMMENT "product image",
                    `stock` BIGINT NOT NULL DEFAULT "0" COMMENT "amount in stock",
                    `viewable` tinyint(1) NOT NULL DEFAULT "1" COMMENT "does the product have to be shown",
                    `last_edit` bigint(20) NOT NULL DEFAULT "0" COMMENT "last product edit id",
                    PRIMARY KEY (`id`),
                    FOREIGN KEY (`last_edit`)
                      REFERENCES `$this->PRODUCTS_EDITS`(`id`)
                      ON UPDATE CASCADE,
                    FOREIGN KEY (`product_group`)
                      REFERENCES `$this->PRODUCTS_GROUPS`(`id`)
                      ON UPDATE CASCADE
                      ON DELETE CASCADE
                  ) ENGINE=InnoDB DEFAULT CHARSET=`$this->CHARSET` COLLATE `$this->COLLATE`;
EOSQL
                );
        }
        
        if (! in_array($this->PRODUCTS_BOUGHT, $tables)) {
            self::query(
                <<<EOSQL
                CREATE TABLE IF NOT EXISTS `$this->PRODUCTS_BOUGHT` (
                    `id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT "transaction id",
                    `expense_id` bigint(11) NOT NULL COMMENT "expense id",
                    `product_id` bigint(11) NOT NULL COMMENT "product bought id",
                    `edit_id` bigint(20) NULL DEFAULT NULL COMMENT "product edit id at the transaction date",
                    `user_id` bigint(20) NOT NULL COMMENT "user id",
                    `quantity` int(11) NOT NULL DEFAULT "1" COMMENT "product quantity",
                    `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "transaction date",
                    PRIMARY KEY (`id`),
                    FOREIGN KEY (`expense_id`)
                      REFERENCES `$this->EXPENSES`(`id`)
                      ON UPDATE CASCADE
                      ON DELETE CASCADE,
                    FOREIGN KEY (`edit_id`)
                      REFERENCES `$this->PRODUCTS_EDITS`(`id`)
                      ON UPDATE CASCADE,
                    FOREIGN KEY (`user_id`)
                      REFERENCES `$this->CLIENTS`(`id`)
                      ON UPDATE CASCADE
                  ) ENGINE=InnoDB DEFAULT CHARSET=`$this->CHARSET` COLLATE `$this->COLLATE`;
EOSQL
                );
        }
        
        if (! in_array($this->REPLENISHMENTS, $tables)) {
            self::query(
                <<<EOSQL
                CREATE TABLE IF NOT EXISTS `$this->REPLENISHMENTS` (
                    `id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT "replenishment id",
                    `product_id` bigint(11) COMMENT "product id",
                    `quantity` int(11) NOT NULL COMMENT "replenishment quantity",
                    `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "replenishment date",
                    PRIMARY KEY (`id`),
                    FOREIGN KEY (`product_id`)
                      REFERENCES `$this->PRODUCTS`(`id`)
                      ON UPDATE CASCADE
                      ON DELETE SET NULL
                  ) ENGINE=InnoDB DEFAULT CHARSET=`$this->CHARSET` COLLATE `$this->COLLATE`;
EOSQL
                );
        }
        
        if (! in_array($this->FORMULAS_EDITS, $tables)) {
            self::query(
                <<<EOSQL
                CREATE TABLE IF NOT EXISTS `$this->FORMULAS_EDITS` (
                    `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT "id",
                    `formula` bigint(20) NOT NULL COMMENT "formula id",
                    `name` varchar(255) NULL DEFAULT NULL COMMENT "new formula name",
                    `price` float(10,2) NULL DEFAULT NULL  COMMENT "new formula price",
                    `edit` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "edition",
                    PRIMARY KEY (`id`)
                  ) ENGINE=InnoDB DEFAULT CHARSET=`$this->CHARSET` COLLATE `$this->COLLATE`;
EOSQL
                );
        }
        
        if (! in_array($this->FORMULAS, $tables)) {
            self::query(
                <<<EOSQL
                CREATE TABLE IF NOT EXISTS `$this->FORMULAS` (
                    `id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT "formula\'s id",
                    `image` longtext COMMENT "formula\'s image",
                    `viewable` tinyint(1) NOT NULL DEFAULT "1" COMMENT "is the formula viewable",
                    `last_edit` bigint(20) NOT NULL DEFAULT "0" COMMENT "last formula edit id",
                    PRIMARY KEY (`id`),
                    FOREIGN KEY (`last_edit`)
                      REFERENCES `$this->FORMULAS_EDITS`(`id`)
                      ON UPDATE CASCADE
                  ) ENGINE=InnoDB DEFAULT CHARSET=`$this->CHARSET` COLLATE `$this->COLLATE`;
EOSQL
                );
        }
        
        if (! in_array($this->FORMULAS_BOUGHT, $tables)) {
            self::query(
                <<<EOSQL
                CREATE TABLE IF NOT EXISTS `$this->FORMULAS_BOUGHT` (
                    `id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT "transaction id",
                    `expense_id` bigint(11) NOT NULL COMMENT "expense id",
                    `formula_id` bigint(11) NOT NULL COMMENT "formula id",
                    `edit_id` bigint(20) NULL DEFAULT NULL COMMENT "formula edit id at the transaction date",
                    `user_id` bigint(20) NOT NULL COMMENT "user id",
                    `quantity` int(11) NOT NULL DEFAULT "1" COMMENT "quantity",
                    `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "transaction date",
                    PRIMARY KEY (`id`),
                    FOREIGN KEY (`expense_id`)
                      REFERENCES `$this->EXPENSES`(`id`)
                      ON UPDATE CASCADE
                      ON DELETE CASCADE,
                    FOREIGN KEY (`edit_id`)
                      REFERENCES `$this->FORMULAS_EDITS`(`id`)
                      ON UPDATE CASCADE,
                    FOREIGN KEY (`user_id`)
                      REFERENCES `$this->CLIENTS`(`id`)
                      ON UPDATE CASCADE
                  ) ENGINE=InnoDB DEFAULT CHARSET=`$this->CHARSET` COLLATE `$this->COLLATE`;
EOSQL
                );
        }
        
        if (! in_array($this->FORMULAS_BOUGHT_PRODUCTS, $tables)) {
            self::query(
                <<<EOSQL
                CREATE TABLE IF NOT EXISTS `$this->FORMULAS_BOUGHT_PRODUCTS` (
                    `id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT "id",
                    `transaction_id` bigint(11) NOT NULL COMMENT "id in formulas bought",
                    `product_id` bigint(11) NOT NULL COMMENT "product id",
                    `product_edit` BIGINT(20) NULL COMMENT "the product edit",
                    `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "transaction date",
                    PRIMARY KEY (`id`),
                    FOREIGN KEY (`transaction_id`)
                      REFERENCES `$this->FORMULAS_BOUGHT`(`id`)
                      ON UPDATE CASCADE
                      ON DELETE CASCADE,
                    FOREIGN KEY (`product_edit`)
                      REFERENCES `$this->PRODUCTS_EDITS`(`id`)
                      ON UPDATE CASCADE
                  ) ENGINE=InnoDB DEFAULT CHARSET=`$this->CHARSET` COLLATE `$this->COLLATE`;
EOSQL
                );
        }
        
        if (! in_array($this->FORMULAS_CHOICES, $tables)) {
            self::query(
                <<<EOSQL
                CREATE TABLE IF NOT EXISTS `$this->FORMULAS_CHOICES` (
                    `id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT "formula choice id",
                    `formula` bigint(11) NOT NULL COMMENT "formula id",
                    `name` varchar(255) NOT NULL COMMENT "choice name",
                    `edit` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "edit time",
                    PRIMARY KEY (`id`),
                    FOREIGN KEY (`formula`)
                      REFERENCES `$this->FORMULAS`(`id`)
                      ON DELETE CASCADE
                  ) ENGINE=InnoDB DEFAULT CHARSET=`$this->CHARSET` COLLATE `$this->COLLATE`;
EOSQL
                );
        }
        
        if (! in_array($this->FORMULAS_CHOICES_PRODUCTS, $tables)) {
            self::query(
                <<<EOSQL
                CREATE TABLE IF NOT EXISTS `$this->FORMULAS_CHOICES_PRODUCTS` (
                    `id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT "id",
                    `choice` bigint(11) NOT NULL COMMENT "formula choice id",
                    `product` bigint(11) NOT NULL COMMENT "product id",
                    `edit` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "edit time",
                    PRIMARY KEY (`id`),
                    FOREIGN KEY (`choice`)
                      REFERENCES `$this->FORMULAS_CHOICES`(`id`)
                      ON DELETE CASCADE,
                    FOREIGN KEY (`product`)
                      REFERENCES `$this->PRODUCTS`(`id`)
                      ON DELETE CASCADE
                  ) ENGINE=InnoDB DEFAULT CHARSET=`$this->CHARSET` COLLATE `$this->COLLATE`;
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
    public final function checkTriggers()
    {
        $stmt = self::$globalConnection->prepare('SHOW TRIGGERS FROM `' . self::$database . '`');
        if (! $stmt->execute())
            self::registerErrorOccurence($stmt);
            
            $triggers = array();
            while ($result = $stmt->fetch())
                $triggers[] = $result['trigger'];
                
                if (! in_array('cafet_new_balance_reload', $triggers)) {
                    self::query(
                        <<<EOSQL
                        CREATE TRIGGER `cafet_new_balance_reload` 
                        BEFORE INSERT ON `$this->RELOADS` 
                        FOR EACH ROW 
                        BEGIN  	
                            SET @user = NEW.user_id;
                            UPDATE `$this->CLIENTS` SET balance = balance + NEW.amount WHERE user_id = @user;
                            SET NEW.user_balance = (SELECT balance FROM `$this->CLIENTS` WHERE user_id = @user); 
                        END
EOSQL
                    );
                }
                
                if (! in_array('cafet_new_product_edition', $triggers)) {
                    self::query(
                        <<<EOSQL
                        CREATE TRIGGER `cafet_new_product_edition` 
                        BEFORE INSERT ON `$this->PRODUCTS_EDITS` 
                        FOR EACH ROW 
                        BEGIN 
                            SET @count = (SELECT COUNT(*) FROM `$this->PRODUCTS_EDITS` WHERE product = NEW.product);
                            IF NEW.price IS NULL THEN
                                IF @count <> 0 THEN
                                    SET NEW.price = (SELECT price FROM `$this->PRODUCTS_EDITS` WHERE product = NEW.product ORDER BY id DESC LIMIT 1);
                                    ELSE
                                   SET NEW.price = 0;
                               END IF;
                               END IF;
                            IF NEW.price < 0 THEN
                                SET NEW.price = 0;
                                END IF;
                            IF NEW.name IS NULL AND @count <> 0 THEN
                                SET NEW.name = (SELECT name FROM `$this->PRODUCTS_EDITS` WHERE product = NEW.product ORDER BY id DESC LIMIT 1);
                            END IF; 
                        END
EOSQL
                    );
                }
                
                if (! in_array('cafet_save_product_edition', $triggers)) {
                    self::query(
                        <<<EOSQL
                        CREATE TRIGGER `cafet_save_product_edition` 
                        AFTER INSERT ON `$this->PRODUCTS_EDITS` 
                        FOR EACH ROW 
                        BEGIN 
                            UPDATE `$this->PRODUCTS` SET last_edit = NEW.id WHERE id = NEW.product; 
                        END
EOSQL
                    );
                }
                
                if (! in_array('cafet_new_product_bought', $triggers)) {
                    self::query(
                        <<<EOSQL
                        CREATE TRIGGER `cafet_new_product_bought` 
                        BEFORE INSERT ON `$this->PRODUCTS_BOUGHT` 
                        FOR EACH ROW 
                        BEGIN 
                            SET NEW.edit_id = (SELECT last_edit FROM `$this->PRODUCTS` WHERE id = NEW.product_id);
                            SET @price = (SELECT price FROM `$this->PRODUCTS_EDITS` WHERE id = NEW.edit_id);
                            UPDATE `$this->PRODUCTS` SET stock = stock - NEW.quantity WHERE id = NEW.product_id;
                            UPDATE `$this->CLIENTS` SET balance = balance-@price*NEW.quantity WHERE user_id = NEW.user_id;
                            UPDATE `$this->EXPENSES` SET user_balance = (SELECT balance FROM `$this->CLIENTS` WHERE user_id = NEW.user_id) WHERE id = NEW.expense_id; 
                        END
EOSQL
                    );
                }
                
                if (! in_array('cafet_new_replenishment', $triggers)) {
                    self::query(
                        <<<EOSQL
                        CREATE TRIGGER `cafet_new_replenishment` 
                        AFTER INSERT ON `$this->REPLENISHMENTS` 
                        FOR EACH ROW 
                        BEGIN 
                            SET @quantity = NEW.quantity;
                            SET @product = NEW.product_id;
                            UPDATE `$this->PRODUCTS` SET stock = stock + @quantity WHERE id = @product; 
                        END
EOSQL
                    );
                }
                
                if (! in_array('cafet_new_formula_edition', $triggers)) {
                    self::query(
                        <<<EOSQL
                        CREATE TRIGGER `cafet_new_formula_edition` 
                        BEFORE INSERT ON `$this->FORMULAS_EDITS` 
                        FOR EACH ROW 
                        BEGIN 
                            SET @count = (SELECT COUNT(*) FROM `$this->FORMULAS_EDITS` WHERE formula = NEW.formula);
                            IF NEW.price IS NULL THEN
                                IF @count <> 0 THEN
                                    SET NEW.price = (SELECT price FROM `$this->FORMULAS_EDITS` WHERE formula = NEW.formula ORDER BY id DESC LIMIT 1);
                                ELSE
                                    SET NEW.price = 0;
                                END IF;
                                END IF;
                            IF NEW.price < 0 THEN
                                SET NEW.price = 0;
                                END IF;
                            IF NEW.name IS NULL AND @count <> 0 THEN
                                SET NEW.name = (SELECT name FROM `$this->FORMULAS_EDITS` WHERE formula = NEW.formula ORDER BY id DESC LIMIT 1);
                            END IF; 
                        END
EOSQL
                    );
                }
                
                if (! in_array('cafet_save_formula_edition', $triggers)) {
                    self::query(
                        <<<EOSQL
                        CREATE TRIGGER `cafet_save_formula_edition` 
                        AFTER INSERT ON `$this->FORMULAS_EDITS` 
                        FOR EACH ROW 
                        BEGIN 
                            UPDATE `$this->FORMULAS` SET last_edit = NEW.id WHERE id = NEW.formula; 
                        END
EOSQL
                    );
                }
                
                if (! in_array('cafet_new_formula_bought', $triggers)) {
                    self::query(
                        <<<EOSQL
                        CREATE TRIGGER `cafet_new_formula_bought` 
                        BEFORE INSERT ON `$this->FORMULAS_BOUGHT` 
                        FOR EACH ROW 
                        BEGIN 
                            SET NEW.edit_id = (SELECT last_edit FROM `$this->FORMULAS` WHERE id = NEW.formula_id);
                            SET @price = (SELECT price FROM `$this->FORMULAS_EDITS` WHERE id = NEW.edit_id);
                            UPDATE `$this->CLIENTS` SET balance = balance-@price*NEW.quantity WHERE user_id = NEW.user_id;
                            UPDATE `$this->EXPENSES` SET user_balance = (SELECT balance FROM `$this->CLIENTS` WHERE user_id = NEW.user_id) WHERE id = NEW.expense_id; 
                        END
EOSQL
                    );
                }
                
                if (! in_array('cafet_new_formula_bought_product', $triggers)) {
                    self::query(
                        <<<EOSQL
                        CREATE TRIGGER `cafet_new_formula_bought_product` 
                        BEFORE INSERT ON `$this->FORMULAS_BOUGHT_PRODUCTS` 
                        FOR EACH ROW 
                        BEGIN 
                            SET NEW.product_edit = (SELECT last_edit FROM `$this->PRODUCTS` WHERE id = NEW.product_id);
                        END
EOSQL
                    );
                }
                
                if (! in_array('cafet_save_formula_bought_product', $triggers)) {
                    self::query(
                        <<<EOSQL
                        CREATE TRIGGER `cafet_save_formula_bought_product` 
                        AFTER INSERT ON `$this->FORMULAS_BOUGHT_PRODUCTS` 
                        FOR EACH ROW 
                        BEGIN 
                            UPDATE `$this->PRODUCTS` SET stock = stock - 1 WHERE id = NEW.product_id; 
                        END
EOSQL
                    );
                }
    }
}

