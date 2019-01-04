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
        $sales = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        $sale = 0;
        $month = 0;
        
        $formulas = 'SELECT '
                . 'COUNT(*) sum, '
                . '(MONTH(f.date) + 11 - MONTH(CURDATE() - INTERVAL 1 YEAR)) % 12 month '
            . 'FROM ' . self::FORMULAS_BOUGHT_PRODUCTS . ' f '
            . 'WHERE EXTRACT(YEAR_MONTH FROM f.date) >= EXTRACT(YEAR_MONTH FROM (CURDATE() - INTERVAL 1 YEAR)) '
            . 'GROUP BY EXTRACT(YEAR_MONTH FROM f.date)';
        $stmt = $this->connection->prepare($formulas);
        $stmt->bindColumn('sum', $sale, PDO::PARAM_INT);
        $stmt->bindColumn('month', $month);
        $stmt->execute();
        $this->check_fetch_errors($stmt);
        while ($stmt->fetch()) $sales[$month] += $sale;
                
        $products = 'SELECT '
                . 'SUM(p.quantity) sum, '
                . '(MONTH(p.date) + 11 - MONTH(CURDATE() - INTERVAL 1 YEAR)) % 12 month '
            . 'FROM ' . self::PRODUCTS_BOUGHT . ' p '
            . 'WHERE EXTRACT(YEAR_MONTH FROM p.date) >= EXTRACT(YEAR_MONTH FROM (CURDATE() - INTERVAL 1 YEAR)) '
            . 'GROUP BY EXTRACT(YEAR_MONTH FROM p.date)';
        $stmt = $this->connection->prepare($products);
        $stmt->bindColumn('sum', $sale, PDO::PARAM_INT);
        $stmt->bindColumn('month', $month, PDO::PARAM_INT);
        $stmt->execute();
        $this->check_fetch_errors($stmt);
        while ($stmt->fetch()) $sales[$month] += $sale;
        
        return $sales;
    }
    
    public function getYearlySubscription() : array
    {
        $stmt = $this->connection->prepare('SELECT '
            	. 'COUNT(*) count, '
                . '(MONTH(`registration`) + 11 - MONTH(CURDATE() - INTERVAL 1 YEAR)) % 12 month '
            . 'FROM `cafet_users` '
            . 'WHERE `registration` >= (CURDATE() - INTERVAL 1 YEAR) '
            . 'GROUP BY EXTRACT(YEAR_MONTH FROM `registration`)');
        
        $registrations = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        $registration = 0;
        $month = 0;
        
        $stmt->bindColumn('count', $registration, PDO::PARAM_INT);
        $stmt->bindColumn('month', $month, PDO::PARAM_INT);
        $stmt->execute();
        $this->check_fetch_errors($stmt);
        
        while ($stmt->fetch()) $registrations[$month] = $registration;
        return $registrations;
    }
}

