<?php
namespace cafetapi\io;

use cafetapi\data\Calendar;
use cafetapi\data\Choice;
use cafetapi\data\Formula;
use cafetapi\data\Product;
use PDO;

/**
 *
 * @author Damien
 *        
 */
class FormulaManager extends Updater
{
    private static $instance;
    
    /**
     * Get singleton object
     * @return FormulaManager the singleton of this class
     */
    public static function getInstance() : FormulaManager
    {
        if(self::$instance === null) self::$instance = new FormulaManager();
        return self::$instance;
    }
    
    public final function getFormulas(bool $show_hiddens = false): array
    {
        $stmt = $this->connection->prepare('SELECT '
            . 'f.id id, '
            . 'e.name name, '
            . 'f.image image, '
            . 'e.price price, '
            . 'f.viewable viewable, '
            . 'DATE_FORMAT(e.edit, "%H") hour, '
            . 'DATE_FORMAT(e.edit, "%i") mins, '
            . 'DATE_FORMAT(e.edit, "%s") secs, '
            . 'DATE_FORMAT(e.edit, "%d") day, '
            . 'DATE_FORMAT(e.edit, "%c") month, '
            . 'DATE_FORMAT(e.edit, "%Y") year '
            . 'FROM ' . self::FORMULAS . ' f '
            . 'LEFT JOIN ' . self::FORMULAS_EDITS . ' e '
            . 'ON f.last_edit = e.id'
            . (! $show_hiddens ? ' WHERE f.viewable = 1' : ''));
        
        $id = $hour = $mins = $secs = $day = $month = $year = 0;
        $name = $price = $image = '';
        $viewable = false;
        
        $stmt->bindColumn('id', $id, PDO::PARAM_INT);
        $stmt->bindColumn('name', $name, PDO::PARAM_STR);
        $stmt->bindColumn('price', $price, PDO::PARAM_STR);
        $stmt->bindColumn('image', $image, PDO::PARAM_STR);
        $stmt->bindColumn('viewable', $viewable, PDO::PARAM_BOOL);
        $stmt->bindColumn('hour', $hour, PDO::PARAM_INT);
        $stmt->bindColumn('mins', $mins, PDO::PARAM_INT);
        $stmt->bindColumn('secs', $secs, PDO::PARAM_INT);
        $stmt->bindColumn('day', $day, PDO::PARAM_INT);
        $stmt->bindColumn('month', $month, PDO::PARAM_INT);
        $stmt->bindColumn('year', $year, PDO::PARAM_INT);
        
        $stmt->execute();
        $this->check_fetch_errors($stmt);
        
        $result = [];
        
        while ($stmt->fetch()) $result[] = new Formula($id, $name, $image, $price, $viewable, new Calendar($year, $month, $day, $hour, $mins, $secs));
            
        return $result;
    }
    
    public final function getFormula(int $id): ?Formula
    {
        $stmt = $this->connection->prepare('SELECT '
            . 'f.id id, '
            . 'e.name name, '
            . 'f.image image, '
            . 'e.price price, '
            . 'f.viewable viewable, '
            . 'DATE_FORMAT(e.edit, "%H") hour, '
            . 'DATE_FORMAT(e.edit, "%i") mins, '
            . 'DATE_FORMAT(e.edit, "%s") secs, '
            . 'DATE_FORMAT(e.edit, "%d") day, '
            . 'DATE_FORMAT(e.edit, "%c") month, '
            . 'DATE_FORMAT(e.edit, "%Y") year '
            . 'FROM ' . self::FORMULAS . ' f '
            . 'LEFT JOIN ' . self::FORMULAS_EDITS . ' e '
            . 'ON f.last_edit = e.id '
            . 'WHERE f.id = :id');
        
        $hour = $mins = $secs = $day = $month = $year = 0;
        $name = $price = $image = '';
        $viewable = false;
        
        $stmt->bindColumn('id', $id, PDO::PARAM_INT);
        $stmt->bindColumn('name', $name, PDO::PARAM_STR);
        $stmt->bindColumn('price', $price, PDO::PARAM_STR);
        $stmt->bindColumn('image', $image, PDO::PARAM_STR);
        $stmt->bindColumn('viewable', $viewable, PDO::PARAM_BOOL);
        $stmt->bindColumn('hour', $hour, PDO::PARAM_INT);
        $stmt->bindColumn('mins', $mins, PDO::PARAM_INT);
        $stmt->bindColumn('secs', $secs, PDO::PARAM_INT);
        $stmt->bindColumn('day', $day, PDO::PARAM_INT);
        $stmt->bindColumn('month', $month, PDO::PARAM_INT);
        $stmt->bindColumn('year', $year, PDO::PARAM_INT);
        
        $stmt->execute([
            'id' => $id
        ]);
        $this->check_fetch_errors($stmt);
        
        if ($stmt->fetch()) return new Formula($id, $name, $image, $price, $viewable, new Calendar($year, $month, $day, $hour, $mins, $secs));
            
        else return NULL;
    }
    
