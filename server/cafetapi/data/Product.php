<?php
namespace cafetapi\data;

use cafetapi\io\ProductManager;

/**
 *
 * @author Damien
 * @since API 0.1.0 (2018)
 */
class Product extends Payable
{

    private $group;
    private $stock;

    /**
     *
     * @param int $id
     *            the id of the product
     * @param string $name
     *            the name of the product
     * @param float $price
     *            the price of the product
     * @param int $group_id
     *            the id of the group the product depends
     * @param string $image
     *            the image encoded in base64
     * @param bool $viewable
     *            whether or not the product should be visible
     * @param Calendar $edit
     *            the date of the last edition
     * @since API 0.1.0 (2018)
     */
    public function __construct(int $id, string $name, float $price, int $group_id, string $image, bool $viewable, int $stock, Calendar $edit)
    {
        parent::__construct($id, $name, $image, $price, $viewable, $edit);
        $this->group = $group_id;
        $this->stock = $stock;
    }

    /**
     * Returns the $group_id
     *
     * @return int the $group_id
     * @since API 0.1.0 (2018)
     */
    public final function getGroup_id(): int
    {
        return $this->group;
    }

    /**
     * Returns the $group
     *
     * @return ProductGroup the $group
     * @since API 0.1.0 (2018)
     */
    public final function getGroup(): ProductGroup
    {
        return ProductManager::getInstance()->getProductGroup($this->group);
    }

    /**
     * Returns the $image
     *
     * @return string the $image
     * @since API 0.1.0 (2018)
     */
    public final function getImage(): string
    {
        return $this->image;
    }

    public function __toString(): string
    {
        return $this->parse_JSON(get_object_vars($this));
    }
    
    public function getProperties(): array
    {
        $vars = get_object_vars($this);
        $vars['edit'] = $vars['edit']->getProperties();
        
        return array_merge(['type' => get_simple_classname($this)], $vars);
    }
}