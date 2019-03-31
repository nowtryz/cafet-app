<?php
namespace cafetapi\io;

use cafetapi\data\Calendar;
use cafetapi\data\Expense;
use cafetapi\data\FormulaBought;
use cafetapi\data\FormulaOrdered;
use cafetapi\data\ProductBought;
use cafetapi\data\ProductOrdered;
use cafetapi\exceptions\NotEnoughtMoneyException;
use cafetapi\exceptions\RequestFailureException;
use PDO;
use cafetapi\MailManager;
use cafetapi\config\Config;
use cafetapi\Logger;

/**
 *
 * @author Damien
 *        
 */
class ExpenseManager extends Updater
{
    private static $instance;
    
    /**
     * Get singleton object
     * @return ExpenseManager the singleton of this class
     */
    public static function getInstance() : ExpenseManager
    {
        if(self::$instance === null) self::$instance = new ExpenseManager();
        return self::$instance;
    }
    
    public final function getClientExpenses(int $client_id): array
    {
        $stmt = $this->connection->prepare('SELECT '
            . 'e.id id, '
            . 'e.user_balance balance, '
            . '(SELECT SUM(edit.price * f.quantity) '
            . 'FROM ' . self::FORMULAS_BOUGHT . ' f '
            . 'LEFT JOIN ' . self::FORMULAS_EDITS . ' edit '
            . 'ON f.edit_id = edit.id '
            . 'WHERE f.expense_id = e.id '
            . 'GROUP BY f.expense_id) ftotal, '
            . '(SELECT SUM(edit.price * p.quantity) '
            . 'FROM ' . self::PRODUCTS_BOUGHT . ' p '
            . 'LEFT JOIN ' . self::PRODUCTS_EDITS . ' edit '
            . 'ON p.edit_id = edit.id '
            . 'WHERE p.expense_id = e.id '
            . 'GROUP BY p.expense_id) ptotal, '
            . 'DATE_FORMAT(e.date, "%H") hour, '
            . 'DATE_FORMAT(e.date, "%i") mins, '
            . 'DATE_FORMAT(e.date, "%s") secs, '
            . 'DATE_FORMAT(e.date, "%d") day, '
            . 'DATE_FORMAT(e.date, "%c") month, '
            . 'DATE_FORMAT(e.date, "%Y") year '
            . 'FROM ' . self::EXPENSES . ' e '
            . 'WHERE e.user_id = :id '
            . 'ORDER BY e.date DESC');
        
        $id = $hour = $mins = $secs = $day = $month = $year = 0;
        $balance = $ftotal = $ptotal = '';
        
        $stmt->bindColumn('id', $id, PDO::PARAM_INT);
        $stmt->bindColumn('balance', $balance, PDO::PARAM_STR);
        $stmt->bindColumn('ftotal', $ftotal, PDO::PARAM_STR);
        $stmt->bindColumn('ptotal', $ptotal, PDO::PARAM_STR);
        $stmt->bindColumn('hour', $hour, PDO::PARAM_INT);
        $stmt->bindColumn('mins', $mins, PDO::PARAM_INT);
        $stmt->bindColumn('secs', $secs, PDO::PARAM_INT);
        $stmt->bindColumn('day', $day, PDO::PARAM_INT);
        $stmt->bindColumn('month', $month, PDO::PARAM_INT);
        $stmt->bindColumn('year', $year, PDO::PARAM_INT);
        
        $stmt->execute([
            'id' => $client_id
        ]);
        $this->check_fetch_errors($stmt);
        
        $result = array();
        
        while ($stmt->fetch()) $result[] = new Expense($id, $client_id, new Calendar($year, $month, $day, $hour, $mins, $secs), floatval($ftotal) + floatval($ptotal), floatval($balance));
            
        return $result;
    }
    
