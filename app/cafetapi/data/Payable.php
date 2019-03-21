<?php
namespace cafetapi\data;

/**
 * A thing that we can pay for, such as a formula or a product
 *
 * @author Damien
 * @since API 0.1.0 (2018)
 */
abstract class Payable extends JSONParsable implements Data
{

    protected $id;
    protected $name;
    protected $image;
    protected $price;
    protected $viewable;
    protected $edit;

    /**
     * A thing that we can pay for, such as a formula or a product
     *
     * @param int $id
     *            the id
     * @param string $name
     *            the name
     * @param string $image
     *            the image encoded in base64
     * @param float $price
     *            the unity price
     * @param bool $viewable
     *            whether this thing would be displayed or not
     * @param Calendar $edit
     *            the last edition
     * @since API 0.1.0 (2018)
     */
    public function __construct(int $id, string $name, string $image, float $price, bool $viewable, Calendar $edit)
    {
        $this->id = $id;
        $this->name = $name;
        $this->image = $image;
        $this->price = $price;
        $this->viewable = $viewable;
        $this->edit = $edit;
    }

    /**
     * Returns the $id
     *
     * @return int the $id
     * @since API 0.1.0 (2018)
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Returns the $name
     *
     * @return string the $name
     * @since API 0.1.0 (2018)
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the $price
     *
     * @return float the $price
     * @since API 0.1.0 (2018)
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * Returns the $viewable
     *
     * @return bool the $viewable
     * @since API 0.1.0 (2018)
     */
    public function getViewable(): bool
    {
        return $this->viewable;
    }

    /**
     * Returns the $edit
     *
     * @return Calendar the $edit
     * @since API 0.1.0 (2018)
     */
    public function getEdit(): Calendar
    {
        return $this->edit;
    }
}

