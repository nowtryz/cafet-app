<?php
namespace cafetapi\io;

use cafetapi\data\Client;
use PDO;
use PDOStatement;
use cafetapi\data\Reload;
use cafetapi\data\Calendar;
use cafetapi\data\Expense;
use cafetapi\data\FormulaBought;
use cafetapi\data\Formula;
use cafetapi\data\Product;
use cafetapi\data\ProductGroup;
use cafetapi\data\ProductBought;
use cafetapi\data\Choice;

/**
 *
 * @author Damien
 * @since API 1.0.0 (2018)
 */
class DataFetcher extends DatabaseConnection
{

    private final function check_fetch_errors(PDOStatement $stmt)
    {
        if ($stmt->errorCode() != '00000')
            parent::registerErrorOccurence($stmt);
    }

    public final function getClients(): array
    {
        $stmt = $this->connection->prepare('SELECT '
            . 'ID id, '
            . 'Email email, '
            . 'Pseudo alias, '
            . 'Nom fname, '
            . 'Prenom sname, '
            . 'adherent member, '
            . 'Credit balance, '
            . 'Annee regyear '
            . 'FROM ' . self::USERS . ' u '
            . 'WHERE u.SU = 0 '
            . 'ORDER BY u.Prenom');

        $member = false;
        $id = $registrationYear = 0;
        $email = $alias = $familyNane = $surname = $balance = '';

        $stmt->bindColumn('id', $id, PDO::PARAM_INT);
        $stmt->bindColumn('email', $email, PDO::PARAM_STR);
        $stmt->bindColumn('alias', $alias, PDO::PARAM_STR);
        $stmt->bindColumn('fname', $familyNane, PDO::PARAM_STR);
        $stmt->bindColumn('sname', $surname, PDO::PARAM_STR);
        $stmt->bindColumn('member', $member, PDO::PARAM_BOOL);
        $stmt->bindColumn('balance', $balance, PDO::PARAM_STR);
        $stmt->bindColumn('regyear', $registrationYear, PDO::PARAM_INT);

        $stmt->execute();
        $this->check_fetch_errors($stmt);

        $result = array();

        while ($stmt->fetch())
            $result[] = new Client($id, $email, $alias, $familyNane, $surname, $member, floatval($balance), $registrationYear);

        return $result;
    }

    public final function getClient(int $id): ?Client
    {
        $stmt = $this->connection->prepare('SELECT '
            . 'ID id, '
            . 'Email email, '
            . 'Pseudo alias, '
            . 'Nom fname, '
            . 'Prenom sname, '
            . 'adherent member, '
            . 'Credit balance, '
            . 'Annee regyear '
            . 'FROM ' . self::USERS . ' '
            . 'WHERE id = :id ');
        
        $member = false;
        $registrationYear = 0;
        $email = $alias = $familyNane = $surname = $balance = '';

        $stmt->bindColumn('id', $id, PDO::PARAM_INT);
        $stmt->bindColumn('email', $email, PDO::PARAM_STR);
        $stmt->bindColumn('alias', $alias, PDO::PARAM_STR);
        $stmt->bindColumn('fname', $familyNane, PDO::PARAM_STR);
        $stmt->bindColumn('sname', $surname, PDO::PARAM_STR);
        $stmt->bindColumn('member', $member, PDO::PARAM_BOOL);
        $stmt->bindColumn('balance', $balance, PDO::PARAM_STR);
        $stmt->bindColumn('regyear', $registrationYear, PDO::PARAM_INT);

        $stmt->execute(array(
            'id' => $id
        ));
        $this->check_fetch_errors($stmt);

        if ($stmt->fetch())
            return new Client($id, $email, $alias, $familyNane, $surname, $member, floatval($balance), $registrationYear);

        else
            return NULL;
    }

