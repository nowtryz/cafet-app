<?php
namespace cafetapi\data;

use cafetapi\io\DataFetcher;

/**
 * A group of products
 *
 * @author Damien
 * @since API 1.0.0 (2018)
 */
class ProductGroup extends JSONParsable implements Data
{

    private $id;

    private $name;

    private $displayName;

    /**
     * A group of products
     *
     * @param int $id
     *            the id of the group
     * @param string $name
     *            the name of the group
     * @param string $displayName
     *            the name of the group that would be displayed
     * @since API 1.0.0 (2018)
     */
    public function __construct(int $id, string $name, string $displayName)
    {
        $this->id = $id;
        $this->name = $name;
        $this->displayName = $displayName;
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
     * Returns the $displayName
     *
     * @return string the $displayName
     * @since API 1.0.0 (2018)
     */
    public final function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * Returns viewable products
     *
     * @return array products
     * @since API 1.0.0 (2018)
     */
    public final function getProducts(): array
    {
        return DataFetcher::getInstance()->getGroupProducts($this->id);
    }

    /**
     * Returns viewable and unviewable products
     *
     * @return array products
     * @since API 1.0.0 (2018)
     */
    public final function getAllProducts(): array
    {
        return DataFetcher::getInstance()->getGroupProducts($this->id, true);
    }

    public function __toString(): string
    {
        return $this->parse_JSON(get_object_vars($this));
    }
    
    public function getProperties(): array
    {
        return array_merge(array('type' => get_simple_classname($this)), get_object_vars($this));
    }
}

