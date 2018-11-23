<?php
namespace cafetapi\io;

use cafetapi\data\Calendar;
use cafetapi\data\Product;
use cafetapi\data\ProductGroup;
use PDO;

/**
 *
 * @author Damien
 *        
 */
class ProductManager extends Updater
{
    private static $instance;
    
    /**
     * Get singleton object
     * @return ProductManager the singleton of this class
     */
    public static function getInstance() : ProductManager
    {
        if(self::$instance === null) self::$instance = new ProductManager();
        return self::$instance;
    }
    
    public final function getProductGroups(): array
    {
        $stmt = $this->connection->prepare('SELECT '
            . 'id, '
            . 'name, '
            . 'display_name dname '
            . 'FROM ' . self::PRODUCTS_GROUPS . ' '
            . 'ORDER BY id');
        
        $id = 0;
        $name = $dname = '';
        
        $stmt->bindColumn('id', $id, PDO::PARAM_INT);
        $stmt->bindColumn('name', $name, PDO::PARAM_STR);
        $stmt->bindColumn('dname', $dname, PDO::PARAM_STR);
        
        $stmt->execute();
        $this->check_fetch_errors($stmt);
        
        $result = array();
        
        while ($stmt->fetch()) $result[] = new ProductGroup($id, $name, $dname);
            
        return $result;
    }
    
    public final function getProductGroup(int $group_id): ?ProductGroup
    {
        $stmt = $this->connection->prepare('SELECT '
            . 'id, '
            . 'name, '
            . 'display_name dname '
            . 'FROM ' . self::PRODUCTS_GROUPS . ' '
            . 'WHERE id = :id '
            . 'ORDER BY id');
        
        $id = 0;
        $name = $dname = '';
        
        $stmt->bindColumn('id', $id, PDO::PARAM_INT);
        $stmt->bindColumn('name', $name, PDO::PARAM_STR);
        $stmt->bindColumn('dname', $dname, PDO::PARAM_STR);
        
        $stmt->execute(array(
            'id' => $group_id
        ));
        $this->check_fetch_errors($stmt);
        
        if ($stmt->fetch()) return new ProductGroup($id, $name, $dname);
            
        else return NULL;
    }
    
    public final function getGroupProducts(int $group_id, bool $show_hiddens = false): array
    {
        $stmt = $this->connection->prepare('SELECT '
            . 'p.id id, '
            . 'e.name name, '
            . 'e.price price, '
            . 'p.image image, '
            . 'p.viewable viewable, '
            . 'p.stock stock, '
            . 'DATE_FORMAT(e.edit, "%H") hour, '
            . 'DATE_FORMAT(e.edit, "%i") mins, '
            . 'DATE_FORMAT(e.edit, "%s") secs, '
            . 'DATE_FORMAT(e.edit, "%d") day, '
            . 'DATE_FORMAT(e.edit, "%c") month, '
            . 'DATE_FORMAT(e.edit, "%Y") year '
            . 'FROM ' . self::PRODUCTS . ' p '
            . 'LEFT JOIN ' . self::PRODUCTS_EDITS . ' e '
            . 'ON p.last_edit = e.id '
            . 'WHERE p.product_group = :group'
            . (! $show_hiddens ? ' AND p.viewable = 1' : ''));
        
        $id = $stock = $hour = $mins = $secs = $day = $month = $year = 0;
        $name = $price = $image = '';
        $viewable = false;
        
        $stmt->bindColumn('id', $id, PDO::PARAM_INT);
        $stmt->bindColumn('name', $name, PDO::PARAM_STR);
        $stmt->bindColumn('price', $price, PDO::PARAM_STR);
        $stmt->bindColumn('image', $image, PDO::PARAM_STR);
        $stmt->bindColumn('viewable', $viewable, PDO::PARAM_BOOL);
        $stmt->bindColumn('stock', $stock, PDO::PARAM_INT);
        $stmt->bindColumn('hour', $hour, PDO::PARAM_INT);
        $stmt->bindColumn('mins', $mins, PDO::PARAM_INT);
        $stmt->bindColumn('secs', $secs, PDO::PARAM_INT);
        $stmt->bindColumn('day', $day, PDO::PARAM_INT);
        $stmt->bindColumn('month', $month, PDO::PARAM_INT);
        $stmt->bindColumn('year', $year, PDO::PARAM_INT);
        
        $stmt->execute(array(
            'group' => $group_id
        ));
        $this->check_fetch_errors($stmt);
        
        $result = array();
        
        while ($stmt->fetch()) $result[] = new Product($id, $name, $price, $group_id, $image, $viewable, $stock, new Calendar($year, $month, $day, $hour, $mins, $secs));
            
        return $result;
    }
    