    public final function getClientReloads(int $client_id): array
    {
        $stmt = $this->connection->prepare('SELECT '
            . 'id id, '
            . 'user_balance balance, '
            . 'amount amount, '
            . 'details details, '
            . 'DATE_FORMAT(date, "%H") hour, '
            . 'DATE_FORMAT(date, "%i") mins, '
            . 'DATE_FORMAT(date, "%s") secs, '
            . 'DATE_FORMAT(date, "%d") day, '
            . 'DATE_FORMAT(date, "%c") month, '
            . 'DATE_FORMAT(date, "%Y") year '
            . 'FROM ' . self::RELOADS . ' '
            . 'WHERE user_id = :id '
            . 'ORDER BY date DESC');

        $id = $hour = $mins = $secs = $day = $month = $year = 0;
        $balance = $amount = $details = '';

        $stmt->bindColumn('id', $id, PDO::PARAM_INT);
        $stmt->bindColumn('balance', $balance, PDO::PARAM_STR);
        $stmt->bindColumn('amount', $amount, PDO::PARAM_STR);
        $stmt->bindColumn('details', $details, PDO::PARAM_STR);
        $stmt->bindColumn('hour', $hour, PDO::PARAM_INT);
        $stmt->bindColumn('mins', $mins, PDO::PARAM_INT);
        $stmt->bindColumn('secs', $secs, PDO::PARAM_INT);
        $stmt->bindColumn('day', $day, PDO::PARAM_INT);
        $stmt->bindColumn('month', $month, PDO::PARAM_INT);
        $stmt->bindColumn('year', $year, PDO::PARAM_INT);

        $stmt->execute(array(
            'id' => $client_id
        ));
        $this->check_fetch_errors($stmt);

        $result = array();

        while ($stmt->fetch())
            $result[] = new Reload($id, $client_id, $details, new Calendar($year, $month, $day, $hour, $mins, $secs), floatval($amount), floatval($balance));

        return $result;
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

        $stmt->execute(array(
            'id' => $client_id
        ));
        $this->check_fetch_errors($stmt);

        $result = array();

        while ($stmt->fetch())
            $result[] = new Expense($id, $client_id, new Calendar($year, $month, $day, $hour, $mins, $secs), floatval($ftotal) + floatval($ptotal), floatval($balance));

        return $result;
    }

    public final function getClientLastExpenses(int $client_id): array
    {
        $stmt = $this->connection->prepare('SELECT '
            . 'e.id id, '
            . 'e.user_balance balance, '
            . '(SELECT SUM(edit.price * f.quantity) '
            . 'FROM ' . self::FORMULAS_BOUGHT . 'f  '
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

        $stmt->execute(array(
            'id' => $client_id,
            'id2' => $client_id
        ));
        $this->check_fetch_errors($stmt);

        $result = array();

        while ($stmt->fetch())
            $result[] = new Expense($id, $client_id, new Calendar($year, $month, $day, $hour, $mins, $secs), floatval($ftotal) + floatval($ptotal), floatval($balance));

        return $result;
    }

    public final function getExpense(int $expense_id): Expense
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
            . 'FROM ' . self::EXPENSES . ' '
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

        $stmt->execute(array(
            'id' => $expense_id
        ));
        $this->check_fetch_errors($stmt);

        if ($stmt->fetch())
            return new Expense($id, $client_id, new Calendar($year, $month, $day, $hour, $mins, $secs), floatval($ftotal) + floatval($ptotal), floatval($balance));

        else
            return null;
    }

    public final function getExpenseDetails(int $expense_id): array
    {
        $result = array();

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

        $stmt->execute(array(
            'id' => $expense_id
        ));
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

        $stmt->execute(array(
            'id' => $expense_id
        ));
        $this->check_fetch_errors($stmt);

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
        
        $stmt->execute(array(
            'id' => $id
        ));
        
        $this->check_fetch_errors($stmt);
        
        if ($stmt->fetch()) {
            $date = new Calendar($year, $month, $day, $hour, $mins, $secs);
            return new ProductBought($id, $pid, $name, $client_id, floatval($price), $quantity, $date);
        }
        
        return null;
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
        
        $stmt->execute(array(
            'id' => $id
        ));
        $this->check_fetch_errors($stmt);
        
        if ($stmt->fetch()) {
            $date = new Calendar($year, $month, $day, $hour, $mins, $secs);
            return new FormulaBought($id, $fid, $name, $client_id, floatval($price), $quantity, $date);
        }
        
        return null;
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

        while ($stmt->fetch())
            $result[] = new ProductGroup($id, $name, $dname);

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

        if ($stmt->fetch())
            return new ProductGroup($id, $name, $dname);

        else
            return NULL;
    }

