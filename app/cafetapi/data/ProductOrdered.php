<?php
namespace cafetapi\data;

/**
 * Model of product for order saving
 *
 * @see \cafetapi\io\DataUpdater::saveOrder()
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
     * @see \cafetapi\io\DataUpdater::saveOrder()
     * @since API 1.0.0 (2018)
     */
    public function __construct(int $id, int $amount)
    {
        parent::__construct($id, $amount);
    }
    
    public function getProperties(): array
    {
        return get_object_vars($this);
    }
}

