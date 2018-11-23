SET autocommit=0;
SET foreign_key_checks=0;



-- --
-- cafet_users
-- --

CREATE TABLE IF NOT EXISTS `cafet_users` (
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
) ENGINE=InnoDB DEFAULT CHARSET=`utf8mb4` COLLATE `utf8mb4_unicode_ci`;

-- --
-- cafet_users
-- --

CREATE TABLE IF NOT EXISTS `cafet_customers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL COMMENT "user\'s id",
  `member` int(11) NOT NULL DEFAULT '0',
  `balance` float(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `cafet_users`(`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=`utf8mb4` COLLATE `utf8mb4_unicode_ci`;

-- --
-- cafet_balance_reloads
-- --

-- Table
CREATE TABLE IF NOT EXISTS `cafet_balance_reloads` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT "reload\'s id",
  `user_id` bigint(20) NOT NULL COMMENT "user\'s id",
  `amount` float(10,2) NOT NULL COMMENT "reload\'s amount",
  `user_balance` float(10,2) NOT NULL DEFAULT "0.00" COMMENT " user\'s balance after transaction",
  `details` varchar(255) NOT NULL DEFAULT "APP/UNKNOW" COMMENT "reload method",
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "transaction\'s date",
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `cafet_users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=`utf8mb4` COLLATE `utf8mb4_unicode_ci`;

-- Triggers
DELIMITER $$

CREATE TRIGGER `cafet_new_balance_reload` 
BEFORE INSERT ON `cafet_balance_reloads` 
FOR EACH ROW 
BEGIN  	
	SET @user = NEW.user_id;
	UPDATE `cafet_customers` SET balance = balance + NEW.amount WHERE user_id = @user;
	SET NEW.user_balance = (SELECT balance FROM `cafet_customers` WHERE user_id = @user); 
END
$$

DELIMITER ;

-- --
-- cafet_config
-- --

-- Table
CREATE TABLE IF NOT EXISTS `cafet_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT "id",
  `name` varchar(255) NOT NULL COMMENT "configuration key",
  `value` text NOT NULL COMMENT "configuration value",
  `edit` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "last edition",
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=`utf8mb4` COLLATE `utf8mb4_unicode_ci`;



-- --
-- cafet_expenses
-- --