    public final function getClientLastExpenses(int $client_id): array
    {
        $stmt = $this->connection->prepare('SELECT '
            . 'e.id id, '
            . 'e.user_balance balance, '
            . '(SELECT SUM(edit.price * f.quantity) '
            . 'FROM ' . self::FORMULAS_BOUGHT . ' f  '
            . 'LEFT JOIN ' . self::FORMULAS_EDITS . ' edit '
            . 'ON f.edit_id = edit.id '
            . 'WHERE f.expense_id = e.id '
            . 'GROUP BY f.expense_id) ftotal, '
            . '(SELECT SUM(edit.price * p.quantity) '
            . 'FROM ' . self::PRODUCTS_BOUGHT . ' p '
            . 'LEFT JOIN ' . self::PRODUCTS_EDITS . ' edit '
            . 'ON p.edit_id = edit.id '
            . 'WHERE p.expense_id = e.id '
            . 'GROUP BY p.expense_id) ptotal, '
            . 'DATE_FORMAT(e.date, "%H") hour, '
            . 'DATE_FORMAT(e.date, "%i") mins, '
            . 'DATE_FORMAT(e.date, "%s") secs, '
            . 'DATE_FORMAT(e.date, "%d") day, '
            . 'DATE_FORMAT(e.date, "%c") month, '
            . 'DATE_FORMAT(e.date, "%Y") year '
            . 'FROM ' . self::EXPENSES . ' e '
            . 'WHERE e.user_id = :id '
            . 'AND e.date > (SELECT MAX(date) FROM ' . self::RELOADS . ' WHERE user_id = :id2) '
            . 'ORDER BY e.date DESC');
        
        $id = $hour = $mins = $secs = $day = $month = $year = 0;
        $balance = $ftotal = $ptotal = '';
        
        $stmt->bindColumn('id', $id, PDO::PARAM_INT);
        $stmt->bindColumn('balance', $balance, PDO::PARAM_STR);
        $stmt->bindColumn('ftotal', $ftotal, PDO::PARAM_STR);
        $stmt->bindColumn('ptotal', $ptotal, PDO::PARAM_STR);
        $stmt->bindColumn('hour', $hour, PDO::PARAM_INT);
        $stmt->bindColumn('mins', $mins, PDO::PARAM_INT);
        $stmt->bindColumn('secs', $secs, PDO::PARAM_INT);
        $stmt->bindColumn('day', $day, PDO::PARAM_INT);
        $stmt->bindColumn('month', $month, PDO::PARAM_INT);
        $stmt->bindColumn('year', $year, PDO::PARAM_INT);
        
        $stmt->execute([
            'id' => $client_id,
            'id2' => $client_id
        ]);
        $this->check_fetch_errors($stmt);
        
        $result = [];
        
        while ($stmt->fetch()) $result[] = new Expense($id, $client_id, new Calendar($year, $month, $day, $hour, $mins, $secs), floatval($ftotal) + floatval($ptotal), floatval($balance));
            
        return $result;
    }
    
