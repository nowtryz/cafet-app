<?php
namespace cafetapi\data;

/**
 * Model of Formula that have been bought.
 * <em>Compared to the Formula object,
 * it contains the price and the name of the formula while it have been bought</em>
 *
 * @see \cafetapi\data\Expense::getDetails()
 * @see \cafetapi\io\DataFetcher::getExpenseDetails()
 * @author Damien
 * @since API 1.0.0 (2018)
 */
class FormulaBought extends ExpenseDetail
{

    private $id;

    private $formula_id;

    /**
     * Model of Formula that have been bought.
     * <em>Compared to the Formula object,
     * it contains the price and the name of the formula while it have been bought</em>
     *
     * @param int $id
     *            the id of the formula purchase in the database
     * @param int $formula_id
     *            the id of the formula
     * @param string $name
     *            the name of the formula
     * @param int $client_id
     *            the id of the client that bought the formula
     * @param float $price
     *            the formula price at the purchase date
     * @param int $quantity
     *            the quantity of formula purchases (according to the {@see ExpenseDetail} defenition)
     * @param Calendar $date
     *            the date of the transaction
     * @see \cafetapi\data\Expense::getDetails()
     * @see \cafetapi\io\DataFetcher::getExpenseDetails()
     * @since API 1.0.0 (2018)
     */
    public function __construct(int $id, int $formula_id, string $name, int $client_id, float $price, int $quantity, Calendar $date)
    {
        parent::__construct($name, $client_id, $price, $quantity, $date);
        $this->formula_id = $formula_id;
        $this->id = $id;
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
     * Returns the $formula
     *
     * @return Formula the $formula
     * @since API 1.0.0 (2018)
     */
    public final function getFormula(): int
    {
        return $this->formula_id;
    }

    public function __toString(): string
    {
        return $this->parse_JSON(get_object_vars($this));
    }
}