    public final function getGroupProducts(int $group_id, bool $show_hiddens = false): array
    {
        $stmt = $this->connection->prepare('SELECT '
            . 'p.id id, '
            . 'e.name name, '
            . 'e.price price, '
            . 'p.image image, '
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
            . 'WHERE p.product_group = :group'
            . (! $show_hiddens ? ' AND p.viewable = 1' : ''));
        
        $id  = $hour = $mins = $secs = $day = $month = $year = 0;
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

        $stmt->execute(array(
            'group' => $group_id
        ));
        $this->check_fetch_errors($stmt);

        $result = array();

        while ($stmt->fetch())
            $result[] = new Product($id, $name, $price, $group_id, $image, $viewable, new Calendar($year, $month, $day, $hour, $mins, $secs));

        return $result;
    }

    public final function getFormulaBoughtProducts(int $formula_bought_id): array
    {
        $stmt = $this->connection->prepare('SELECT '
            . 'user_id client_id '
            . 'FROM ' . self::FORMULAS_BOUGHT . ' '
            . 'WHERE id = :id');

        $client_id = 0;

        $stmt->bindColumn('client_id', $client_id, PDO::PARAM_INT);
        $stmt->execute(array(
            'id' => $formula_bought_id
        ));
        $this->check_fetch_errors($stmt);

        if (! $stmt->fetch())
            return array();

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

        $stmt->execute(array(
            'fb_id' => $formula_bought_id
        ));
        $this->check_fetch_errors($stmt);

        $result = array();

        while ($stmt->fetch())
            $result[] = new ProductBought(0, $product_id, $name, $client_id, 0, 1, new Calendar($year, $month, $day, $hour, $mins, $secs));

        return $result;
    }