    public final function getExpense(int $expense_id): ?Expense
    {
        $stmt = $this->connection->prepare('SELECT '
            . 'e.id id, '
            . 'e.user_balance balance, '
            . 'e.user_id client_id, '
            . '(SELECT SUM(edit.price * f.quantity) '
            . 'FROM ' . self::FORMULAS_BOUGHT . ' f '
            . 'LEFT JOIN ' . self::FORMULAS_EDITS . ' edit '
            . 'ON f.edit_id = edit.id '
            . 'WHERE f.expense_id = e.id '
            . 'GROUP BY f.expense_id) ftotal, '
            . '(SELECT SUM(edit.price * p.quantity) '
            . 'FROM ' . self::PRODUCTS_BOUGHT . ' p '
            . 'LEFT JOIN ' . self::PRODUCTS_EDITS . ' edit '
            . 'ON p.edit_id = edit.id '
            . 'WHERE p.expense_id = e.id '
            . 'GROUP BY p.expense_id) ptotal, '
            . 'DATE_FORMAT(e.date, "%H") hour, '
            . 'DATE_FORMAT(e.date, "%i") mins, '
            . 'DATE_FORMAT(e.date, "%s") secs, '
            . 'DATE_FORMAT(e.date, "%d") day, '
            . 'DATE_FORMAT(e.date, "%c") month, '
            . 'DATE_FORMAT(e.date, "%Y") year '
            . 'FROM ' . self::EXPENSES . ' e '
            . 'WHERE e.id = :id');
        
        $id = $client_id = $hour = $mins = $secs = $day = $month = $year = 0;
        $balance = $ftotal = $ptotal = '';
        
        $stmt->bindColumn('id', $id, PDO::PARAM_INT);
        $stmt->bindColumn('balance', $balance, PDO::PARAM_STR);
        $stmt->bindColumn('client_id', $client_id, PDO::PARAM_INT);
        $stmt->bindColumn('ftotal', $ftotal, PDO::PARAM_STR);
        $stmt->bindColumn('ptotal', $ptotal, PDO::PARAM_STR);
        $stmt->bindColumn('hour', $hour, PDO::PARAM_INT);
        $stmt->bindColumn('mins', $mins, PDO::PARAM_INT);
        $stmt->bindColumn('secs', $secs, PDO::PARAM_INT);
        $stmt->bindColumn('day', $day, PDO::PARAM_INT);
        $stmt->bindColumn('month', $month, PDO::PARAM_INT);
        $stmt->bindColumn('year', $year, PDO::PARAM_INT);
        
        $stmt->execute([
            'id' => $expense_id
        ]);
        $this->check_fetch_errors($stmt);
        
        if ($stmt->fetch()) return new Expense($id, $client_id, new Calendar($year, $month, $day, $hour, $mins, $secs), floatval($ftotal) + floatval($ptotal), floatval($balance));
            
        else return null;
    }
    
    public final function getExpenses(): array
    {
        $stmt = $this->connection->prepare('SELECT '
            . 'e.id id, '
            . 'e.user_balance balance, '
            . 'e.user_id client_id, '
            . '(SELECT SUM(edit.price * f.quantity) '
            . 'FROM ' . self::FORMULAS_BOUGHT . ' f '
            . 'LEFT JOIN ' . self::FORMULAS_EDITS . ' edit '
            . 'ON f.edit_id = edit.id '
            . 'WHERE f.expense_id = e.id '
            . 'GROUP BY f.expense_id) ftotal, '
            . '(SELECT SUM(edit.price * p.quantity) '
            . 'FROM ' . self::PRODUCTS_BOUGHT . ' p '
            . 'LEFT JOIN ' . self::PRODUCTS_EDITS . ' edit '
            . 'ON p.edit_id = edit.id '
            . 'WHERE p.expense_id = e.id '
            . 'GROUP BY p.expense_id) ptotal, '
            . 'DATE_FORMAT(e.date, "%H") hour, '
            . 'DATE_FORMAT(e.date, "%i") mins, '
            . 'DATE_FORMAT(e.date, "%s") secs, '
            . 'DATE_FORMAT(e.date, "%d") day, '
            . 'DATE_FORMAT(e.date, "%c") month, '
            . 'DATE_FORMAT(e.date, "%Y") year '
            . 'FROM ' . self::EXPENSES . ' e');
        
        $id = $client_id = $hour = $mins = $secs = $day = $month = $year = 0;
        $balance = $ftotal = $ptotal = '';
        
        $stmt->bindColumn('id', $id, PDO::PARAM_INT);
        $stmt->bindColumn('balance', $balance, PDO::PARAM_STR);
        $stmt->bindColumn('client_id', $client_id, PDO::PARAM_INT);
        $stmt->bindColumn('ftotal', $ftotal, PDO::PARAM_STR);
        $stmt->bindColumn('ptotal', $ptotal, PDO::PARAM_STR);
        $stmt->bindColumn('hour', $hour, PDO::PARAM_INT);
        $stmt->bindColumn('mins', $mins, PDO::PARAM_INT);
        $stmt->bindColumn('secs', $secs, PDO::PARAM_INT);
        $stmt->bindColumn('day', $day, PDO::PARAM_INT);
        $stmt->bindColumn('month', $month, PDO::PARAM_INT);
        $stmt->bindColumn('year', $year, PDO::PARAM_INT);
        
        $stmt->execute();
        $this->check_fetch_errors($stmt);
        
        $expenses = [];
        
        while ($stmt->fetch()) $expenses[] = new Expense($id, $client_id, new Calendar($year, $month, $day, $hour, $mins, $secs), floatval($ftotal) + floatval($ptotal), floatval($balance));
            
        return $expenses;
    }
    