    public final function getProducts(bool $show_hiddens = false): array
    {
        $stmt = $this->connection->prepare('SELECT '
            . "\n" . 'p.id id, '
            . "\n" . 'e.name name, '
            . "\n" . 'e.price price, '
            . "\n" . 'p.image image, '
            . "\n" . 'p.stock stock, '
            . "\n" . 'p.product_group pgroup, '
            . "\n" . 'p.viewable viewable, '
            . "\n" . 'DATE_FORMAT(e.edit, "%H") hour, '
            . "\n" . 'DATE_FORMAT(e.edit, "%i") mins, '
            . "\n" . 'DATE_FORMAT(e.edit, "%s") secs, '
            . "\n" . 'DATE_FORMAT(e.edit, "%d") day, '
            . "\n" . 'DATE_FORMAT(e.edit, "%c") month, '
            . "\n" . 'DATE_FORMAT(e.edit, "%Y") year '
            . "\n" . 'FROM ' . self::PRODUCTS . ' p '
            . "\n" . 'LEFT JOIN ' . self::PRODUCTS_EDITS . ' e '
            . "\n" . 'ON p.last_edit = e.id'
            . (! $show_hiddens ? "\n" . ' WHERE p.viewable = 1' : ''));
        
        $id = $group_id = $stock = $hour = $mins = $secs = $day = $month = $year = 0;
        $name = $price = $image = '';
        $viewable = false;
        
        $stmt->bindColumn('id', $id, PDO::PARAM_INT);
        $stmt->bindColumn('name', $name, PDO::PARAM_STR);
        $stmt->bindColumn('price', $price, PDO::PARAM_STR);
        $stmt->bindColumn('image', $image, PDO::PARAM_STR);
        $stmt->bindColumn('pgroup', $group_id, PDO::PARAM_INT);
        $stmt->bindColumn('viewable', $viewable, PDO::PARAM_BOOL);
        $stmt->bindColumn('stock', $stock, PDO::PARAM_INT);
        $stmt->bindColumn('hour', $hour, PDO::PARAM_INT);
        $stmt->bindColumn('mins', $mins, PDO::PARAM_INT);
        $stmt->bindColumn('secs', $secs, PDO::PARAM_INT);
        $stmt->bindColumn('day', $day, PDO::PARAM_INT);
        $stmt->bindColumn('month', $month, PDO::PARAM_INT);
        $stmt->bindColumn('year', $year, PDO::PARAM_INT);
        
        $stmt->execute();
        $this->check_fetch_errors($stmt);
        
        $result = array();
        
        while ($stmt->fetch()) $result[] = new Product($id, $name, $price, $group_id, $image, $viewable, $stock, new Calendar($year, $month, $day, $hour, $mins, $secs));
            
        return $result;
    }
    
    public final function getProduct(int $id): ?Product
    {
        $stmt = $this->connection->prepare('SELECT '
            . 'p.id id, '
            . 'e.name name, '
            . 'e.price price, '
            . 'p.image image, '
            . 'p.stock stock, '
            . 'p.product_group group_id, '
            . 'p.viewable viewable, '
            . 'DATE_FORMAT(e.edit, "%H") hour, '
            . 'DATE_FORMAT(e.edit, "%i") mins, '
            . 'DATE_FORMAT(e.edit, "%s") secs, '
            . 'DATE_FORMAT(e.edit, "%d") day, '
            . 'DATE_FORMAT(e.edit, "%c") month, '
            . 'DATE_FORMAT(e.edit, "%Y") year '
            . 'FROM ' . self::PRODUCTS . ' p '
            . 'LEFT JOIN ' . self::PRODUCTS_EDITS . ' e '
            . 'ON p.last_edit = e.id '
            . 'WHERE p.id = :id');
        
        $group_id = $stock = $hour = $mins = $secs = $day = $month = $year = 0;
        $name = $price = $image = '';
        $viewable = false;
        
        $stmt->bindColumn('id', $id, PDO::PARAM_INT);
        $stmt->bindColumn('name', $name, PDO::PARAM_STR);
        $stmt->bindColumn('price', $price, PDO::PARAM_STR);
        $stmt->bindColumn('image', $image, PDO::PARAM_STR);
        $stmt->bindColumn('group_id', $group_id, PDO::PARAM_INT);
        $stmt->bindColumn('viewable', $viewable, PDO::PARAM_BOOL);
        $stmt->bindColumn('stock', $stock, PDO::PARAM_INT);
        $stmt->bindColumn('hour', $hour, PDO::PARAM_INT);
        $stmt->bindColumn('mins', $mins, PDO::PARAM_INT);
        $stmt->bindColumn('secs', $secs, PDO::PARAM_INT);
        $stmt->bindColumn('day', $day, PDO::PARAM_INT);
        $stmt->bindColumn('month', $month, PDO::PARAM_INT);
        $stmt->bindColumn('year', $year, PDO::PARAM_INT);
        
        $stmt->execute(array(
            'id' => $id
        ));
        $this->check_fetch_errors($stmt);
        
        if ($stmt->fetch()) return new Product($id, $name, $price, $group_id, $image, $viewable, $stock, new Calendar($year, $month, $day, $hour, $mins, $secs));
            
        else return NULL;
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
        $this->beginTransaction();
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
        $this->commit();
        return $this->getProduct($product_id);
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
        $this->beginTransaction();
        
        $stmt = $this->connection->prepare('INSERT INTO ' . self::PRODUCTS_GROUPS . '(name, display_name) VALUES(:name, :dname)');
        $stmt->execute(array(
            'name' => $name,
            'dname' => $name
        ));
        $this->checkUpdate($stmt, 'unable to add the product group');
        
        $id = $this->connection->lastInsertId();
        $this->commit();
        return $this->getProductGroup($id);
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
        $this->beginTransaction();
        
        $stmt = $this->connection->prepare('UPDATE ' . self::PRODUCTS_GROUPS . ' SET display_name = :dname WHERE id = :id');
        $stmt->execute(array(
            'dname' => $display_name,
            'id' => $group_id
        ));
        $this->checkUpdate($stmt, 'unable to change product group display name');
        
        $this->commit();
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
        $this->beginTransaction();
        
        $stmt = $this->connection->prepare('UPDATE ' . self::PRODUCTS_GROUPS . ' SET name = :name WHERE id = :id');
        $stmt->execute(array(
            'name' => $name,
            'id' => $group_id
        ));
        $this->checkUpdate($stmt, 'unable to change product group name');
        
        $this->commit();
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
        $this->beginTransaction();
        
        $stmt = $this->connection->prepare('INSERT INTO ' . self::PRODUCTS_EDITS . '(product, name) VALUES (:id, :name)');
        $stmt->execute(array(
            'id' => $product_id,
            'name' => $name
        ));
        $this->checkUpdate($stmt, 'unable to update product information');
        
        $this->commit();
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
        $this->beginTransaction();
        
        $stmt = $this->connection->prepare('INSERT INTO ' . self::PRODUCTS_EDITS . '(product, price) VALUES (:id, :price)');
        $stmt->execute(array(
            'id' => $product_id,
            'price' => $price
        ));
        $this->checkUpdate($stmt, 'unable to update product information');
        
        $this->commit();
        return true;
    }
    