-- Table
CREATE TABLE IF NOT EXISTS `cafet_expenses` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT "transaction\'s id",
  `user_id` bigint(20) NOT NULL COMMENT "user\'s id",
  `user_balance` float(10,2) NOT NULL DEFAULT "0.00" COMMENT "user\'s balance after transaction",
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "transaction\'s date",
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `cafet_users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=`utf8mb4` COLLATE `utf8mb4_unicode_ci`;



-- --
-- cafet_products_groups
-- --

-- Table
CREATE TABLE IF NOT EXISTS `cafet_products_groups` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT "product group id",
  `name` varchar(30) NOT NULL COMMENT "product group name",
  `display_name` varchar(255) NOT NULL COMMENT "product group display name",
  `edit` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "edit time",
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=`utf8mb4` COLLATE `utf8mb4_unicode_ci`;

-- Triggers
DELIMITER $$

CREATE TRIGGER `cafet_product_group_deleted` 
BEFORE DELETE ON `cafet_products_groups` 
FOR EACH ROW 
BEGIN 
	DELETE FROM `cafet_products` WHERE product_group = OLD.id; 
END
$$

DELIMITER ;



-- --
-- cafet_products_edits
-- --

-- Table
CREATE TABLE IF NOT EXISTS `cafet_products_edits` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT "edit id",
  `product` bigint(20) NOT NULL COMMENT "product id",
  `name` varchar(255) NULL DEFAULT NULL COMMENT "new product name",
  `price` float(10,2) NULL DEFAULT NULL COMMENT "new product price",
  `edit` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "edition",
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=`utf8mb4` COLLATE `utf8mb4_unicode_ci`;

-- Triggers
DELIMITER $$

CREATE TRIGGER `cafet_new_product_edition` 
BEFORE INSERT ON `cafet_products_edits` 
FOR EACH ROW 
BEGIN 
	SET @count = (SELECT COUNT(*) FROM `cafet_products_edits` WHERE product = NEW.product);
	IF NEW.price IS NULL THEN
		IF @count <> 0 THEN
			SET NEW.price = (SELECT price FROM `cafet_products_edits` WHERE product = NEW.product ORDER BY id DESC LIMIT 1);
			ELSE
	   	SET NEW.price = 0;
	   END IF;
   	END IF;
	IF NEW.price < 0 THEN
		SET NEW.price = 0;
		END IF;
	IF NEW.name IS NULL AND @count <> 0 THEN
		SET NEW.name = (SELECT name FROM `cafet_products_edits` WHERE product = NEW.product ORDER BY id DESC LIMIT 1);
	END IF; 
END
$$

CREATE TRIGGER `cafet_save_product_edition` 
AFTER INSERT ON `cafet_products_edits` 
FOR EACH ROW 
BEGIN 
	UPDATE `cafet_products` SET last_edit = NEW.id WHERE id = NEW.product; 
END
$$

DELIMITER ;



-- --
-- cafet_products
-- --

-- Table
CREATE TABLE IF NOT EXISTS `cafet_products` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT "product id",
  `product_group` bigint(11) NOT NULL DEFAULT "0" COMMENT "product group",
  `image` longtext COMMENT "product image",
  `stock` BIGINT NOT NULL DEFAULT "0" COMMENT "amount in stock",
  `viewable` tinyint(1) NOT NULL DEFAULT "1" COMMENT "does the product have to be shown",
  `last_edit` bigint(20) NOT NULL DEFAULT "0" COMMENT "last product edit id",
  PRIMARY KEY (`id`),
  FOREIGN KEY (`last_edit`) REFERENCES `cafet_products_edits`(`id`),
  FOREIGN KEY (`product_group`) REFERENCES `cafet_products_groups`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=`utf8mb4` COLLATE `utf8mb4_unicode_ci`;



-- --
-- cafet_products_bought
-- --

-- Table
CREATE TABLE IF NOT EXISTS `cafet_products_bought` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT "transaction id",
  `expense_id` bigint(11) NOT NULL COMMENT "expense id",
  `product_id` bigint(11) NOT NULL COMMENT "product bought id",
  `edit_id` bigint(20) NULL DEFAULT NULL COMMENT "product edit id at the transaction date",
  `user_id` bigint(20) NOT NULL COMMENT "user id",
  `quantity` int(11) NOT NULL DEFAULT "1" COMMENT "product quantity",
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "transaction date",
  PRIMARY KEY (`id`),
  FOREIGN KEY (`expense_id`) REFERENCES `cafet_expenses`(`id`),
  FOREIGN KEY (`edit_id`) REFERENCES `cafet_products_edits`(`id`),
  FOREIGN KEY (`user_id`) REFERENCES `cafet_users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=`utf8mb4` COLLATE `utf8mb4_unicode_ci`;

-- Triggers
DELIMITER $$

CREATE TRIGGER `cafet_new_product_bought` 
BEFORE INSERT ON `cafet_products_bought` 
FOR EACH ROW 
BEGIN 
	SET NEW.edit_id = (SELECT last_edit FROM `cafet_products` WHERE id = NEW.product_id);
	SET @price = (SELECT price FROM `cafet_products_edits` WHERE id = NEW.edit_id);
	UPDATE `cafet_products` SET stock = stock - NEW.quantity WHERE id = NEW.product_id;
	UPDATE `cafet_customers` SET balance = balance-@price*NEW.quantity WHERE user_id = NEW.user_id;
	UPDATE `cafet_expenses` SET user_balance = (SELECT balance FROM `cafet_customers` WHERE user_id = NEW.user_id) WHERE id = NEW.expense_id; 
END
$$

DELIMITER ;



-- --
-- cafet_replenishments
-- --

-- Table
CREATE TABLE IF NOT EXISTS `cafet_replenishments` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT "replenishment id",
  `product_id` bigint(11) NOT NULL COMMENT "product id",
  `quantity` int(11) NOT NULL COMMENT "replenishment quantity",
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "replenishment date",
  PRIMARY KEY (`id`),
  FOREIGN KEY (`product_id`) REFERENCES `cafet_products`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=`utf8mb4` COLLATE `utf8mb4_unicode_ci`;

