<?php
namespace cafetapi\io;

use PDO;

class StatsManager extends DatabaseConnection
{
    private static $instance;
    
    /**
     * Get singleton object
     * @return StatsManager the singleton of this class
     */
    public static function getInstance() : StatsManager
    {
        if(self::$instance === null) self::$instance = new StatsManager();
        return self::$instance;
    }
    
    public function getWeeklyRevenue() : float
    {
        $stmt = $this->connection->prepare("SELECT SUM(" . ReloadManager::FIELD_AMOUNT . ") revenue FROM " . self::RELOADS . " WHERE " . ReloadManager::FIELD_DATE . " >= (CURDATE() - INTERVAL 1 WEEK )");
        $revenue = 0;
        $stmt->bindColumn('revenue', $revenue);
        $stmt->execute();
        $this->check_fetch_errors($stmt);
        $stmt->fetch();
        return floatval($revenue);
    }
    
    public function getMonthlySales() : int
    {
        $stmt = $this->connection->prepare("SELECT ("
                . "SELECT COUNT(*) "
                . "FROM  " . self::FORMULAS_BOUGHT_PRODUCTS . " "
                . "WHERE MONTH(date) = MONTH(CURDATE()) AND YEAR(date) = YEAR(CURDATE())"
            . ") + ("
                . "SELECT SUM(quantity) "
                . "FROM " . self::PRODUCTS_BOUGHT . " "
                . "WHERE MONTH(date) = MONTH(CURDATE()) AND YEAR(date) = YEAR(CURDATE())"
            . ") count");
        
        $count = 0;
        $stmt->bindColumn('count', $count, PDO::PARAM_INT);
        $stmt->execute();
        $this->check_fetch_errors($stmt);
        $stmt->fetch();
        return $count;
    }
    
    public function getWeeklyBalanceReloads() : array
    {
        $stmt = $this->connection->prepare("SELECT SUM(" . ReloadManager::FIELD_AMOUNT . ") reload, (DAYOFWEEK(date) + 6 - DAYOFWEEK(CURDATE())) % 7 day FROM " . self::RELOADS . " WHERE " . ReloadManager::FIELD_DATE . " >= (CURDATE() - INTERVAL 1 WEEK ) GROUP BY DATE(" . ReloadManager::FIELD_DATE . ")");
        
        $reloads = array(0., 0., 0., 0., 0., 0., 0.);
        $reload = 0.;
        $day = 0;
        $stmt->bindColumn('reload', $reload);
        $stmt->bindColumn('day', $day);
        $stmt->execute();
        $this->check_fetch_errors($stmt);
        
        while ($stmt->fetch()) $reloads[$day] = floatval($reload);
        return $reloads;
    }
    
    public function getLastMonthlySalesCount() : array
    {
        $formulas = '('
            . 'SELECT '
                . 'COUNT(*) f_sum, '
                . 'MONTH(f.date) f_date, '
                . 'EXTRACT(YEAR_MONTH FROM f.date) f_ym '
            . 'FROM ' . self::FORMULAS_BOUGHT_PRODUCTS . ' f '
            . 'WHERE EXTRACT(YEAR_MONTH FROM f.date) >= EXTRACT(YEAR_MONTH FROM (CURDATE() - INTERVAL 1 YEAR)) '
            . 'GROUP BY EXTRACT(YEAR_MONTH FROM f.date) '
            . ') vformulas';
        $products = '('
            . 'SELECT '
                . 'SUM(p.quantity) p_sum, '
            	. 'MONTh(p.date) p_date, '
                . 'EXTRACT(YEAR_MONTH FROM p.date) p_ym '
            . 'FROM ' . self::PRODUCTS_BOUGHT . ' p '
            . 'WHERE EXTRACT(YEAR_MONTH FROM p.date) >= EXTRACT(YEAR_MONTH FROM (CURDATE() - INTERVAL 1 YEAR)) '
            . 'GROUP BY EXTRACT(YEAR_MONTH FROM p.date) '
            . ') vproducts';
        $sql = 'SELECT '
                . 'IFNULL(f_sum, 0) + IFNULL(p_sum, 0) sum, '
                . '(IFNULL(f_date, p_date) + 11 - MONTH(CURDATE() - INTERVAL 1 YEAR)) % 12 month '
            . 'FROM ( '
                . 'SELECT * '
                . "FROM $formulas "
                . "LEFT JOIN $products "
                . 'ON vformulas.f_ym = vproducts.p_ym '
                . 'UNION '
                . 'SELECT * '
                . "FROM $formulas "
                . "RIGHT JOIN $products "
                . 'ON vformulas.f_ym = vproducts.p_ym '
            . ') sum_table';
        $stmt = $this->connection->prepare($sql);
        
        $sales = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        $sale = 0.;
        $month = 0;
        $stmt->bindColumn('sum', $sale, PDO::PARAM_INT);
        $stmt->bindColumn('month', $month);
        $stmt->execute();
        $this->check_fetch_errors($stmt);
        
        while ($stmt->fetch()) $sales[$month] = $sale;
        return $sales;
    }
}