    public final function getFormulaChoicesIDs(int $formula_id): array
    {
        $stmt = $this->connection->prepare('SELECT '
            . 'id '
            . 'FROM ' . self::FORMULAS_CHOICES . ' '
            . 'WHERE formula = :id');
        
        $stmt->execute(['id' => $formula_id]);
        $this->check_fetch_errors($stmt);
        $datas = $stmt->fetchAll();
        
        $choices = array();
        foreach ($datas as $data) ['id' => $choices[]] = $data;
        return $choices;
    }
    
    public final function getFormulaChoices(int $formula_id): array
    {
        $stmt = $this->connection->prepare('SELECT '
            . 'id, '
            . 'name '
            . 'FROM ' . self::FORMULAS_CHOICES . ' '
            . 'WHERE formula = :id');
        
        $stmt->execute([
            'id' => $formula_id
        ]);
        $this->check_fetch_errors($stmt);
        
        $datas = $stmt->fetchAll();
        
        $stmt = $this->connection->prepare('SELECT '
            . 'p.id id, '
            . 'e.name name, '
            . 'e.price price, '
            . 'p.image image, '
            . 'p.product_group pgroup, '
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
            . 'LEFT JOIN ' . self::FORMULAS_CHOICES_PRODUCTS . ' c '
            . 'ON c.product = p.id '
            . 'WHERE c.choice = :id');
        
        $id = $stock = $group_id = $hour = $mins = $secs = $day = $month = $year = 0;
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
        
        $result = [];
        
        foreach ($datas as $data) {
            $choice = array();
            
            $stmt->execute([
                'id' => $data['id']
            ]);
            $this->check_fetch_errors($stmt);
            
            $choice = array();
            
            while ($stmt->fetch()) $choice[] = new Product($id, $name, floatval($price), $group_id, $image, $viewable, $stock, new Calendar($year, $month, $day, $hour, $mins, $secs));
                
            $stmt->closeCursor();
            
            $result[] = new Choice(intval($data['id']), strval($data['name']), $formula_id, $choice);
        }
        
        return $result;
    }
    
    public final function getChoice(int $choice_id): ?Choice
    {
        $stmt = $this->connection->prepare('SELECT '
            . 'formula, '
            . 'name '
            . 'FROM ' . self::FORMULAS_CHOICES . ' '
            . 'WHERE id = :id');
        
        $formula_id = 0;
        $choice_name = '';
        
        $stmt->bindColumn('formula', $formula_id, PDO::PARAM_INT);
        $stmt->bindColumn('name', $choice_name, PDO::PARAM_STR);
        
        $stmt->execute([
            'id' => $choice_id
        ]);
        
        $this->check_fetch_errors($stmt);
        
        $stmt->fetch();
        
        $stmt = $this->connection->prepare('SELECT '
            . 'p.id id, '
            . 'e.name name, '
            . 'e.price price, '
            . 'p.image image, '
            . 'p.product_group pgroup, '
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
            . 'LEFT JOIN ' . self::FORMULAS_CHOICES_PRODUCTS . ' c '
            . 'ON c.product = p.id '
            . 'WHERE c.choice = :id');
        
        $id = $stock = $group_id = $hour = $mins = $secs = $day = $month = $year = 0;
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
        
        $stmt->execute([
            'id' => $choice_id
        ]);
        
        $this->check_fetch_errors($stmt);
        
        $choice = [];
        
        while ($stmt->fetch()) $choice[] = new Product($id, $name, floatval($price), $group_id, $image, $viewable, $stock, new Calendar($year, $month, $day, $hour, $mins, $secs));
            
        $stmt->closeCursor();
            
        return new Choice($choice_id, $choice_name, $formula_id, $choice);
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
        $this->beginTransaction();
        $this->connection->exec('SET foreign_key_checks=0');
        
        $stmt = $this->connection->prepare('INSERT INTO ' . self::FORMULAS . '(id) VALUES (NULL)');
        $stmt->execute();
        $this->checkUpdate($stmt, 'unable to add the formula');
        
        $stmt->closeCursor();
        $formula_id = $this->connection->lastInsertId();
        $stmt = $this->connection->prepare('INSERT INTO ' . self::FORMULAS_EDITS . '(formula, name) VALUES(:formula,:name)');
        $stmt->execute([
            'formula' => $formula_id,
            'name' => $name
        ]);
        $this->checkUpdate($stmt, 'unable to add the formula');
        $stmt->closeCursor();
        
        $this->connection->exec('SET foreign_key_checks=1');
        $this->commit();
        return $this->getFormula($formula_id);
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
        $this->beginTransaction();
        
        $stmt = $this->connection->prepare('INSERT INTO ' . self::FORMULAS_CHOICES . '(formula, name) VALUES (:formula, :name)');
        $stmt->execute([
            'formula' => $formula_id,
            'name' => $name
        ]);
        $this->checkUpdate($stmt, 'unable to add the choice');
        
        $id = $this->connection->lastInsertId();
        $this->commit();
        return $this->getChoice($id);
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
        $this->beginTransaction();
        
        $stmt = $this->connection->prepare('INSERT INTO ' . self::FORMULAS_CHOICES_PRODUCTS . '(choice, product) VALUES (:choice, :product)');
        $stmt->execute([
            'choice' => $choice_id,
            'product' => $product_id
        ]);
        $this->checkUpdate($stmt, 'unable to register product');
        
        $this->commit();
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
        $this->beginTransaction();
        
        $stmt = $this->connection->prepare('DELETE FROM ' . self::FORMULAS_CHOICES_PRODUCTS . ' WHERE choice = :choice AND product = :product');
        $stmt->execute([
            'choice' => $choice_id,
            'product' => $product_id
        ]);
        $this->checkUpdate($stmt, 'unable to unregister product');
        
        $this->commit();
        return true;
    }
    