    public final function getProducts(bool $show_hiddens = false): array
    {
        $stmt = $this->connection->prepare('SELECT '
            . "\n" . 'p.id id, '
            . "\n" . 'e.name name, '
            . "\n" . 'e.price price, '
            . "\n" . 'p.image image, '
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
        
        $id = $group_id = $hour = $mins = $secs = $day = $month = $year = 0;
        $name = $price = $image = '';
        $viewable = false;

        $stmt->bindColumn('id', $id, PDO::PARAM_INT);
        $stmt->bindColumn('name', $name, PDO::PARAM_STR);
        $stmt->bindColumn('price', $price, PDO::PARAM_STR);
        $stmt->bindColumn('image', $image, PDO::PARAM_STR);
        $stmt->bindColumn('pgroup', $group_id, PDO::PARAM_INT);
        $stmt->bindColumn('viewable', $viewable, PDO::PARAM_BOOL);
        $stmt->bindColumn('hour', $hour, PDO::PARAM_INT);
        $stmt->bindColumn('mins', $mins, PDO::PARAM_INT);
        $stmt->bindColumn('secs', $secs, PDO::PARAM_INT);
        $stmt->bindColumn('day', $day, PDO::PARAM_INT);
        $stmt->bindColumn('month', $month, PDO::PARAM_INT);
        $stmt->bindColumn('year', $year, PDO::PARAM_INT);

        $stmt->execute();
        $this->check_fetch_errors($stmt);

        $result = array();

        while ($stmt->fetch())
            $result[] = new Product($id, $name, $price, $group_id, $image, $viewable, new Calendar($year, $month, $day, $hour, $mins, $secs));

        return $result;
    }

    public final function getProduct(int $id): ?Product
    {
        $stmt = $this->connection->prepare('SELECT '
            . 'p.id id, '
            . 'e.name name, '
            . 'e.price price, '
            . 'p.image image, '
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
        
        $group_id = $hour = $mins = $secs = $day = $month = $year = 0;
        $name = $price = $image = '';
        $viewable = false;
        
        $stmt->bindColumn('id', $id, PDO::PARAM_INT);
        $stmt->bindColumn('name', $name, PDO::PARAM_STR);
        $stmt->bindColumn('price', $price, PDO::PARAM_STR);
        $stmt->bindColumn('image', $image, PDO::PARAM_STR);
        $stmt->bindColumn('group_id', $group_id, PDO::PARAM_INT);
        $stmt->bindColumn('viewable', $viewable, PDO::PARAM_BOOL);
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

        if ($stmt->fetch())
            return new Product($id, $name, $price, $group_id, $image, $viewable, new Calendar($year, $month, $day, $hour, $mins, $secs));

        else
            return NULL;
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

        $result = array();

        while ($stmt->fetch())
            $result[] = new Formula($id, $name, $image, $price, $viewable, new Calendar($year, $month, $day, $hour, $mins, $secs));

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

        $stmt->execute(array(
            'id' => $id
        ));
        $this->check_fetch_errors($stmt);

        if ($stmt->fetch())
            return new Formula($id, $name, $image, $price, $viewable, new Calendar($year, $month, $day, $hour, $mins, $secs));

        else
            return NULL;
    }

    public final function getFormulaChoices(int $formula_id): array
    {
        $stmt = $this->connection->prepare('SELECT '
            . 'id, '
            . 'name '
            . 'FROM ' . self::FORMULAS_CHOICES . ' '
            . 'WHERE formula = :id');

        $stmt->execute(array(
            'id' => $formula_id
        ));
        $this->check_fetch_errors($stmt);

        $datas = $stmt->fetchAll();

        $stmt = $this->connection->prepare('SELECT '
            . 'p.id id, '
            . 'e.name name, '
            . 'e.price price, '
            . 'p.image image, '
            . 'p.product_group pgroup, '
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
            . 'LEFT JOIN ' . self::FORMULAS_CHOICES_PRODUCTS . ' c '
            . 'ON c.product = p.id '
            . 'WHERE c.choice = :id');
        
        $id = $group_id = $hour = $mins = $secs = $day = $month = $year = 0;
        $name = $price = $image = '';
        $viewable = false;
        
        $stmt->bindColumn('id', $id, PDO::PARAM_INT);
        $stmt->bindColumn('name', $name, PDO::PARAM_STR);
        $stmt->bindColumn('price', $price, PDO::PARAM_STR);
        $stmt->bindColumn('image', $image, PDO::PARAM_STR);
        $stmt->bindColumn('pgroup', $group_id, PDO::PARAM_INT);
        $stmt->bindColumn('viewable', $viewable, PDO::PARAM_BOOL);
        $stmt->bindColumn('hour', $hour, PDO::PARAM_INT);
        $stmt->bindColumn('mins', $mins, PDO::PARAM_INT);
        $stmt->bindColumn('secs', $secs, PDO::PARAM_INT);
        $stmt->bindColumn('day', $day, PDO::PARAM_INT);
        $stmt->bindColumn('month', $month, PDO::PARAM_INT);
        $stmt->bindColumn('year', $year, PDO::PARAM_INT);

        $result = array();

        foreach ($datas as $data) {
            $choice = array();

            $stmt->execute(array(
                'id' => $data['id']
            ));
            $this->check_fetch_errors($stmt);

            $choice = array();

            while ($stmt->fetch())
                $choice[] = new Product($id, $name, floatval($price), $group_id, $image, $viewable, new Calendar($year, $month, $day, $hour, $mins, $secs));

            $stmt->closeCursor();

            $result[] = new Choice(intval($data['id']), strval($data['name']), $formula_id, $choice);
        }

        return $result;
    }

    public final function searchClient(string $expression): array
    {
        $stmt = $this->connection->prepare('SELECT '
            . 'ID id, '
            . 'Email email, '
            . 'Pseudo alias, '
            . 'Nom fname, '
            . 'Prenom sname, '
            . 'adherent member, '
            . 'Credit balance, '
            . 'Annee regyear '
            . 'FROM ' . self::USERS . ' u '
            . 'WHERE u.SU = 0 '
            . 'AND (u.Pseudo LIKE :expression '
            . 'OR u.Nom LIKE :expression '
            . 'OR u.Prenom LIKE :expression) '
            . 'ORDER BY u.Prenom');

        $id = $registrationYear = 0;
        $email = $alias = $familyNane = $surname = $balance = '';
        $member = false;

        $stmt->bindColumn('id', $id, PDO::PARAM_INT);
        $stmt->bindColumn('email', $email, PDO::PARAM_STR);
        $stmt->bindColumn('alias', $alias, PDO::PARAM_STR);
        $stmt->bindColumn('fname', $familyNane, PDO::PARAM_STR);
        $stmt->bindColumn('sname', $surname, PDO::PARAM_STR);
        $stmt->bindColumn('member', $member, PDO::PARAM_BOOL);
        $stmt->bindColumn('balance', $balance, PDO::PARAM_STR);
        $stmt->bindColumn('regyear', $registrationYear, PDO::PARAM_INT);
        
        $search = "%$expression%";

        $stmt->execute(array(
            'expression' =>  $search
        ));
        $this->check_fetch_errors($stmt);

        $result = array();

        while ($stmt->fetch())
            $result[] = new Client($id, $email, $alias, $familyNane, $surname, $member, floatval($balance), $registrationYear);

        return $result;
    }
}

