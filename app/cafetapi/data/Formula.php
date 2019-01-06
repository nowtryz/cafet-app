<?php
namespace cafetapi\data;

use cafetapi\io\FormulaManager;

/**
 * A formula with a list of choices
 *
 * @author Damien
 * @since API 1.0.0 (2018)
 */
class Formula extends Payable
{

    /**
     * A formula with a list of choices
     *
     * @param int $id
     *            the formula id
     * @param string $name
     *            the name of the formula
     * @param float $price
     *            the price of the formula
     * @param bool $viewable
     *            whether the formula should be displyed or not
     * @param Calendar $edit
     *            the date of the last edit
     * @since API 1.0.0 (2018)
     */
    public function __construct(int $id, string $name, string $image, float $price, bool $viewable, Calendar $edit)
    {
        parent::__construct($id, $name, $image, $price, $viewable, $edit);
    }

    /**
     * Return the choices
     *
     * @return array the availible choices
     * @since API 1.0.0 (2018)
     */
    public final function getChoices(): array
    {
        return FormulaManager::getInstance()->getFormulaChoices($this->id);
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