-- Triggers
DELIMITER $$

CREATE TRIGGER `cafet_new_replenishment` 
AFTER INSERT ON `cafet_replenishments` 
FOR EACH ROW 
BEGIN 
	SET @quantity = NEW.quantity;
	SET @product = NEW.product_id;
	UPDATE `cafet_products` SET stock = stock + @quantity WHERE id = @product; 
END
$$

DELIMITER ;



-- --
-- cafet_formulas_edits
-- --

-- Table
CREATE TABLE IF NOT EXISTS `cafet_formulas_edits` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT "id",
  `formula` bigint(20) NOT NULL COMMENT "formula id",
  `name` varchar(255) NULL DEFAULT NULL COMMENT "new formula name",
  `price` float(10,2) NULL DEFAULT NULL  COMMENT "new formula price",
  `edit` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "edition",
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=`utf8mb4` COLLATE `utf8mb4_unicode_ci`;

-- Triggers
DELIMITER $$

CREATE TRIGGER `cafet_new_formula_edition` 
BEFORE INSERT ON `cafet_formulas_edits` 
FOR EACH ROW 
BEGIN 
	SET @count = (SELECT COUNT(*) FROM `cafet_formulas_edits` WHERE formula = NEW.formula);
	IF NEW.price IS NULL THEN
		IF @count <> 0 THEN
			SET NEW.price = (SELECT price FROM `cafet_formulas_edits` WHERE formula = NEW.formula ORDER BY id DESC LIMIT 1);
		ELSE
			SET NEW.price = 0;
		END IF;
		END IF;
	IF NEW.price < 0 THEN
		SET NEW.price = 0;
		END IF;
	IF NEW.name IS NULL AND @count <> 0 THEN
		SET NEW.name = (SELECT name FROM `cafet_formulas_edits` WHERE formula = NEW.formula ORDER BY id DESC LIMIT 1);
	END IF; 
END
$$

CREATE TRIGGER `cafet_save_formula_edition` 
AFTER INSERT ON `cafet_formulas_edits` 
FOR EACH ROW 
BEGIN 
	UPDATE `cafet_formulas` SET last_edit = NEW.id WHERE id = NEW.formula; 
END
$$

DELIMITER ;



-- --
-- cafet_formulas
-- --

-- Table
CREATE TABLE IF NOT EXISTS `cafet_formulas` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT "formula\'s id",
  `image` longtext COMMENT "formula\'s image",
  `viewable` tinyint(1) NOT NULL DEFAULT "1" COMMENT "is the formula viewable",
  `last_edit` bigint(20) NOT NULL DEFAULT "0" COMMENT "last formula edit id",
  PRIMARY KEY (`id`),
  FOREIGN KEY (`last_edit`) REFERENCES `cafet_formulas_edits`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=`utf8mb4` COLLATE `utf8mb4_unicode_ci`;

-- Triggers
DELIMITER $$

CREATE TRIGGER `cafet_formula_deleted` 
BEFORE DELETE ON `cafet_formulas` 
FOR EACH ROW 
BEGIN 
	DELETE FROM `cafet_formulas_choices` WHERE formula = OLD.id; 
END
$$

DELIMITER ;



-- --
-- cafet_formulas_bought
-- --

