<?php
namespace cafetapi\data;

/**
 * A choice in a formula
 *
 * @author Damien
 * @since API 1.0.0 (2018)
 */
class Choice extends JSONParsable implements Data
{
    private $id;
    private $name;
    private $choice;
    private $formula;

    /**
     * A choice in a formula
     *
     * @param int $id
     *            the id of the choice in the database
     * @param string $name
     *            the name of the choice
     * @param Formula $formula_id
     *            the id of the formula
     * @param array $choice
     *            a list of products for this choice
     * @see \cafetapi\io\FormulaManager::getFormulaChoices()
     * @since API 1.0.0 (2018)
     */
    public function __construct(int $id, string $name, int $formula_id, array $choice)
    {
        $this->id = $id;
        $this->name = $name;
        $this->choice = $choice;
        $this->formula = $formula_id;
    }

    /**
     * Returns the $id
     *
     * @return int the $id
     * @since API 1.0.0 (2018)
     */
    public final function getId(): int
    {
        return $this->id;
    }

    /**
     * Returns the $name
     *
     * @return string the $name
     * @since API 1.0.0 (2018)
     */
    public final function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the $choice
     *
     * @return array products of this $choice
     * @since API 1.0.0 (2018)
     */
    public final function getProducts(): array
    {
        return $this->choice;
    }

    /**
     * Returns the $formula
     *
     * @return \cafetapi\data\Formula the $formula
     * @since API 1.0.0 (2018)
     */
    public final function getFormula(): int
    {
        return $this->formula;
    }

    public function __toString(): string
    {
        return $this->parse_JSON(get_object_vars($this));
    }
    
    public function getProperties(): array
    {
        $vars = get_object_vars($this);
        foreach ($vars['choice'] as &$product) $product = $product->getProperties();
        
        return array_merge(['type' => get_simple_classname($this)], $vars);
    }

}

