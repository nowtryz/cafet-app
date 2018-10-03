<?php
namespace cafetapi\io;

use cafetapi\data\Choice;
use cafetapi\data\Formula;
use cafetapi\data\FormulaOrdered;
use cafetapi\data\Product;
use cafetapi\data\ProductGroup;
use cafetapi\data\ProductOrdered;
use cafetapi\exceptions\NotEnoughtMoneyException;
use cafetapi\exceptions\RequestFailureException;
use PDO;
use PDOStatement;

/**
 * Object specialized in data updating from the database for the API
 *
 * @author Damien
 * @since API 1.0.0 (2018)
 */
class DataUpdater extends DatabaseConnection
{
    private static $instance;
    
    /**
     * Get singleton object
     * @return DataUpdater the singleton of this class
     */
    public static function getInstance() : DataUpdater
    {
        if(self::$instance === null) self::$instance = new DataUpdater();
        return self::$instance;
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
    private final function checkUpdate(PDOStatement $stmt, string $message, bool $autorisation_error = false)
    {
        if ($stmt->errorCode() != '00000')
            parent::registerErrorOccurence($stmt);

        $sql_error = $stmt->errorCode() != '00000' ? ': ' . $stmt->errorInfo()[2] : '';
        $backtrace = debug_backtrace()[1];

        if ($stmt->rowCount() == 0) {
            $this->connection->rollBack();

            throw new RequestFailureException($message . $sql_error, null, null, $backtrace['file'], $backtrace['line']);
        }
    }

    /**
     * Insert a product into the database
     *
     * @param string $name
     * @param int $group_id
     * @return Product the product inserted
     * @since API 1.0.0 (2018)
     */
    public final function addProduct(string $name, int $group_id): Product
    {
        $this->connection->beginTransaction();
        $this->connection->exec('SET foreign_key_checks=0');

        $stmt = $this->connection->prepare('INSERT INTO ' . self::PRODUCTS . '(product_group) VALUES (:group)');
        $stmt->execute(array(
            'group' => $group_id
        ));

        $this->checkUpdate($stmt, 'unable to add the product (product insertion)');

        $stmt->closeCursor();
        $product_id = $this->connection->lastInsertId();
        $stmt = $this->connection->prepare('INSERT INTO ' . self::PRODUCTS_EDITS . '(product, name) VALUES(:product,:name)');
        $stmt->execute(array(
            'product' => $product_id,
            'name' => $name
        ));

        $this->checkUpdate($stmt, 'unable to add the product (edit insertion)');
        $stmt->closeCursor();
        
        $this->connection->exec('SET foreign_key_checks=1');
        $this->connection->commit();
        return (new DataFetcher())->getProduct($product_id);
    }

    /**
     * Insert a product group in the database
     *
     * @param string $name
     * @return ProductGroup the group inserted
     * @since API 1.0.0 (2018)
     */
    public final function addProductGroup(string $name): ProductGroup
    {
        $this->connection->beginTransaction();

        $stmt = $this->connection->prepare('INSERT INTO ' . self::PRODUCTS_GROUPS . '(name, display_name) VALUES(:name, :dname)');
        $stmt->execute(array(
            'name' => $name,
            'dname' => $name
        ));
        $this->checkUpdate($stmt, 'unable to add the product group');

        $id = $this->connection->lastInsertId();
        $this->connection->commit();
        return (new DataFetcher())->getProductGroup($id);
    }

    /**
     * Insert a formula in the database
     *
     * @param
     *            string name
     * @return Formula the formula inserted
     * @since API 1.0.0 (2018)
     */
    public final function addFormula(string $name): Formula
    {
        $this->connection->beginTransaction();
        $this->connection->exec('SET foreign_key_checks=0');

        $stmt = $this->connection->prepare('INSERT INTO ' . self::FORMULAS . '(id) VALUES (NULL)');
        $stmt->execute();
        $this->checkUpdate($stmt, 'unable to add the formula');

        $stmt->closeCursor();
        $formula_id = $this->connection->lastInsertId();
        $stmt = $this->connection->prepare('INSERT INTO ' . self::FORMULAS_EDITS . '(formula, name) VALUES(:formula,:name)');
        $stmt->execute(array(
            'formula' => $formula_id,
            'name' => $name
        ));
        $this->checkUpdate($stmt, 'unable to add the formula');
        $stmt->closeCursor();
        
        $this->connection->exec('SET foreign_key_checks=1');
        $this->connection->commit();
        return (new DataFetcher())->getFormula($formula_id);
    }

    /**
     * Insert a choice in the database for the specified formula
     *
     * @param string $name
     * @param int $formula_id
     * @return Choice the choice inserted
     * @since API 1.0.0 (2018)
     */
    public final function addChoice(string $name, int $formula_id): Choice
    {
        $this->connection->beginTransaction();

        $stmt = $this->connection->prepare('INSERT INTO ' . self::FORMULAS_CHOICES . '(formula, name) VALUES (:formula, :name)');
        $stmt->execute(array(
            'formula' => $formula_id,
            'name' => $name
        ));
        $this->checkUpdate($stmt, 'unable to add the choice');

        $id = $this->connection->lastInsertId();
        $this->connection->commit();
        return (new DataFetcher())->getChoice($id);
    }

    /**
     * Register a product for the specified choice
     *
     * @param int $choice_id
     * @param int $product_id
     * @return bool if the query has been correctly completed
     * @since API 1.0.0 (2018)
     */
    public final function addProductToChoice(int $choice_id, int $product_id): bool
    {
        $this->connection->beginTransaction();

        $stmt = $this->connection->prepare('INSERT INTO ' . self::FORMULAS_CHOICES_PRODUCTS . '(choice, product) VALUES (:choice, :product)');
        $stmt->execute(array(
            'choice' => $choice_id,
            'product' => $product_id
        ));
        $this->checkUpdate($stmt, 'unable to register product');

        $this->connection->commit();
        return true;
    }

    /**
     * Unregister a product for the specified choice
     *
     * @param int $choice_id
     * @param int $product_id
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function removeProductFromChoice(int $choice_id, int $product_id): bool
    {
        $this->connection->beginTransaction();

        $stmt = $this->connection->prepare('DELETE FROM ' . self::FORMULAS_CHOICES_PRODUCTS . ' WHERE choice = :choice AND product = :product');
        $stmt->execute(array(
            'choice' => $choice_id,
            'product' => $product_id
        ));
        $this->checkUpdate($stmt, 'unable to unregister product');

        $this->connection->commit();
        return true;
    }

    /**
     * Save a purchase in the database
     *
     * @param int $client_id
     * @param array $order array of Ordered
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function saveOrder(int $client_id, array $order): bool
    {
        $this->connection->beginTransaction();

        // Save expense
        $stmt = $this->connection->prepare('INSERT INTO ' . self::EXPENSES . ' (user_id) VALUES (:id)');
        $stmt->execute(array(
            'id' => $client_id
        ));
        $this->checkUpdate($stmt, 'unable to save order');
        $expense_id = $this->connection->lastInsertId();

        // Save expense details
        foreach ($order as $entry) {
            if ($entry instanceof ProductOrdered) {
                $stmt = $this->connection->prepare('SELECT 1 FROM ' . self::PRODUCTS . ' WHERE id = :id LIMIT 1');
                $stmt->execute(array(
                    'id' => $entry->getId()
                ));
                if (! $stmt->fetch())
                    cafet_throw_error('03-005', 'product with id ' . $entry->getId() . ' doesn\'t exist');
                $stmt->closeCursor();

                $stmt = $this->connection->prepare('INSERT '
                    . 'INTO ' . self::PRODUCTS_BOUGHT . '(expense_id, product_id, user_id, quantity) '
                    . 'VALUES (:expense, :product, :client, :quantity)');
                $stmt->execute(array(
                    'expense' => $expense_id,
                    'product' => $entry->getId(),
                    'client' => $client_id,
                    'quantity' => $entry->getAmount()
                ));
                $this->checkUpdate($stmt, 'unable to save expense details');
            } elseif ($entry instanceof FormulaOrdered) {

                $stmt = $this->connection->prepare('SELECT 1 FROM ' . self::FORMULAS . ' WHERE id = :id LIMIT 1');
                $stmt->execute(array(
                    'id' => $entry->getId()
                ));
                if (! $stmt->fetch())
                    cafet_throw_error('03-005', 'formula with id ' . $entry->getId() . ' doesn\'t exist');
                $stmt->closeCursor();

                $stmt = $this->connection->prepare('INSERT '
                    . 'INTO ' . self::FORMULAS_BOUGHT . '(expense_id, formula_id, user_id, quantity) '
                    . 'VALUES (:expense,:formula,:client,:quantity)');
                $stmt->execute(array(
                    'expense' => $expense_id,
                    'formula' => $entry->getId(),
                    'client' => $client_id,
                    'quantity' => $entry->getAmount()
                ));
                $this->checkUpdate($stmt, 'unable to save expense details');

                $i = 0;
                $size = count($entry->getProducts());
                $fb_id = $this->connection->lastInsertId();
                $parameters = array(
                    'id' => $fb_id
                );
                
                $sql = 'INSERT INTO ' . self::FORMULAS_BOUGHT_PRODUCTS . '(transaction_id,product_id) VALUES ';
                foreach ($entry->getProducts() as $product) {
                    $sql .= "(:id,:product$i)";
                    $sql .= $i < $size - 1 ? ',' : '';
                    $parameters['product' . $i] = $product;
                    $i ++;
                }
                $stmt = $this->connection->prepare($sql);
                $stmt->execute($parameters);
                $this->checkUpdate($stmt, 'unable to save expense details');
            }
        }

        $e = (new DataFetcher())->getExpense($expense_id);
        $conf = cafet_get_configurations();

        if (($delta = $e->getBalanceAfterTransaction() - $conf['balance_limit']) < 0) {
            $this->connection->rollBack();
            $backtrace = debug_backtrace()[1];
            throw new NotEnoughtMoneyException('missing ' . $delta . ' money to perform this action', null, null, $backtrace['file'], $backtrace['line']);
            return false;
        } else {
            $this->connection->commit();
            if ($e->getBalanceAfterTransaction() < $conf['balance_warning'])
                cafet_send_reload_request($client_id);
        }

        return true;
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
        $this->connection->beginTransaction();

        $stmt = $this->connection->prepare('INSERT INTO ' . self::RELOADS . '(user_id, amount, details) VALUES (:client,:amount,:details)');
        $stmt->execute(array(
            'client' => $client_id,
            'amount' => $amount,
            'details' => $comment
        ));
        $this->checkUpdate($stmt, 'unable to save balance reload');

        $this->connection->commit();
        return true;
    }

    /**
     * Change the display name for the specified group
     *
     * @param int $group_id
     * @param string $display_name
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function setProductGroupDisplayName(int $group_id, string $display_name): bool
    {
        $this->connection->beginTransaction();

        $stmt = $this->connection->prepare('UPDATE ' . self::PRODUCTS_GROUPS . ' SET display_name = :dname WHERE id = :id');
        $stmt->execute(array(
            'dname' => $display_name,
            'id' => $group_id
        ));
        $this->checkUpdate($stmt, 'unable to change product group display name');

        $this->connection->commit();
        return true;
    }

    /**
     * Change the name for the specified group
     *
     * @param int $group_id
     * @param string $name
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function setProductGroupName(int $group_id, string $name): bool
    {
        $this->connection->beginTransaction();

        $stmt = $this->connection->prepare('UPDATE ' . self::PRODUCTS_GROUPS . ' SET name = :name WHERE id = :id');
        $stmt->execute(array(
            'name' => $name,
            'id' => $group_id
        ));
        $this->checkUpdate($stmt, 'unable to change product group name');

        $this->connection->commit();
        return true;
    }

    /**
     * Change the name for the specified product
     *
     * @param int $product_id
     * @param string $name
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function setProductName(int $product_id, string $name): bool
    {
        $this->connection->beginTransaction();

        $stmt = $this->connection->prepare('INSERT INTO ' . self::PRODUCTS_EDITS . '(product, name) VALUES (:id, :name)');
        $stmt->execute(array(
            'id' => $product_id,
            'name' => $name
        ));
        $this->checkUpdate($stmt, 'unable to update product information');

        $this->connection->commit();
        return true;
    }

    /**
     * Change the price for the specified product
     *
     * @param int $product_id
     * @param float $price
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function setProductPrice(int $product_id, float $price): bool
    {
        $this->connection->beginTransaction();

        $stmt = $this->connection->prepare('INSERT INTO ' . self::PRODUCTS_EDITS . '(product, price) VALUES (:id, :price)');
        $stmt->execute(array(
            'id' => $product_id,
            'price' => $price
        ));
        $this->checkUpdate($stmt, 'unable to update product information');

        $this->connection->commit();
        return true;
    }

    /**
     * Change the group for the specified product
     *
     * @param int $product_id
     * @param int $group_id
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function setProductGroup(int $product_id, int $group_id): bool
    {
        $this->connection->beginTransaction();

        $stmt = $this->connection->prepare('SELECT 1 FROM ' . self::PRODUCTS_GROUPS . ' WHERE id = :id LIMIT 1');
        $stmt->execute(array(
            'id' => $group_id
        ));
        if (! $stmt->fetch())
            cafet_throw_error('03-005', 'the group with id ' . $group_id . ' doesn\'t exist');

        $stmt = $this->connection->prepare('UPDATE ' . self::PRODUCTS . ' SET product_group = :group WHERE id = :id');
        $stmt->execute(array(
            'group' => $group_id,
            'id' => $product_id
        ));
        $this->checkUpdate($stmt, 'unable to update the product');

        $this->connection->commit();
        return true;
    }

    /**
     * Change the image of the specified product
     *
     * @param int $product_id
     * @param string $image_base64
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function setProductImage(int $product_id, string $image_base64): bool
    {
        $this->connection->beginTransaction();

        $stmt = $this->connection->prepare('UPDATE ' . self::PRODUCTS . ' SET image = :image WHERE id = :id');
        $stmt->execute(array(
            'image' => $image_base64,
            'id' => $product_id
        ));
        $this->checkUpdate($stmt, 'unable to update product');

        $this->connection->commit();
        return true;
    }

    /**
     * Change the visibility of the specified product
     *
     * @param int $product_id
     * @param bool $flag
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function setProductViewable(int $product_id, bool $flag): bool
    {
        $this->connection->beginTransaction();

        $stmt = $this->connection->prepare('UPDATE ' . self::PRODUCTS . ' SET viewable = :flag WHERE id = :id');
        
        $stmt->bindValue(':flag', $flag, PDO::PARAM_BOOL);
        $stmt->bindValue(':id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $this->checkUpdate($stmt, 'unable to update product');

        $this->connection->commit();
        return true;
    }

    /**
     * Change the name of the specified formula
     *
     * @param int $formula_id
     * @param string $name
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function setFormulaName(int $formula_id, string $name): bool
    {
        $this->connection->beginTransaction();

        $stmt = $this->connection->prepare('INSERT INTO ' . self::FORMULAS_EDITS . '(formula,name) VALUES (:id, :name)');
        $stmt->execute(array(
            'id' => $formula_id,
            'name' => $name
        ));
        $this->checkUpdate($stmt, 'unable to update formula');

        $this->connection->commit();
        return true;
    }

    /**
     * Change the price of the specified formula
     *
     * @param int $formula_id
     * @param float $price
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function setFormulaPrice(int $formula_id, float $price): bool
    {
        $this->connection->beginTransaction();

        $stmt = $this->connection->prepare('INSERT INTO ' . self::FORMULAS_EDITS . '(formula,price) VALUES (:id, :price)');
        $stmt->execute(array(
            'id' => $formula_id,
            'price' => $price
        ));
        $this->checkUpdate($stmt, 'unable to update formula');

        $this->connection->commit();
        return true;
    }

    /**
     * Change the visibility of the specified formula
     *
     * @param int $formula_id
     * @param bool $flag
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function setFormulaViewable(int $formula_id, bool $flag): bool
    {
        $this->connection->beginTransaction();

        $stmt = $this->connection->prepare('UPDATE ' . self::FORMULAS . ' SET viewable = :flag WHERE id = :id');
        
        $stmt->bindValue(':flag', $flag, PDO::PARAM_BOOL);
        $stmt->bindValue(':id', $formula_id, PDO::PARAM_INT);
        
        $stmt->execute();
        
        $this->checkUpdate($stmt, 'unable to update formula');

        $this->connection->commit();
        return true;
    }

    /**
     * Change the image of the specified formula
     *
     * @param int $formula_id
     * @param string $image_base64
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function setFormulaImage(int $formula_id, string $image_base64): bool
    {
        $this->connection->beginTransaction();

        $stmt = $this->connection->prepare('UPDATE ' . self::FORMULAS . ' SET image = :image WHERE id = :id');
        $stmt->execute(array(
            'image' => $image_base64,
            'id' => $formula_id
        ));
        $this->checkUpdate($stmt, 'unable to update formula');

        $this->connection->commit();
        return true;
    }

    /**
     * Delete a product from the database
     *
     * @param int $id
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function deleteProduct(int $id): bool
    {
        $this->connection->beginTransaction();

        $stmt = $this->connection->prepare('DELETE FROM ' . self::PRODUCTS . ' WHERE id = :id');
        $stmt->execute(array(
            'id' => $id
        ));
        $this->checkUpdate($stmt, 'unable to delete the product');

        $this->connection->commit();
        return true;
    }

    /**
     * Delete a product group from the database
     *
     * @param int $id
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function deleteProductGroup(int $id): bool
    {
        $this->connection->beginTransaction();

        $stmt = $this->connection->prepare('DELETE FROM ' . self::PRODUCTS_GROUPS . ' WHERE id = :id');
        $stmt->execute(array(
            'id' => $id
        ));
        $this->checkUpdate($stmt, 'unable to delete the product group');

        $this->connection->commit();
        return true;
    }

    /**
     * Delete a formula from the database
     *
     * @param int $id
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function deleteFormula(int $id): bool
    {
        $this->connection->beginTransaction();

        $stmt = $this->connection->prepare('DELETE FROM ' . self::FORMULAS . ' WHERE id = :id');
        $stmt->execute(array(
            'id' => $id
        ));
        $this->checkUpdate($stmt, 'unable to delete the formula');

        $this->connection->commit();
        return true;
    }

    /**
     * Delete a choice from the database
     *
     * @param int $id
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function deleteFormulaChoice(int $id): bool
    {
        $this->connection->beginTransaction();

        $stmt = $this->connection->prepare('DELETE FROM ' . self::FORMULAS_CHOICES . ' WHERE id = :id');
        $stmt->execute(array(
            'id' => $id
        ));
        $this->checkUpdate($stmt, 'unable to delete the choice');

        $this->connection->commit();
        return true;
    }
}