    /**
     * Change the name and the price for the specified product
     *
     * @param int $product_id
     * @param float $price
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function setProductInformation(int $product_id, string $name, float $price): bool
    {
        $this->beginTransaction();
        
        $stmt = $this->connection->prepare('INSERT INTO ' . self::PRODUCTS_EDITS . '(product, name, price) VALUES (:id, :name, :price)');
        $stmt->execute(array(
            'id' => $product_id,
            'name' => $name,
            'price' => $price
        ));
        $this->checkUpdate($stmt, 'unable to update product information');
        
        $this->commit();
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
        $this->beginTransaction();
        
        $stmt = $this->connection->prepare('SELECT 1 FROM ' . self::PRODUCTS_GROUPS . ' WHERE id = :id LIMIT 1');
        $stmt->execute(array(
            'id' => $group_id
        ));
        if (! $stmt->fetch()) cafet_throw_error('03-005', 'the group with id ' . $group_id . ' doesn\'t exist');
            
        $stmt = $this->connection->prepare('UPDATE ' . self::PRODUCTS . ' SET product_group = :group WHERE id = :id');
        $stmt->execute(array(
            'group' => $group_id,
            'id' => $product_id
        ));
        $this->checkUpdate($stmt, 'unable to update the product group');
        
        $this->commit();
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
        $this->beginTransaction();
        
        $stmt = $this->connection->prepare('UPDATE ' . self::PRODUCTS . ' SET image = :image WHERE id = :id');
        $stmt->execute(array(
            'image' => $image_base64,
            'id' => $product_id
        ));
        $this->checkUpdate($stmt, 'unable to update product image');
        
        $this->commit();
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
        $this->beginTransaction();
        
        $stmt = $this->connection->prepare('UPDATE ' . self::PRODUCTS . ' SET viewable = :flag WHERE id = :id');
        
        $stmt->bindValue(':flag', $flag, PDO::PARAM_BOOL);
        $stmt->bindValue(':id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $this->checkUpdate($stmt, 'unable to update product visibility');
        
        $this->commit();
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
        $this->beginTransaction();
        
        $stmt = $this->connection->prepare('DELETE FROM ' . self::PRODUCTS . ' WHERE id = :id');
        $stmt->execute(array(
            'id' => $id
        ));
        $this->checkUpdate($stmt, 'unable to delete the product');
        
        $this->commit();
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
        $this->beginTransaction();
        
        $stmt = $this->connection->prepare('DELETE FROM ' . self::PRODUCTS_GROUPS . ' WHERE id = :id');
        $stmt->execute(array(
            'id' => $id
        ));
        $this->checkUpdate($stmt, 'unable to delete the product group');
        
        $this->commit();
        return true;
    }
}

