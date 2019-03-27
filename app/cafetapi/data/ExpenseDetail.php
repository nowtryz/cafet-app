<?php
namespace cafetapi\data;

/**
 * ExpenseDetail stores information about a piece of a bill, it
 * can be acquired by the Expense's methode Expense::getDetails()
 *
 * @see Expense::getDetails()
 * @author Damien
 * @since API 0.1.0 (2018)
 */
abstract class ExpenseDetail extends JSONParsable implements Data
{
    protected $name;
    protected $client_id;
    protected $price;
    protected $quantity;
    protected $date;

    /**
     *
     * ExpenseDetail stores information about a piece of a bill, it
     * can be acquired by the Expense's methode Expense::getDetails()
     *
     * @param string $name
     *            the name of the piece
     * @param int $client_id
     *            the client that pay for it
     * @param float $price
     *            the unity price of the piece
     * @param int $quantity
     *            the quantity bought
     * @param Calendar $date
     *            the date of the transaction
     * @since API 0.1.0 (2018)
     */
    public function __construct(string $name, int $client_id, float $price, int $quantity, Calendar $date)
    {
        $this->name = $name;
        $this->client_id = $client_id;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->date = $date;
    }

    /**
     * Returns the $name
     *
     * @return string the $name
     * @since API 0.1.0 (2018)
     */
    public final function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the $client
     *
     * @return Client the $client
     * @since API 0.1.0 (2018)
     */
    public final function getClient(): int
    {
        return $this->client_id;
    }

    /**
     * Returns the $price
     *
     * @return float the $price
     * @since API 0.1.0 (2018)
     */
    public final function getPrice(): float
    {
        return $this->price;
    }

    /**
     * Returns the $quantity
     *
     * @return int the $quantity
     * @since API 0.1.0 (2018)
     */
    public final function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * Returns the $date
     *
     * @return Calendar the $date
     * @since API 0.1.0 (2018)
     */
    public final function getDate(): Calendar
    {
        return $this->date;
    }
}