-- Table
CREATE TABLE IF NOT EXISTS `cafet_formulas_bought` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT "transaction id",
  `expense_id` bigint(11) NOT NULL COMMENT "expense id",
  `formula_id` bigint(11) NOT NULL COMMENT "formula id",
  `edit_id` bigint(20) NULL DEFAULT NULL COMMENT "formula edit id at the transaction date",
  `user_id` bigint(20) NOT NULL COMMENT "user id",
  `quantity` int(11) NOT NULL DEFAULT "1" COMMENT "quantity",
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "transaction date",
  PRIMARY KEY (`id`),
  FOREIGN KEY (`expense_id`) REFERENCES `cafet_expenses`(`id`),
  FOREIGN KEY (`edit_id`) REFERENCES `cafet_formulas_edits`(`id`),
  FOREIGN KEY (`user_id`) REFERENCES `cafet_users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=`utf8mb4` COLLATE `utf8mb4_unicode_ci`;

-- Triggers
DELIMITER $$

CREATE TRIGGER `cafet_new_formula_bought` 
BEFORE INSERT ON `cafet_formulas_bought` 
FOR EACH ROW 
BEGIN 
	SET NEW.edit_id = (SELECT last_edit FROM `cafet_formulas` WHERE id = NEW.formula_id);
	SET @price = (SELECT price FROM `cafet_formulas_edits` WHERE id = NEW.edit_id);
	UPDATE `cafet_customers` SET balance = balance-@price*NEW.quantity WHERE user_id = NEW.user_id;
	UPDATE `cafet_expenses` SET user_balance = (SELECT balance FROM `cafet_customers` WHERE user_id = NEW.user_id) WHERE id = NEW.expense_id; 
END
$$

DELIMITER ;



-- --
-- cafet_formulas_bought_products
-- --

-- Table
CREATE TABLE IF NOT EXISTS `cafet_formulas_bought_products` (
 `id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT "id",
  `transaction_id` bigint(11) NOT NULL COMMENT "id in formulas bought",
  `product_id` bigint(11) NOT NULL COMMENT "product id",
  `product_edit` BIGINT(20) NULL COMMENT "the product edit",
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "transaction date",
  PRIMARY KEY (`id`),
  FOREIGN KEY (`transaction_id`) REFERENCES `cafet_formulas_bought`(`id`),
  FOREIGN KEY (`product_edit`) REFERENCES `cafet_products_edits`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=`utf8mb4` COLLATE `utf8mb4_unicode_ci`;

-- Triggers
DELIMITER $$

CREATE TRIGGER `cafet_new_formula_bought_product` 
BEFORE INSERT ON `cafet_formulas_bought_products` 
FOR EACH ROW 
BEGIN 
	SET NEW.product_edit = (SELECT last_edit FROM `cafet_products` WHERE id = NEW.product_id);
END
$$

CREATE TRIGGER `cafet_save_formula_bought_product` 
AFTER INSERT ON `cafet_formulas_bought_products` 
FOR EACH ROW 
BEGIN 
	UPDATE `cafet_products` SET stock = stock - 1 WHERE id = NEW.product_id; 
END
$$

DELIMITER ;



-- --
-- cafet_formulas_choices
-- --

-- Table
CREATE TABLE IF NOT EXISTS `cafet_formulas_choices` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT "formula choice id",
  `formula` bigint(11) NOT NULL COMMENT "formula id",
  `name` varchar(255) NOT NULL COMMENT "choice name",
  `edit` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "edit time",
  PRIMARY KEY (`id`),
  FOREIGN KEY (`formula`) REFERENCES `cafet_formulas`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=`utf8mb4` COLLATE `utf8mb4_unicode_ci`;

-- Triggers
DELIMITER $$

CREATE TRIGGER `cafet_formula_choice_deleted` 
BEFORE DELETE ON `cafet_formulas_choices` 
FOR EACH ROW 
BEGIN 
	DELETE FROM `cafet_formulas_choices_products` WHERE choice = OLD.id; 
END
$$

DELIMITER ;



-- --
-- cafet_formulas_choices_products
-- --

-- Table
CREATE TABLE IF NOT EXISTS `cafet_formulas_choices_products` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT "id",
  `choice` bigint(11) NOT NULL COMMENT "formula choice id",
  `product` bigint(11) NOT NULL COMMENT "product id",
  `edit` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "edit time",
  PRIMARY KEY (`id`),
  FOREIGN KEY (`choice`) REFERENCES `cafet_formulas_choices`(`id`),
  FOREIGN KEY (`product`) REFERENCES `cafet_products`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=`utf8mb4` COLLATE `utf8mb4_unicode_ci`;



-- --
-- End
-- --
SET foreign_key_checks=1;
COMMIT ;