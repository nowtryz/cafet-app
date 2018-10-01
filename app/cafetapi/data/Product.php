<?php
namespace cafetapi\data;

use cafetapi\io\DataFetcher;

/**
 *
 * @author Damien
 * @since API 1.0.0 (2018)
 */
class Product extends Payable
{

    private $group;

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
     * @since API 1.0.0 (2018)
     */
    public function __construct(int $id, string $name, float $price, int $group_id, string $image, bool $viewable, Calendar $edit)
    {
        parent::__construct($id, $name, $image, $price, $viewable, $edit);
        $this->group = $group_id;
    }

    /**
     * Returns the $group_id
     *
     * @return int the $group_id
     * @since API 1.0.0 (2018)
     */
    public final function getGroup_id(): int
    {
        return $this->group;
    }

    /**
     * Returns the $group
     *
     * @return ProductGroup the $group
     * @since API 1.0.0 (2018)
     */
    public final function getGroup(): ProductGroup
    {
        return (new DataFetcher())->getProductGroup($this->group);
    }

    /**
     * Returns the $image
     *
     * @return string the $image
     * @since API 1.0.0 (2018)
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
        
        return array_merge(array('type' => get_simple_classname($this)), $vars);
    }
}