    /**
     * Unregister all products for the specified choice
     *
     * @param int $choice_id
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function removeAllProductsFromChoice(int $choice_id): bool
    {
        $this->beginTransaction();
        
        $stmt = $this->connection->prepare('DELETE FROM ' . self::FORMULAS_CHOICES_PRODUCTS . ' WHERE choice = :choice');
        $stmt->execute(['choice' => $choice_id]);
        $this->checkUpdate($stmt, 'unable to unregister product');
        
        $this->commit();
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
        $this->beginTransaction();
        
        $stmt = $this->connection->prepare('INSERT INTO ' . self::FORMULAS_EDITS . '(formula,name) VALUES (:id, :name)');
        $stmt->execute([
            'id' => $formula_id,
            'name' => $name
        ]);
        $this->checkUpdate($stmt, 'unable to update formula');
        
        $this->commit();
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
        $this->beginTransaction();
        
        $stmt = $this->connection->prepare('INSERT INTO ' . self::FORMULAS_EDITS . '(formula,price) VALUES (:id, :price)');
        $stmt->execute([
            'id' => $formula_id,
            'price' => $price
        ]);
        $this->checkUpdate($stmt, 'unable to update formula');
        
        $this->commit();
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
        $this->beginTransaction();
        
        $stmt = $this->connection->prepare('UPDATE ' . self::FORMULAS . ' SET viewable = :flag WHERE id = :id');
        
        $stmt->bindValue(':flag', $flag, PDO::PARAM_BOOL);
        $stmt->bindValue(':id', $formula_id, PDO::PARAM_INT);
        
        $stmt->execute();
        
        $this->checkUpdate($stmt, 'unable to update formula');
        
        $this->commit();
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
        $this->beginTransaction();
        
        $stmt = $this->connection->prepare('UPDATE ' . self::FORMULAS . ' SET image = :image WHERE id = :id');
        $stmt->execute([
            'image' => $image_base64,
            'id' => $formula_id
        ]);
        $this->checkUpdate($stmt, 'unable to update formula');
        
        $this->commit();
        return true;
    }
    
    /**
     * Sets the name of the choice with the given id
     * @param int $choice_id
     * @param string $name
     * @return bool
     */
    public final function setFormulaChoiceName(int $choice_id, string $name) : bool
    {
        $this->beginTransaction();
        
        $stmt = $this->connection->prepare('UPDATE ' . self::FORMULAS_CHOICES . ' SET name = :name WHERE id = :id');
        $stmt->execute([
            'name' => $name,
            'id' => $choice_id
        ]);
        $this->checkUpdate($stmt, 'unable to update formula choice');
        
        $this->commit();
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
        $this->beginTransaction();
        
        $stmt = $this->connection->prepare('DELETE FROM ' . self::FORMULAS . ' WHERE id = :id');
        $stmt->execute([
            'id' => $id
        ]);
        $this->checkUpdate($stmt, 'unable to delete the formula');
        
        $this->commit();
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
        $this->beginTransaction();
        
        $stmt = $this->connection->prepare('DELETE FROM ' . self::FORMULAS_CHOICES . ' WHERE id = :id');
        $stmt->execute([
            'id' => $id
        ]);
        $this->checkUpdate($stmt, 'unable to delete the choice');
        
        $this->commit();
        return true;
    }
}

