<?php
namespace cafetapi\io;

class DatabaseIntegrity extends DatabaseConnection
{
    /**
     * Cheks the existence of every cafetAPI tables an create those doesn't exist
     *
     * @since API 1.0.0 (2018)
     */
    public static final function checkTables()
    {
        $stmt = self::$globalConnection->query('SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = "BASE TABLE" AND TABLE_SCHEMA = "' . self::$database . '"');
        $tables = [];
        while ($row = $stmt->fetch()) $tables[] = $row[0];
        $stmt->closeCursor();
        
        $needed_tables = [
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
    public static final function checkTriggers()
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
}

