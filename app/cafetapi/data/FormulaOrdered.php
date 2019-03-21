<?php
namespace cafetapi\data;

/**
 * Model of formula for order saving
 *
 * @see \cafetapi\io\DataUpdater::saveOrder()
 * @author damien
 * @since API 0.1.0 (2018)
 */
class FormulaOrdered extends Ordered implements Data
{

    private $products;

    /**
     *
     * Model of formula for order saving
     *
     * @param int $id
     *            the id of the formula
     * @param int $amount
     *            the quantity of formula
     * @param array $products
     *            an
     * @see \cafetapi\io\FormulaManager::saveOrder()
     * @since API 0.1.0 (2018)
     */
    public function __construct(int $id, int $amount, array $products)
    {
        parent::__construct($id, $amount);
        $this->products = ! is_associative_array($products) ? $products : array();
    }

    /**
     * Returns the $products
     *
     * @return array the $products
     * @since API 0.1.0 (2018)
     */
    public function getProducts(): array
    {
        return $this->products;
    }
    
    public function getProperties(): array
    {
        return array_merge(['type' => get_simple_classname($this)], get_object_vars($this));
    }
}

