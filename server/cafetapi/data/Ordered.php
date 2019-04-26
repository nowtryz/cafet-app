<?php
namespace cafetapi\data;

/**
 * Abstract class for order saving models
 *
 * @author Damien
 * @since API 0.1.0 (2018)
 */
abstract class Ordered implements Data
{
    private $id;
    private $amount;

    /**
     * Abstract class for order saving models
     *
     * @param int $id
     *            the id of the thing ordered
     * @param int $amount
     *            the amounut if this thing
     * @since API 0.1.0 (2018)
     */
    public function __construct(int $id, int $amount)
    {
        $this->id = $id;
        $this->amount = $amount;
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
     * Returns the $amount
     *
     * @return int the $amount
     * @since API 0.1.0 (2018)
     */
    public function getAmount(): int
    {
        return $this->amount;
    }
}