    public final function getExpenseDetails(int $expense_id): array
    {
        $result = [];
        
        $stmt = $this->connection->prepare('SELECT '
            . 'b.id id, '
            . 'e.name name, '
            . 'e.price price, '
            . 'b.formula_id fid, '
            . 'b.quantity quantity, '
            . 'b.user_id client_id, '
            . 'DATE_FORMAT(b.date, "%H") hour, '
            . 'DATE_FORMAT(b.date, "%i") mins, '
            . 'DATE_FORMAT(b.date, "%s") secs, '
            . 'DATE_FORMAT(b.date, "%d") day, '
            . 'DATE_FORMAT(b.date, "%c") month, '
            . 'DATE_FORMAT(b.date, "%Y") year '
            . 'FROM ' . self::FORMULAS_BOUGHT . ' b '
            . 'LEFT JOIN ' . self::FORMULAS_EDITS . ' e '
            . 'ON b.edit_id = e.id '
            . 'WHERE b.expense_id = :id');
        
        $id = $fid = $quantity = $client_id = $hour = $mins = $secs = $day = $month = $year = 0;
        $name = $price = '';
        
        $stmt->bindColumn('id', $id, PDO::PARAM_INT);
        $stmt->bindColumn('name', $name, PDO::PARAM_STR);
        $stmt->bindColumn('price', $price, PDO::PARAM_STR);
        $stmt->bindColumn('fid', $fid, PDO::PARAM_INT);
        $stmt->bindColumn('quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindColumn('client_id', $client_id, PDO::PARAM_INT);
        $stmt->bindColumn('hour', $hour, PDO::PARAM_INT);
        $stmt->bindColumn('mins', $mins, PDO::PARAM_INT);
        $stmt->bindColumn('secs', $secs, PDO::PARAM_INT);
        $stmt->bindColumn('day', $day, PDO::PARAM_INT);
        $stmt->bindColumn('month', $month, PDO::PARAM_INT);
        $stmt->bindColumn('year', $year, PDO::PARAM_INT);
        
        $stmt->execute([
            'id' => $expense_id
        ]);
        $this->check_fetch_errors($stmt);
        
        while ($stmt->fetch()) {
            $date = new Calendar($year, $month, $day, $hour, $mins, $secs);
            $result[] = new FormulaBought($id, $fid, $name, $client_id, floatval($price), $quantity, $date);
        }
        
        $stmt = $this->connection->prepare('SELECT '
            . 'b.id id, '
            . 'e.name name, '
            . 'e.price price, '
            . 'b.product_id pid, '
            . 'b.quantity quantity, '
            . 'b.user_id client_id, '
            . 'DATE_FORMAT(b.date, "%H") hour, '
            . 'DATE_FORMAT(b.date, "%i") mins, '
            . 'DATE_FORMAT(b.date, "%s") secs, '
            . 'DATE_FORMAT(b.date, "%d") day, '
            . 'DATE_FORMAT(b.date, "%c") month, '
            . 'DATE_FORMAT(b.date, "%Y") year '
            . 'FROM ' . self::PRODUCTS_BOUGHT . ' b '
            . 'LEFT JOIN ' . self::PRODUCTS_EDITS . ' e '
            . 'ON b.edit_id = e.id '
            . 'WHERE b.expense_id = :id');
        
        $id = $pid = $quantity = $client_id = $hour = $mins = $secs = $day = $month = $year = 0;
        $name = $price = '';
        
        $stmt->bindColumn('id', $id, PDO::PARAM_INT);
        $stmt->bindColumn('name', $name, PDO::PARAM_STR);
        $stmt->bindColumn('price', $price, PDO::PARAM_STR);
        $stmt->bindColumn('pid', $pid, PDO::PARAM_INT);
        $stmt->bindColumn('quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindColumn('client_id', $client_id, PDO::PARAM_INT);
        $stmt->bindColumn('hour', $hour, PDO::PARAM_INT);
        $stmt->bindColumn('mins', $mins, PDO::PARAM_INT);
        $stmt->bindColumn('secs', $secs, PDO::PARAM_INT);
        $stmt->bindColumn('day', $day, PDO::PARAM_INT);
        $stmt->bindColumn('month', $month, PDO::PARAM_INT);
        $stmt->bindColumn('year', $year, PDO::PARAM_INT);
        
        $stmt->execute([
            'id' => $expense_id
        ]);
        $this->check_fetch_errors($stmt);
        
        while ($stmt->fetch()) {
            $date = new Calendar($year, $month, $day, $hour, $mins, $secs);
            $result[] = new ProductBought($id, $pid, $name, $client_id, floatval($price), $quantity, $date);
        }
        
        return $result;
    }
    
    public final function getProductsBought(): array
    {
        $stmt = $this->connection->prepare('SELECT '
            . 'b.id id, '
            . 'e.name name, '
            . 'e.price price, '
            . 'b.product_id pid, '
            . 'b.quantity quantity, '
            . 'b.user_id client_id, '
            . 'DATE_FORMAT(b.date, "%H") hour, '
            . 'DATE_FORMAT(b.date, "%i") mins, '
            . 'DATE_FORMAT(b.date, "%s") secs, '
            . 'DATE_FORMAT(b.date, "%d") day, '
            . 'DATE_FORMAT(b.date, "%c") month, '
            . 'DATE_FORMAT(b.date, "%Y") year '
            . 'FROM ' . self::PRODUCTS_BOUGHT . ' b '
            . 'LEFT JOIN ' . self::PRODUCTS_EDITS . ' e '
            . 'ON b.edit_id = e.id ');
        
        $id = $pid = $quantity = $client_id = $hour = $mins = $secs = $day = $month = $year = 0;
        $name = $price = '';
        
        $stmt->bindColumn('id', $id, PDO::PARAM_INT);
        $stmt->bindColumn('name', $name, PDO::PARAM_STR);
        $stmt->bindColumn('price', $price, PDO::PARAM_STR);
        $stmt->bindColumn('pid', $pid, PDO::PARAM_INT);
        $stmt->bindColumn('quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindColumn('client_id', $client_id, PDO::PARAM_INT);
        $stmt->bindColumn('hour', $hour, PDO::PARAM_INT);
        $stmt->bindColumn('mins', $mins, PDO::PARAM_INT);
        $stmt->bindColumn('secs', $secs, PDO::PARAM_INT);
        $stmt->bindColumn('day', $day, PDO::PARAM_INT);
        $stmt->bindColumn('month', $month, PDO::PARAM_INT);
        $stmt->bindColumn('year', $year, PDO::PARAM_INT);
        
        $stmt->execute();
        
        $this->check_fetch_errors($stmt);
        
        $result = [];
        
        while ($stmt->fetch()) {
            $date = new Calendar($year, $month, $day, $hour, $mins, $secs);
            $result[] = new ProductBought($id, $pid, $name, $client_id, floatval($price), $quantity, $date);
        }
        
        return $result;
    }
    
    public final function getProductBought(int $id): ?ProductBought
    {
        $stmt = $this->connection->prepare('SELECT '
            . 'b.id id, '
            . 'e.name name, '
            . 'e.price price, '
            . 'b.product_id pid, '
            . 'b.quantity quantity, '
            . 'b.user_id client_id, '
            . 'DATE_FORMAT(b.date, "%H") hour, '
            . 'DATE_FORMAT(b.date, "%i") mins, '
            . 'DATE_FORMAT(b.date, "%s") secs, '
            . 'DATE_FORMAT(b.date, "%d") day, '
            . 'DATE_FORMAT(b.date, "%c") month, '
            . 'DATE_FORMAT(b.date, "%Y") year '
            . 'FROM ' . self::PRODUCTS_BOUGHT . ' b '
            . 'LEFT JOIN ' . self::PRODUCTS_EDITS . ' e '
            . 'ON b.edit_id = e.id '
            . 'WHERE b.id = :id');
        
        $pid = $quantity = $client_id = $hour = $mins = $secs = $day = $month = $year = 0;
        $name = $price = '';
        
        $stmt->bindColumn('id', $id, PDO::PARAM_INT);
        $stmt->bindColumn('name', $name, PDO::PARAM_STR);
        $stmt->bindColumn('price', $price, PDO::PARAM_STR);
        $stmt->bindColumn('pid', $pid, PDO::PARAM_INT);
        $stmt->bindColumn('quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindColumn('client_id', $client_id, PDO::PARAM_INT);
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
        
        if ($stmt->fetch()) {
            $date = new Calendar($year, $month, $day, $hour, $mins, $secs);
            return new ProductBought($id, $pid, $name, $client_id, floatval($price), $quantity, $date);
        }
        
        return null;
    }
    
    public final function getFormulasBought(): array
    {
        $stmt = $this->connection->prepare('SELECT '
            . 'b.id id, '
            . 'e.name name, '
            . 'e.price price, '
            . 'b.formula_id fid, '
            . 'b.quantity quantity, '
            . 'b.user_id client_id, '
            . 'DATE_FORMAT(b.date, "%H") hour, '
            . 'DATE_FORMAT(b.date, "%i") mins, '
            . 'DATE_FORMAT(b.date, "%s") secs, '
            . 'DATE_FORMAT(b.date, "%d") day, '
            . 'DATE_FORMAT(b.date, "%c") month, '
            . 'DATE_FORMAT(b.date, "%Y") year '
            . 'FROM ' . self::FORMULAS_BOUGHT . ' b '
            . 'LEFT JOIN ' . self::FORMULAS_EDITS . ' e '
            . 'ON b.edit_id = e.id ');
        
        $id = $fid = $quantity = $client_id = $hour = $mins = $secs = $day = $month = $year = 0;
        $name = $price = '';
        
        $stmt->bindColumn('id', $id, PDO::PARAM_INT);
        $stmt->bindColumn('name', $name, PDO::PARAM_STR);
        $stmt->bindColumn('price', $price, PDO::PARAM_STR);
        $stmt->bindColumn('fid', $fid, PDO::PARAM_INT);
        $stmt->bindColumn('quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindColumn('client_id', $client_id, PDO::PARAM_INT);
        $stmt->bindColumn('hour', $hour, PDO::PARAM_INT);
        $stmt->bindColumn('mins', $mins, PDO::PARAM_INT);
        $stmt->bindColumn('secs', $secs, PDO::PARAM_INT);
        $stmt->bindColumn('day', $day, PDO::PARAM_INT);
        $stmt->bindColumn('month', $month, PDO::PARAM_INT);
        $stmt->bindColumn('year', $year, PDO::PARAM_INT);
        
        $stmt->execute();
        $this->check_fetch_errors($stmt);
        
        $result = [];
        
        while ($stmt->fetch()) {
            $date = new Calendar($year, $month, $day, $hour, $mins, $secs);
            $result[] = new FormulaBought($id, $fid, $name, $client_id, floatval($price), $quantity, $date);
        }
        
        return $result;
    }
    
    public final function getFormulaBought(int $id): ?FormulaBought
    {
        $stmt = $this->connection->prepare('SELECT '
            . 'b.id id, '
            . 'e.name name, '
            . 'e.price price, '
            . 'b.formula_id fid, '
            . 'b.quantity quantity, '
            . 'b.user_id client_id, '
            . 'DATE_FORMAT(b.date, "%H") hour, '
            . 'DATE_FORMAT(b.date, "%i") mins, '
            . 'DATE_FORMAT(b.date, "%s") secs, '
            . 'DATE_FORMAT(b.date, "%d") day, '
            . 'DATE_FORMAT(b.date, "%c") month, '
            . 'DATE_FORMAT(b.date, "%Y") year '
            . 'FROM ' . self::FORMULAS_BOUGHT . ' b '
            . 'LEFT JOIN ' . self::FORMULAS_EDITS . ' e '
            . 'ON b.edit_id = e.id '
            . 'WHERE b.id = :id');
        
        $fid = $quantity = $client_id = $hour = $mins = $secs = $day = $month = $year = 0;
        $name = $price = '';
        
        $stmt->bindColumn('id', $id, PDO::PARAM_INT);
        $stmt->bindColumn('name', $name, PDO::PARAM_STR);
        $stmt->bindColumn('price', $price, PDO::PARAM_STR);
        $stmt->bindColumn('fid', $fid, PDO::PARAM_INT);
        $stmt->bindColumn('quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindColumn('client_id', $client_id, PDO::PARAM_INT);
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
        
        if ($stmt->fetch()) {
            $date = new Calendar($year, $month, $day, $hour, $mins, $secs);
            return new FormulaBought($id, $fid, $name, $client_id, floatval($price), $quantity, $date);
        }
        
        return null;
    }
    
    public final function getFormulaBoughtProducts(int $formula_bought_id): array
    {
        $stmt = $this->connection->prepare('SELECT '
            . 'user_id client_id '
            . 'FROM ' . self::FORMULAS_BOUGHT . ' '
            . 'WHERE id = :id');
        
        $client_id = 0;
        
        $stmt->bindColumn('client_id', $client_id, PDO::PARAM_INT);
        $stmt->execute([
            'id' => $formula_bought_id
        ]);
        $this->check_fetch_errors($stmt);
        
        if (! $stmt->fetch()) return [];
            
        $stmt = $this->connection->prepare('SELECT '
            . 'b.product_id product_id, '
            . 'e.name name, '
            . 'DATE_FORMAT(b.date, "%H") hour, '
            . 'DATE_FORMAT(b.date, "%i") mins, '
            . 'DATE_FORMAT(b.date, "%s") secs, '
            . 'DATE_FORMAT(b.date, "%d") day, '
            . 'DATE_FORMAT(b.date, "%c") month, '
            . 'DATE_FORMAT(b.date, "%Y") year '
            . 'FROM ' . self::FORMULAS_BOUGHT_PRODUCTS . ' b '
            . 'LEFT JOIN ' . self::PRODUCTS_EDITS . ' e '
            . 'ON b.product_edit = e.id '
            . 'WHERE b.transaction_id = :fb_id');
        
        $product_id  = $hour = $mins = $secs = $day = $month = $year = 0;
        $name = '';
        
        $stmt->bindColumn('product_id', $product_id, PDO::PARAM_INT);
        $stmt->bindColumn('name', $name, PDO::PARAM_STR);
        $stmt->bindColumn('hour', $hour, PDO::PARAM_INT);
        $stmt->bindColumn('mins', $mins, PDO::PARAM_INT);
        $stmt->bindColumn('secs', $secs, PDO::PARAM_INT);
        $stmt->bindColumn('day', $day, PDO::PARAM_INT);
        $stmt->bindColumn('month', $month, PDO::PARAM_INT);
        $stmt->bindColumn('year', $year, PDO::PARAM_INT);
        
        $stmt->execute([
            'fb_id' => $formula_bought_id
        ]);
        $this->check_fetch_errors($stmt);
        
        $result = [];
        
        while ($stmt->fetch()) $result[] = new ProductBought(0, $product_id, $name, $client_id, 0, 1, new Calendar($year, $month, $day, $hour, $mins, $secs));
            
        return $result;
    }
    
    /**
     * Save a purchase in the database
     *
     * @param int $client_id
     * @param array $order array of Ordered
     * @return bool if the query have correctly been completed
     * @since API 0.1.0 (2018)
     */
    public final function saveOrder(int $client_id, array $order): bool
    {
        $this->beginTransaction();
        
        $client = ClientManager::getInstance()->getClient($client_id);
        if (!$client) throw new RequestFailureException('Unexisting client');
        
        // Save expense
        $stmt = $this->connection->prepare('INSERT INTO ' . self::EXPENSES . ' (user_id) VALUES (:id)');
        $stmt->execute([
            'id' => $client_id
        ]);
        $this->checkUpdate($stmt, 'unable to save order');
        $expense_id = $this->connection->lastInsertId();
        
        // Save expense details
        foreach ($order as $entry) {
            if ($entry instanceof ProductOrdered) {
                $stmt = $this->connection->prepare('SELECT 1 FROM ' . self::PRODUCTS . ' WHERE id = :id LIMIT 1');
                $stmt->execute([
                    'id' => $entry->getId()
                ]);
                if (! $stmt->fetch())
                    Logger::throwError('03-005', 'product with id ' . $entry->getId() . ' doesn\'t exist');
                    $stmt->closeCursor();
                    
                    $stmt = $this->connection->prepare('INSERT '
                        . 'INTO ' . self::PRODUCTS_BOUGHT . '(expense_id, product_id, user_id, quantity) '
                        . 'VALUES (:expense, :product, :client, :quantity)');
                    $stmt->execute([
                        'expense' => $expense_id,
                        'product' => $entry->getId(),
                        'client' => $client_id,
                        'quantity' => $entry->getAmount()
                    ]);
                    $this->checkUpdate($stmt, 'unable to save expense details');
            } elseif ($entry instanceof FormulaOrdered) {
                
                $stmt = $this->connection->prepare('SELECT 1 FROM ' . self::FORMULAS . ' WHERE id = :id LIMIT 1');
                $stmt->execute([
                    'id' => $entry->getId()
                ]);
                if (! $stmt->fetch())
                    Logger::throwError('03-005', 'formula with id ' . $entry->getId() . ' doesn\'t exist');
                    $stmt->closeCursor();
                    
                    $stmt = $this->connection->prepare('INSERT '
                        . 'INTO ' . self::FORMULAS_BOUGHT . '(expense_id, formula_id, user_id, quantity) '
                        . 'VALUES (:expense,:formula,:client,:quantity)');
                    $stmt->execute([
                        'expense' => $expense_id,
                        'formula' => $entry->getId(),
                        'client' => $client_id,
                        'quantity' => $entry->getAmount()
                    ]);
                    $this->checkUpdate($stmt, 'unable to save expense details');
                    
                    $i = 0;
                    $size = count($entry->getProducts());
                    $fb_id = $this->connection->lastInsertId();
                    $parameters = [
                        'id' => $fb_id
                    ];
                    
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
        
        $expense = ExpenseManager::getInstance()->getExpense($expense_id);
        
        if (($delta = $expense->getBalanceAfterTransaction() - Config::balance_limit) < 0) {
            $this->connection->rollBack();
            $backtrace = debug_backtrace()[1];
            throw new NotEnoughtMoneyException('missing ' . abs($delta) . '€ to perform this action', null, null, $backtrace['file'], $backtrace['line']);
            return false;
        } else {
            $this->commit();
            try {
                if ($client->getMailPreference('payment_notice')) MailManager::paymentNotice($client, $expense)->send();
                if ($expense->getBalanceAfterTransaction() < Config::balance_warning) {
                    if ($client->getMailPreference('reload_request')) MailManager::reloadRequest($client)->send();
                }
            } catch (\Exception | \Error $e) {
                Logger::log($e);
            }
        }
        
        return true;
    }
}

