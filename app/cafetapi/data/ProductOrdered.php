<?php
namespace cafetapi\data;

/**
 * Model of product for order saving
 *
 * @see \cafetapi\io\ExpenseManager::saveOrder()
 * @author Damien
 * @since API 1.0.0 (2018)
 */
class ProductOrdered extends Ordered implements Data
{

    /**
     * Model of product for order saving
     *
     * @param int $id
     *            product id
     * @param int $amount
     *            quantity of product
     * @see \cafetapi\io\ExpenseManager::saveOrder()
     * @since API 1.0.0 (2018)
     */
    public function __construct(int $id, int $amount)
    {
        parent::__construct($id, $amount);
    }
    
    public function getProperties(): array
    {
        return array_merge(['type' => get_simple_classname($this)], get_object_vars($this));
    }
}

