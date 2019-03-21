<?php
namespace cafetapi\data;

/**
 * Model of Product that have been bought.
 * <em>Compared to the {@see Product} object,
 * it contains the price and the name of the product while it have been bought</em>
 *
 * @author Damien
 * @see \cafetapi\data\Expense::getDetails()
 * @see \cafetapi\io\DataFetcher::getExpenseDetails()
 * @since API 0.1.0 (2018)
 */
class ProductBought extends ExpenseDetail
{

    private $id;

    private $product;

    /**
     *
     * Model of Product that have been bought. <em>Compared to the {@see Product} object,
     * it contains the price and the name of the product while it have been bought</em>
     *
     * @param int $id
     *            the id of the product purchase in the database
     * @param int $product_id
     *            the id of the product
     * @param string $name
     *            the name of the product
     * @param int $client_id
     *            the id of the client that bought the product
     * @param float $price
     *            the product price at the purchase date
     * @param int $quantity
     *            the quantity bought
     * @param Calendar $date
     *            the date of the transaction
     * @see \cafetapi\data\Expense::getDetails()
     * @see \cafetapi\io\FormulaManager::getExpenseDetails()
     * @since API 0.1.0 (2018)
     */
    public function __construct(int $id, int $product_id, string $name, int $client_id, float $price, int $quantity, Calendar $date)
    {
        parent::__construct($name, $client_id, $price, $quantity, $date);
        $this->product = $product_id;
        $this->id = $id;
    }

    /**
     * Returns the $product
     *
     * @return Product the $product
     * @since API 0.1.0 (2018)
     */
    public final function getProduct(): int
    {
        return $this->product;
    }

    public function __toString(): string
    {
        return $this->parse_JSON(get_object_vars($this));
    }
    
    public function getProperties(): array
    {
        $vars = get_object_vars($this);
        $vars['date'] = $vars['date']->getProperties();
        
        return array_merge(['type' => get_simple_classname($this)], $vars);
    }
}

